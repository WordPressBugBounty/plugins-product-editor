<?php
/**
 * CSV Import page — Premium feature
 *
 * @since 2.3.0
 * @package Product-Editor
 */

$nonce = wp_create_nonce( 'pe_changes' );
?>
<style>
.pe-csv-import { max-width: 800px; }
.pe-csv-import .notice { margin: 10px 0; }
.pe-csv-upload-area { border: 2px dashed #c3c4c7; border-radius: 8px; padding: 30px; text-align: center; margin: 20px 0; background: #fafafa; }
.pe-csv-upload-area input[type=file] { margin: 10px 0; }
.pe-csv-columns-info { background: #f0f6fc; border-left: 4px solid #2271b1; padding: 12px 16px; margin: 15px 0; border-radius: 0 4px 4px 0; }
.pe-csv-columns-info code { background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid #ddd; margin: 2px; display: inline-block; }
#pe-csv-results { display: none; margin-top: 20px; }
#pe-csv-progress { display: none; margin: 10px 0; }
</style>

<div class="wrap product-editor pe-csv-import">
    <h1><?php esc_html_e( 'Import Products from CSV', 'product-editor' ); ?></h1>

    <div class="pe-csv-columns-info">
        <strong><?php esc_html_e( 'Supported columns:', 'product-editor' ); ?></strong><br>
        <code>id</code> <?php esc_html_e( 'or', 'product-editor' ); ?> <code>sku</code> <?php esc_html_e( '(required to identify products)', 'product-editor' ); ?><br>
        <code>name</code> &nbsp; <code>regular_price</code> &nbsp; <code>sale_price</code> &nbsp;
        <code>stock_quantity</code> &nbsp; <code>short_description</code> &nbsp; <code>weight</code>
        <br><br>
        <?php esc_html_e( 'Only columns present in the CSV will be updated. Unknown columns are ignored.', 'product-editor' ); ?>
    </div>

    <div id="pe-csv-results"></div>

    <form id="pe-csv-import-form" method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="pe_csv_import_apply">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

        <div class="pe-csv-upload-area">
            <p><strong><?php esc_html_e( 'Select your CSV file', 'product-editor' ); ?></strong></p>
            <input type="file" name="csv_file" id="pe-csv-file" accept=".csv,text/csv" required>
            <p class="description"><?php esc_html_e( 'UTF-8 encoded CSV with comma separator. First row = column headers.', 'product-editor' ); ?></p>
        </div>

        <div id="pe-csv-progress" class="notice notice-info">
            <p><?php esc_html_e( 'Processing…', 'product-editor' ); ?></p>
        </div>

        <p>
            <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import CSV', 'product-editor' ); ?>">
            &nbsp;
            <span class="description"><?php esc_html_e( 'All changes are reversible using the Undo button on the main editor page.', 'product-editor' ); ?></span>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#pe-csv-import-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('#pe-csv-progress').show();
        $('#pe-csv-results').hide();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $('#pe-csv-progress').hide();
                var cls = resp.updated > 0 ? 'notice-success' : 'notice-warning';
                var html = '<div class="notice ' + cls + '"><p><strong>' + resp.message + '</strong></p>';
                if (resp.errors && resp.errors.length > 0) {
                    html += '<ul>';
                    resp.errors.forEach(function(err) { html += '<li>' + err + '</li>'; });
                    html += '</ul>';
                }
                html += '</div>';
                $('#pe-csv-results').html(html).show();
            },
            error: function(xhr) {
                $('#pe-csv-progress').hide();
                var msg = '<?php echo esc_js( __( 'Import failed.', 'product-editor' ) ); ?>';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                $('#pe-csv-results').html('<div class="notice notice-error"><p>' + msg + '</p></div>').show();
            }
        });
    });
});
</script>
