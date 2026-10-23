<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/speitzako-app/product-editor
 * @since      1.0.0
 *
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */
class Product_Editor_Activator {

	/**
   * Activate the plugin.
   * Create tables for storing old values of changed attributes and scheduled tasks
	 * @since    1.0.0
	 */
	public static function activate() {
    $version = get_option( 'PRODUCT_EDITOR_VERSION', PRODUCT_EDITOR_VERSION );
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create reverse table for undo functionality
    $table_name = $wpdb->prefix . PRODUCT_EDITOR_REVERSE_TABLE;

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name varchar(191),
		data longtext,
		UNIQUE KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // Create scheduled tasks table (Premium feature)
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-product-editor-scheduler.php';
    Product_Editor_Scheduler::create_table();

    // Create activity log table (Premium feature)
    $log_table = $wpdb->prefix . 'pe_activity_log';
    $sql_log   = "CREATE TABLE $log_table (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		user_id bigint(20) NOT NULL DEFAULT 0,
		user_login varchar(60),
		action_summary text,
		products_count int(11) DEFAULT 0,
		products_ids longtext,
		UNIQUE KEY id (id),
		KEY time (time),
		KEY user_id (user_id)
	) $charset_collate;";
    dbDelta( $sql_log );

    update_option('PRODUCT_EDITOR_VERSION', $version, false);
	}

}
