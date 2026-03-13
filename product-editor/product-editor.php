<?php
/**
 * @link              https://github.com/speitzako-app/product-editor
 * @since             1.0.0
 * @package           Product-Editor
 * @author            speitzako-app <support@speitzako-app.com>
 *
 * @wordpress-plugin
 * Plugin Name:       Product Editor Pro - Bulk Edit & Schedule WooCommerce Prices
 * Plugin URI:        https://github.com/speitzako-app/product-editor
 * Description:       Bulk edit WooCommerce prices, stock, categories, SKU, titles, descriptions &amp; images. Schedule changes. CSV import/export. Conditional price rules. Activity log. Premium features unlock unlimited power!
 * Version:           2.3.1
 * Author:            speitzako-app
 * Author URI:        https://github.com/speitzako-app
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-editor
 * Domain Path:       /languages
 * WC requires at least: 4.5
 * WC tested up to: 9.5
 * Requires Plugins: woocommerce
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('PRODUCT_EDITOR_VERSION', '2.3.1');
// table for storing old values of changed attributes.
define('PRODUCT_EDITOR_REVERSE_TABLE', 'pe_reverse_steps');

define('PRODUCT_EDITOR_SUPPORT_EMAIL', 'support@speitzako-app.com');
define('PRODUCT_EDITOR_VIDEO_URL', 'https://youtu.be/mSM_ndk2z7A');

// For development: Force premium mode (set to true to test premium features)
// In production, remove this or set to false
define('PRODUCT_EDITOR_FORCE_PREMIUM', false);

require plugin_dir_path(__FILE__) . 'helpers/class-general-helper.php';

// Load license management class
require_once plugin_dir_path(__FILE__) . 'includes/class-product-editor-license.php';

// Load scheduler class for premium features
require_once plugin_dir_path(__FILE__) . 'includes/class-product-editor-scheduler.php';

/**
 * Freemius Integration
 *
 * @since 2.0.0
 */
if ( ! function_exists( 'pe_fs' ) ) {
    // Create a helper function for easy SDK access.
    function pe_fs() {
        global $pe_fs;

        if ( ! isset( $pe_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $pe_fs = fs_dynamic_init( array(
                'id'                  => '22944',
                'slug'                => 'product-editor',
                'premium_slug'        => 'product-editor-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_6fdac2374d2655533b549ffef98b4',
                'is_premium'          => false,
                'is_premium_only'     => false,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => true,
                'has_affiliation'     => 'all',
                'menu'                => array(
                    'slug'           => 'product-editor',
                    'override_exact' => true,
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => array(
                        'slug' => 'edit.php?post_type=product',
                    ),
                ),
                'is_live'             => true,
            ) );
        }

        return $pe_fs;
    }

    // Init Freemius.
    pe_fs();
    // Signal that SDK was initiated.
    do_action( 'pe_fs_loaded' );

    // Freemius uninstall hook
    pe_fs()->add_action( 'after_uninstall', 'pe_fs_uninstall_cleanup' );
}

/**
 * Cleanup function called after Freemius uninstall
 *
 * @since 2.1.0
 */
function pe_fs_uninstall_cleanup() {
    global $wpdb;

    // Delete plugin options
    delete_option( 'product_editor_options' );
    delete_option( 'product_editor_version' );

    // Delete reverse steps table
    $table_name = $wpdb->prefix . PRODUCT_EDITOR_REVERSE_TABLE;
    $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
}

/**
 * Customize the Freemius pricing page — inject social proof above the widget
 * and custom CSS to improve conversion.
 *
 * Filter: fs_templates/pricing.php_{unique_affix}
 * @since 2.3.1
 */
add_filter( 'fs_templates/pricing.php_product_editor', 'pe_customize_freemius_pricing_page' );
function pe_customize_freemius_pricing_page( $html ) {
    $upgrade_url = function_exists( 'pe_fs' ) ? pe_fs()->get_upgrade_url() : '#';
    $trial_url   = function_exists( 'pe_fs' ) ? pe_fs()->get_trial_url() : '#';

    $before = '
<style>
/* ── Product Editor Pro — Pricing page enhancements ── */
#fs_pricing { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
.pe-pricing-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    color: #fff;
    padding: 40px 40px 32px;
    text-align: center;
    margin: -10px -10px 0;
}
.pe-pricing-header h2 {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 10px;
    color: #fff;
}
.pe-pricing-header p {
    color: #94a3b8;
    font-size: 1.05rem;
    margin: 0 0 24px;
}
.pe-ph-promo {
    display: inline-block;
    background: linear-gradient(90deg, #f59e0b, #ef4444);
    color: #fff;
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .95rem;
    margin-bottom: 28px;
}
.pe-ph-promo code {
    background: rgba(255,255,255,.25);
    padding: 2px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 1rem;
    font-weight: 800;
}
.pe-ph-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.1);
}
.pe-ph-stat strong { display: block; font-size: 1.6rem; font-weight: 800; color: #60a5fa; }
.pe-ph-stat span { font-size: .82rem; color: #94a3b8; }

.pe-pricing-testimonials {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 16px;
    padding: 28px 40px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.pe-pt {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #2563eb;
    border-radius: 8px;
    padding: 16px;
    font-size: 13px;
    color: #374151;
    line-height: 1.5;
}
.pe-pt-stars { color: #f59e0b; font-size: 11px; margin-bottom: 6px; }
.pe-pt-author { color: #9ca3af; font-size: 11px; margin-top: 8px; }

.pe-pricing-footer {
    padding: 24px 40px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    text-align: center;
}
.pe-pf-trust {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
    color: #64748b;
    font-size: 13px;
}
.pe-pf-trust span { display: flex; align-items: center; gap: 6px; }
.pe-trial-link {
    display: block;
    margin: 12px auto 0;
    color: #2563eb;
    font-size: 13px;
    text-decoration: underline;
    text-align: center;
}
@media(max-width:800px) {
    .pe-pricing-testimonials { grid-template-columns: 1fr; }
    .pe-ph-stats { gap: 20px; }
}
</style>

<div class="pe-pricing-header">
    <h2>⚡ Product Editor Pro</h2>
    <p>Edit your entire WooCommerce catalog in seconds — not hours.</p>
    <div class="pe-ph-promo">🎁 Limited offer — 15% off with code <code>PROMO15</code></div>
    <div class="pe-ph-stats">
        <div class="pe-ph-stat"><strong>1,000+</strong><span>active stores</span></div>
        <div class="pe-ph-stat"><strong>5/5 ★</strong><span>on WordPress.org</span></div>
        <div class="pe-ph-stat"><strong>14 days</strong><span>free trial</span></div>
        <div class="pe-ph-stat"><strong>30 days</strong><span>money-back</span></div>
    </div>
</div>

<div class="pe-pricing-testimonials">
    <div class="pe-pt">
        <div class="pe-pt-stars">★★★★★</div>
        "CSV import saves me 1h every Monday with supplier prices. ROI in the first week."
        <div class="pe-pt-author">— Thomas L., dropshipper · 500 products</div>
    </div>
    <div class="pe-pt">
        <div class="pe-pt-stars">★★★★★</div>
        "The undo button saved me once from a 90% discount on 200 products by mistake. Worth it alone."
        <div class="pe-pt-author">— Alex R., sports equipment · 600 products</div>
    </div>
    <div class="pe-pt">
        <div class="pe-pt-stars">★★★★★</div>
        "Setup took 2 min. Bulk-renamed 400 products with a prefix. Something I\'d been dreading for weeks."
        <div class="pe-pt-author">— Clara M., home decor · 400 products</div>
    </div>
</div>
';

    $after = '
<div class="pe-pricing-footer">
    <div class="pe-pf-trust">
        <span>✓ 30-day money-back guarantee</span>
        <span>✓ Cancel anytime</span>
        <span>✓ Instant activation after payment</span>
        <span>✓ Secure checkout via Freemius</span>
    </div>
    <a href="' . esc_url( $trial_url ) . '" class="pe-trial-link" target="_blank">
        Or start your 14-day free trial — no credit card required
    </a>
</div>
';

    return $before . $html . $after;
}

function activate_product_editor()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-product-editor-activator.php';
    Product_Editor_Activator::activate();
}

function deactivate_product_editor()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-product-editor-deactivator.php';
    Product_Editor_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_product_editor');
register_deactivation_hook(__FILE__, 'deactivate_product_editor');

// The core plugin class.
require plugin_dir_path(__FILE__) . 'includes/class-product-editor.php';

add_filter( 'plugin_action_links', 'action_links_product_editor', 10, 2 );

function action_links_product_editor( $links_array, $plugin_file_name )
{
	if( strpos( $plugin_file_name, basename(__FILE__) ) ) {
		array_unshift($links_array,
			'<a href="' . esc_url( admin_url( '/edit.php?post_type=product&page=product-editor' ) ) . '">' . __( 'Product Editor', 'product-editor' ) . '</a>'
		);
	}
	return $links_array;
}

/**
 * Declare compatibility with WooCommerce HPOS (High-Performance Order Storage)
 *
 * @since 2.0.0
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_product_editor()
{
    $plugin = new Product_Editor();
    $plugin->run();
}
/**
 * Affiche une notif sur la page native "Tous les produits" de WooCommerce
 */
function pe_advertise_update_on_products_screen() {
    // On ne cible QUE la page "Tous les produits" de WooCommerce
    $screen = get_current_screen();
    if ( ! $screen || 'product' !== $screen->post_type || 'edit' !== $screen->base ) {
        return;
    }

    // Si l'utilisateur a déjà cliqué sur "Masquer", on respecte son choix
    $user_id = get_current_user_id();
    if ( get_user_meta( $user_id, 'pe_dismissed_update_notice_2_2_0', true ) ) {
        return;
    }

    // Check if free user
    $is_free = function_exists( 'pe_fs' ) && ! pe_fs()->can_use_premium_code();

    ?>
    <div class="notice notice-info is-dismissible" id="pe-update-notice">
        <p>
            <strong>⚡ New in Product Editor:</strong> Version 2.3.1 is here!<br>
            Now with <strong>bulk title/description editing</strong>, <strong>featured image bulk set</strong>, <strong>CSV import/export</strong> and <strong>conditional price rules</strong>!
        </p>
        <?php if ( $is_free ) : ?>
        <p style="background: linear-gradient(90deg, #ff6b6b, #ee5a24); color: #fff; padding: 10px 15px; border-radius: 4px; display: inline-block; font-weight: bold;">
            🎁 Get 15% off with the code <span style="background: #fff; color: #ee5a24; padding: 2px 8px; border-radius: 3px; font-family: monospace;">PROMO15</span>
        </p>
        <?php endif; ?>
        <p>
            <a href="<?php echo admin_url( 'edit.php?post_type=product&page=product-editor' ); ?>" class="button button-primary">Try the new editor</a>
            <?php if ( $is_free ) : ?>
                <a href="<?php echo pe_fs()->get_upgrade_url(); ?>" class="button button-secondary" style="color: #d63638;">See Pro features</a>
            <?php endif; ?>
        </p>
    </div>
    <script>
    // Petit script pour gérer la fermeture définitive de la notif
    jQuery(document).ready(function($){
        $('#pe-update-notice').on('click', '.notice-dismiss', function(){
            $.post(ajaxurl, {
                action: 'pe_dismiss_update_notice'
            });
        });
    });
    </script>
    <?php
}
add_action( 'admin_notices', 'pe_advertise_update_on_products_screen' );

// Sauvegarde le fait que l'utilisateur a fermé la pub pour ne plus l'embêter
function pe_dismiss_update_notice_ajax() {
    update_user_meta( get_current_user_id(), 'pe_dismissed_update_notice_2_2_0', true );
    wp_die();
}
add_action( 'wp_ajax_pe_dismiss_update_notice', 'pe_dismiss_update_notice_ajax' );
/**
 * Ajoute des liens d'action dans la liste des plugins
 */
function pe_add_plugin_action_links( $links ) {
    // Link to editor (bold to catch attention)
    $settings_link = '<a href="edit.php?post_type=product&page=product-editor"><strong>' . __( 'Launch Editor', 'product-editor' ) . '</strong></a>';

    // On l'ajoute au début de la liste
    array_unshift( $links, $settings_link );

    // Si version gratuite, on ajoute un lien "Go Pro" rouge
    if ( function_exists( 'pe_fs' ) && ! pe_fs()->can_use_premium_code() ) {
        $premium_link = '<a href="' . pe_fs()->get_upgrade_url() . '" style="color:#d63638;font-weight:bold;">' . __( 'Go Pro', 'product-editor' ) . '</a>';
        $links[] = $premium_link;
    }

    return $links;
}
$plugin_basename = plugin_basename( __FILE__ );
add_filter( 'plugin_action_links_' . $plugin_basename, 'pe_add_plugin_action_links' );
function pe_enqueue_pointer_script_style( $hook_suffix ) {
    $screen = get_current_screen();
    
    // On affiche le pointeur sur la liste des produits
    if ( 'edit.php' != $hook_suffix || 'product' != $screen->post_type )
        return;

    // On vérifie si l'utilisateur l'a déjà vu
    $dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
    if ( in_array( 'pe_new_features_pointer_2_2', $dismissed_pointers ) )
        return;

    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
    
    add_action( 'admin_print_footer_scripts', 'pe_print_pointer_script' );
}
add_action( 'admin_enqueue_scripts', 'pe_enqueue_pointer_script_style' );

function pe_print_pointer_script() {
    $pointer_content = '<h3>🎁 Version 2.3.1 — New Features!</h3>';
    $pointer_content .= '<p>Bulk title/description editing, CSV import/export, featured image bulk set. <strong>Use code PROMO15</strong> for 15% off Pro!</p>';
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var target = 'a[href="edit.php?post_type=product&page=product-editor"]';

        $(target).pointer({
            content: '<?php echo $pointer_content; ?>',
            position: {
                edge: 'left',
                align: 'center'
            },
            close: function() {
                $.post( ajaxurl, {
                    pointer: 'pe_new_features_pointer_2_2',
                    action: 'dismiss-wp-pointer'
                });
            }
        }).pointer('open');
    });
    </script>
    <?php
}
run_product_editor();
