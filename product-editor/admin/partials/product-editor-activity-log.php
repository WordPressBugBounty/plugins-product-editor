<?php
/**
 * Activity Log page — Premium feature
 *
 * @since 2.3.0
 * @package Product-Editor
 */

global $wpdb;
$table = $wpdb->prefix . 'pe_activity_log';

// Pagination
$per_page    = 50;
$current_page = max( 1, (int) General_Helper::get_var( 'paged', 1 ) );
$offset      = ( $current_page - 1 ) * $per_page;

$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
$logs  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset ), ARRAY_A );

$num_pages = ceil( $total / $per_page );
?>
<div class="wrap product-editor">
    <h1><?php esc_html_e( 'Activity Log', 'product-editor' ); ?></h1>
    <p><?php esc_html_e( 'Full history of all bulk operations performed through Product Editor.', 'product-editor' ); ?></p>

    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php
            echo paginate_links( array(
                'base'      => add_query_arg( 'paged', '%#%' ),
                'total'     => $num_pages,
                'current'   => $current_page,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
            <span class="displaying-num"><?php printf( __( '%d entries', 'product-editor' ), $total ); ?></span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th style="width:160px"><?php esc_html_e( 'Date & Time', 'product-editor' ); ?></th>
                <th style="width:120px"><?php esc_html_e( 'User', 'product-editor' ); ?></th>
                <th><?php esc_html_e( 'Action(s)', 'product-editor' ); ?></th>
                <th style="width:80px"><?php esc_html_e( 'Products', 'product-editor' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ( $logs ) : ?>
            <?php foreach ( $logs as $log ) : ?>
            <tr>
                <td><?php echo esc_html( $log['time'] ); ?></td>
                <td><?php echo esc_html( $log['user_login'] ); ?></td>
                <td><?php echo esc_html( $log['action_summary'] ); ?></td>
                <td style="text-align:center"><?php echo esc_html( $log['products_count'] ); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" style="text-align:center;padding:20px">
                    <?php esc_html_e( 'No activity recorded yet. Bulk operations will appear here.', 'product-editor' ); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
