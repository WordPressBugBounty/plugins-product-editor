<?php
/**
 * Scheduler page template
 *
 * @link       https://github.com/speitzako-app/product-editor
 * @since      2.0.0
 *
 * @package    Product-Editor
 * @subpackage Product_Editor/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap product-editor-scheduler-page">
	<h1><?php echo esc_html__( 'Product Editor - Scheduled Tasks', 'product-editor' ); ?></h1>

	<div class="pe-scheduler-intro">
		<p><?php esc_html_e( 'Schedule bulk product changes to be executed automatically at a specific date and time. Perfect for planning sales, promotions, and seasonal price adjustments.', 'product-editor' ); ?></p>
	</div>

	<div class="pe-scheduler-instructions">
		<h3><?php esc_html_e( 'How to schedule a task:', 'product-editor' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'Go to the main Product Editor page', 'product-editor' ); ?></li>
			<li><?php esc_html_e( 'Select products and configure the changes you want to make', 'product-editor' ); ?></li>
			<li><?php esc_html_e( 'Instead of applying changes immediately, click "Schedule for Later"', 'product-editor' ); ?></li>
			<li><?php esc_html_e( 'Choose the date and time when changes should be applied', 'product-editor' ); ?></li>
			<li><?php esc_html_e( 'Your scheduled tasks will appear below and execute automatically', 'product-editor' ); ?></li>
		</ol>
		<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=product-editor' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Product Editor', 'product-editor' ); ?></a></p>
	</div>

	<!-- Pending Tasks -->
	<div class="pe-tasks-section">
		<h2><?php esc_html_e( 'Pending Tasks', 'product-editor' ); ?> (<?php echo count( $pending_tasks ); ?>)</h2>

		<?php if ( empty( $pending_tasks ) ) : ?>
			<p class="pe-no-tasks"><?php esc_html_e( 'No pending tasks. Schedule a new task from the Product Editor page.', 'product-editor' ); ?></p>
		<?php else : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Task Name', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Scheduled Time', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Products', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Options', 'product-editor' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $pending_tasks as $task ) : ?>
						<tr>
							<td><?php echo esc_html( $task->id ); ?></td>
							<td><strong><?php echo esc_html( $task->name ); ?></strong></td>
							<td>
								<?php
								$scheduled_time = strtotime( $task->scheduled_time );
								$time_diff = $scheduled_time - current_time( 'timestamp' );
								$time_diff_human = human_time_diff( current_time( 'timestamp' ), $scheduled_time );

								echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $scheduled_time ) );
								echo '<br><span class="pe-time-until">';
								if ( $time_diff > 0 ) {
									echo sprintf( esc_html__( 'In %s', 'product-editor' ), $time_diff_human );
								} else {
									echo '<span style="color: #d63638;">' . esc_html__( 'Overdue', 'product-editor' ) . '</span>';
								}
								echo '</span>';
								?>
							</td>
							<td><?php echo esc_html( count( $task->product_ids ) ); ?> <?php esc_html_e( 'products', 'product-editor' ); ?></td>
							<td>
								<?php
								$action_names = array();
								foreach ( $task->actions as $action => $value ) {
									$action_names[] = str_replace( '_', ' ', ucfirst( $action ) );
								}
								echo esc_html( implode( ', ', $action_names ) );
								?>
							</td>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'cancel', 'task_id' => $task->id ) ), 'cancel_task_' . $task->id ) ); ?>"
								   class="button button-small"
								   onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to cancel this task?', 'product-editor' ); ?>');">
									<?php esc_html_e( 'Cancel', 'product-editor' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<!-- Completed Tasks -->
	<div class="pe-tasks-section">
		<h2><?php esc_html_e( 'Completed Tasks', 'product-editor' ); ?> (<?php echo count( $completed_tasks ); ?>)</h2>

		<?php if ( empty( $completed_tasks ) ) : ?>
			<p class="pe-no-tasks"><?php esc_html_e( 'No completed tasks yet.', 'product-editor' ); ?></p>
		<?php else : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Task Name', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Scheduled Time', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Products', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Result', 'product-editor' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $completed_tasks as $task ) : ?>
						<tr>
							<td><?php echo esc_html( $task->id ); ?></td>
							<td><?php echo esc_html( $task->name ); ?></td>
							<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $task->scheduled_time ) ) ); ?></td>
							<td><?php echo esc_html( count( $task->product_ids ) ); ?> <?php esc_html_e( 'products', 'product-editor' ); ?></td>
							<td>
								<?php if ( $task->result ) : ?>
									<span class="pe-status-success">
										✓ <?php echo esc_html( $task->result['success'] ); ?> <?php esc_html_e( 'successful', 'product-editor' ); ?>
										<?php if ( $task->result['failed'] > 0 ) : ?>
											, <?php echo esc_html( $task->result['failed'] ); ?> <?php esc_html_e( 'failed', 'product-editor' ); ?>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<!-- Failed Tasks -->
	<?php if ( ! empty( $failed_tasks ) ) : ?>
		<div class="pe-tasks-section">
			<h2><?php esc_html_e( 'Failed Tasks', 'product-editor' ); ?> (<?php echo count( $failed_tasks ); ?>)</h2>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Task Name', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Scheduled Time', 'product-editor' ); ?></th>
						<th><?php esc_html_e( 'Error', 'product-editor' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $failed_tasks as $task ) : ?>
						<tr>
							<td><?php echo esc_html( $task->id ); ?></td>
							<td><?php echo esc_html( $task->name ); ?></td>
							<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $task->scheduled_time ) ) ); ?></td>
							<td><span class="pe-status-error"><?php echo esc_html( $task->error_message ); ?></span></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

<style>
.pe-scheduler-intro {
	background: #fff;
	border-left: 4px solid #2271b1;
	padding: 15px;
	margin: 20px 0;
}

.pe-scheduler-instructions {
	background: #fff;
	border: 1px solid #ccd0d4;
	padding: 20px;
	margin: 20px 0;
}

.pe-scheduler-instructions ol {
	margin: 15px 0;
	padding-left: 25px;
}

.pe-scheduler-instructions li {
	margin: 8px 0;
}

.pe-tasks-section {
	margin: 30px 0;
	background: #fff;
	padding: 20px;
	border: 1px solid #ccd0d4;
}

.pe-tasks-section h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #ddd;
}

.pe-no-tasks {
	padding: 20px;
	text-align: center;
	color: #666;
	font-style: italic;
}

.pe-time-until {
	font-size: 12px;
	color: #666;
}

.pe-status-success {
	color: #46b450;
	font-weight: 500;
}

.pe-status-error {
	color: #d63638;
	font-weight: 500;
}

.wp-list-table th,
.wp-list-table td {
	padding: 12px 10px;
}
</style>
