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
 * Description:       Bulk edit WooCommerce prices, stock, categories, and SKU. Schedule changes for future dates. Mass update inventory, tags, and more. Premium features for stock & category management!
 * Version:           2.2.1
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

define('PRODUCT_EDITOR_VERSION', '2.2.1');
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
            <strong>⚡ New in Product Editor:</strong> Version 2.2.1 is here!<br>
            Discover <strong>bulk stock management</strong> and <strong>price scheduling</strong> directly in our editor.
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
    $pointer_content = '<h3>🎁 Version 2.2.1 + Promo !</h3>';
    $pointer_content .= '<p>Nouvelles fonctionnalités disponibles. <strong>Code promo PROMO15</strong> pour 15% de réduction !</p>';
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
