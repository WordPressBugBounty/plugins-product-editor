<?php
/**
 * Scheduler for Planned Product Changes
 *
 * @link       https://github.com/speitzako-app/product-editor
 * @since      2.0.0
 *
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */

/**
 * Scheduler Class for Premium Feature
 *
 * Handles scheduling of product changes for future execution
 *
 * @since      2.0.0
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */
class Product_Editor_Scheduler {

	/**
	 * Database table name for scheduled tasks
	 */
	const TABLE_NAME = 'pe_scheduled_tasks';

	/**
	 * Cron hook name
	 */
	const CRON_HOOK = 'product_editor_execute_scheduled_task';

	/**
	 * Task statuses
	 */
	const STATUS_PENDING = 'pending';
	const STATUS_RUNNING = 'running';
	const STATUS_COMPLETED = 'completed';
	const STATUS_FAILED = 'failed';
	const STATUS_CANCELLED = 'cancelled';

	/**
	 * Initialize the scheduler
	 *
	 * @since 2.0.0
	 */
	public static function init() {
		// Register cron hook
		add_action( self::CRON_HOOK, array( __CLASS__, 'execute_scheduled_task' ), 10, 1 );

		// Check for scheduled tasks every minute (for precise timing)
		if ( ! wp_next_scheduled( 'product_editor_check_scheduled_tasks' ) ) {
			wp_schedule_event( time(), 'hourly', 'product_editor_check_scheduled_tasks' );
		}
		add_action( 'product_editor_check_scheduled_tasks', array( __CLASS__, 'check_and_schedule_tasks' ) );
	}

	/**
	 * Create database table for scheduled tasks
	 *
	 * @since 2.0.0
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			scheduled_time datetime NOT NULL,
			created_time datetime DEFAULT CURRENT_TIMESTAMP,
			status varchar(20) DEFAULT 'pending',
			user_id bigint(20) NOT NULL,
			product_ids longtext NOT NULL,
			actions longtext NOT NULL,
			result longtext,
			error_message text,
			PRIMARY KEY  (id),
			KEY status (status),
			KEY scheduled_time (scheduled_time)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Drop the scheduled tasks table
	 *
	 * @since 2.0.0
	 */
	public static function drop_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

	/**
	 * Schedule a new task
	 *
	 * @param string $name Task name/description
	 * @param string $scheduled_time Scheduled execution time (Y-m-d H:i:s format)
	 * @param array $product_ids Array of product IDs to modify
	 * @param array $actions Array of actions to perform
	 * @return int|false Task ID on success, false on failure
	 * @since 2.0.0
	 */
	public static function schedule_task( $name, $scheduled_time, $product_ids, $actions ) {
		// Check if premium feature is available
		if ( ! Product_Editor_License::can_use_scheduler() ) {
			return false;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Validate scheduled time
		$scheduled_timestamp = strtotime( $scheduled_time );
		if ( $scheduled_timestamp <= current_time( 'timestamp' ) ) {
			return false; // Cannot schedule in the past
		}

		// Insert task
		$result = $wpdb->insert(
			$table_name,
			array(
				'name' => sanitize_text_field( $name ),
				'scheduled_time' => $scheduled_time,
				'user_id' => get_current_user_id(),
				'product_ids' => wp_json_encode( $product_ids ),
				'actions' => wp_json_encode( $actions ),
				'status' => self::STATUS_PENDING
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s' )
		);

		if ( $result ) {
			$task_id = $wpdb->insert_id;

			// Schedule WordPress cron event
			wp_schedule_single_event( $scheduled_timestamp, self::CRON_HOOK, array( $task_id ) );

			return $task_id;
		}

		return false;
	}

	/**
	 * Get a scheduled task by ID
	 *
	 * @param int $task_id Task ID
	 * @return object|null Task object or null
	 * @since 2.0.0
	 */
	public static function get_task( $task_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$task = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			$task_id
		) );

		if ( $task ) {
			$task->product_ids = json_decode( $task->product_ids, true );
			$task->actions = json_decode( $task->actions, true );
			if ( $task->result ) {
				$task->result = json_decode( $task->result, true );
			}
		}

		return $task;
	}

	/**
	 * Get all scheduled tasks
	 *
	 * @param string $status Filter by status (optional)
	 * @param int $limit Limit results (default 100)
	 * @return array Array of task objects
	 * @since 2.0.0
	 */
	public static function get_tasks( $status = '', $limit = 100 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$where = '';
		if ( $status ) {
			$where = $wpdb->prepare( ' WHERE status = %s', $status );
		}

		$tasks = $wpdb->get_results(
			"SELECT * FROM $table_name $where ORDER BY scheduled_time DESC LIMIT " . intval( $limit )
		);

		foreach ( $tasks as $task ) {
			$task->product_ids = json_decode( $task->product_ids, true );
			$task->actions = json_decode( $task->actions, true );
			if ( $task->result ) {
				$task->result = json_decode( $task->result, true );
			}
		}

		return $tasks;
	}

	/**
	 * Cancel a scheduled task
	 *
	 * @param int $task_id Task ID
	 * @return bool Success status
	 * @since 2.0.0
	 */
	public static function cancel_task( $task_id ) {
		$task = self::get_task( $task_id );
		if ( ! $task || $task->status !== self::STATUS_PENDING ) {
			return false;
		}

		// Remove cron event
		$scheduled_timestamp = strtotime( $task->scheduled_time );
		wp_unschedule_event( $scheduled_timestamp, self::CRON_HOOK, array( $task_id ) );

		// Update task status
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->update(
			$table_name,
			array( 'status' => self::STATUS_CANCELLED ),
			array( 'id' => $task_id ),
			array( '%s' ),
			array( '%d' )
		) !== false;
	}

	/**
	 * Execute a scheduled task
	 *
	 * @param int $task_id Task ID
	 * @since 2.0.0
	 */
	public static function execute_scheduled_task( $task_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$task = self::get_task( $task_id );
		if ( ! $task || $task->status !== self::STATUS_PENDING ) {
			return;
		}

		// Update status to running
		$wpdb->update(
			$table_name,
			array( 'status' => self::STATUS_RUNNING ),
			array( 'id' => $task_id ),
			array( '%s' ),
			array( '%d' )
		);

		try {
			// Execute the task
			$result = self::execute_bulk_changes( $task->product_ids, $task->actions );

			// Update task as completed
			$wpdb->update(
				$table_name,
				array(
					'status' => self::STATUS_COMPLETED,
					'result' => wp_json_encode( $result )
				),
				array( 'id' => $task_id ),
				array( '%s', '%s' ),
				array( '%d' )
			);

			// Send notification to user (optional)
			self::send_task_notification( $task_id, true );

		} catch ( Exception $e ) {
			// Update task as failed
			$wpdb->update(
				$table_name,
				array(
					'status' => self::STATUS_FAILED,
					'error_message' => $e->getMessage()
				),
				array( 'id' => $task_id ),
				array( '%s', '%s' ),
				array( '%d' )
			);

			// Send failure notification
			self::send_task_notification( $task_id, false, $e->getMessage() );
		}
	}

	/**
	 * Execute bulk changes on products
	 *
	 * @param array $product_ids Array of product IDs
	 * @param array $actions Actions to perform
	 * @return array Result of the operation
	 * @since 2.0.0
	 */
	private static function execute_bulk_changes( $product_ids, $actions ) {
		$wpdb->query( 'START TRANSACTION' );

		$results = array(
			'success' => 0,
			'failed' => 0,
			'products' => array()
		);

		foreach ( $product_ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				$results['failed']++;
				continue;
			}

			try {
				// Apply each action
				foreach ( $actions as $action_name => $action_data ) {
					self::apply_action( $product, $action_name, $action_data );
				}

				$product->save();
				$results['success']++;
				$results['products'][] = $id;

			} catch ( Exception $e ) {
				$results['failed']++;
			}
		}

		$wpdb->query( 'COMMIT' );
		WC_Cache_Helper::get_transient_version( 'product', true );

		return $results;
	}

	/**
	 * Apply a single action to a product
	 *
	 * @param WC_Product $product Product object
	 * @param string $action_name Action name
	 * @param mixed $action_data Action data
	 * @since 2.0.0
	 */
	private static function apply_action( $product, $action_name, $action_data ) {
		switch ( $action_name ) {
			case 'change_regular_price':
				if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
					$product->set_regular_price( $action_data['value'] );
				}
				break;

			case 'change_sale_price':
				if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
					$product->set_sale_price( $action_data['value'] );
				}
				break;

			case 'change_date_on_sale_from':
				if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
					$product->set_date_on_sale_from( $action_data['value'] );
				}
				break;

			case 'change_date_on_sale_to':
				if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
					$product->set_date_on_sale_to( $action_data['value'] );
				}
				break;

			case 'change_tags':
				if ( ! is_a( $product, 'WC_Product_Variation' ) ) {
					$product->set_tag_ids( $action_data['value'] );
				}
				break;
		}
	}

	/**
	 * Send notification to user when task completes
	 *
	 * @param int $task_id Task ID
	 * @param bool $success Whether task succeeded
	 * @param string $error_message Error message if failed
	 * @since 2.0.0
	 */
	private static function send_task_notification( $task_id, $success, $error_message = '' ) {
		$task = self::get_task( $task_id );
		if ( ! $task ) {
			return;
		}

		$user = get_userdata( $task->user_id );
		if ( ! $user || ! $user->user_email ) {
			return;
		}

		$subject = $success
			? sprintf( __( '[Product Editor] Scheduled task "%s" completed successfully', 'product-editor' ), $task->name )
			: sprintf( __( '[Product Editor] Scheduled task "%s" failed', 'product-editor' ), $task->name );

		$message = $success
			? sprintf(
				__( 'Your scheduled task "%s" has been completed successfully at %s.', 'product-editor' ),
				$task->name,
				current_time( 'mysql' )
			)
			: sprintf(
				__( 'Your scheduled task "%s" failed with error: %s', 'product-editor' ),
				$task->name,
				$error_message
			);

		wp_mail( $user->user_email, $subject, $message );
	}

	/**
	 * Check for tasks that need to be scheduled
	 * This runs periodically to catch any tasks that might have been missed
	 *
	 * @since 2.0.0
	 */
	public static function check_and_schedule_tasks() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Find pending tasks that should have been executed already
		$tasks = $wpdb->get_results(
			"SELECT id, scheduled_time FROM $table_name
			WHERE status = 'pending'
			AND scheduled_time <= NOW()
			LIMIT 10"
		);

		foreach ( $tasks as $task ) {
			self::execute_scheduled_task( $task->id );
		}
	}

	/**
	 * Delete old completed tasks
	 *
	 * @param int $days_to_keep Number of days to keep completed tasks
	 * @since 2.0.0
	 */
	public static function cleanup_old_tasks( $days_to_keep = 30 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM $table_name
			WHERE status IN ('completed', 'failed', 'cancelled')
			AND created_time < DATE_SUB(NOW(), INTERVAL %d DAY)",
			$days_to_keep
		) );
	}
}
