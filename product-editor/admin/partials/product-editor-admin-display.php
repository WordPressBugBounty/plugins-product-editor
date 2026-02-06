<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/speitzako-app/product-editor
 * @since      1.0.0
 *
 * @package    Product-Editor
 * @subpackage Productesc_html_editor/admin/partials
 */

/** @var int $show_variations should show variations in variable products. */
/** @var bool $is_exact_match whether you need to look for an exact match of the search string. */
/** @var int $total count of base products */
/** @var int $num_on_page count products on page */
/** @var int $num_of_pages count of pages */
/** @var string[] $search_select_args values from GET request */
/** @var WC_Product_Simple[]|WC_Product_Variable[]|WC_Product_Grouped[] $products */
/** @var array $visible_columns lists of table columns */
/** @var string $style_visible_columns style for visibility table columns */

?>
<?php
    $nonce = wp_create_nonce( 'pe_changes' );
    // Show welcome notice
    include "product-editor-admin-notice.php";
?>
<style>
    .product-editor-loading {
        position: fixed;
        top: 40%;
        z-index: 1000;
        background: white;
        padding: 0 20px;
        border: 1px solid silver;
    }
    .product-editor-loading__loader {
        width: fit-content;
        font-weight: bold;
        font-family: monospace;
        line-height: 2em;
        font-size: 30px;
        clip-path: inset(0 3ch 0 0);
        animation: l4 1s steps(4) infinite;
    }
    .product-editor-loading__loader:before {
        content:"Loading..."
    }
    @keyframes l4 {to{clip-path: inset(0 -1ch 0 0)}}
    .product-editor .button--plus  img,
    .product-editor .button--minus  img {
        width: 18px;
        height: 18px;
    }
    .product-editor .show-cols__list {
        display: none;
    }
</style>

<style id="table_columns_visibility">
    <?php
    echo $style_visible_columns;
    ?>
</style>

<template id="tmp-edit-single">
	<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
		<input type="hidden" name="action" value="bulk_changes">
		<input type="hidden" id="change_action" name="" value="">
		<input type="hidden" name="ids" value="">
		<div class="pe-edit-box" data-old_value="">

			<div class="btn-container">
				<input type="submit" class="button" value="<?php esc_html_e( 'Save', 'product-editor' ); ?>"/>
				<a class="button discard" tabindex="0"><?php esc_html_e( 'Cancel', 'product-editor' ); ?></a>
			</div>
		</div>
	</form>
</template>
<template id="tmp-add-search-taxonomy">
    <div class="form-group">
        <label><span class="label"></span>&nbsp;
            <input type="hidden" name="" class="taxonomy_selected_name" />
            <input type="text" name="" class="form-control taxonomy_selected_terms" />
        </label>
        <button type="button" class="button button--minus"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) )?>img/minus-icon.svg"/></button>
    </div>
</template>
<?php
$terms_for_taxonomies = [
    'product_cat' => General_Helper::get_terms('product_cat', true),
    'product_tag' => General_Helper::get_terms('product_tag', false),
    'product_visibility' => General_Helper::get_terms('product_visibility', true)
];
foreach ( General_Helper::get_var( 'search_include_taxonomies', [], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY ) as $tax ) {
    if ( !isset($terms_for_taxonomies[$tax]) )
        $terms_for_taxonomies[$tax] = General_Helper::get_terms($tax, true);
}
?>
<script>
    var pe_data = {
        'ajax_url' : '<?php echo admin_url('admin-ajax.php'); ?>',
        'admin_post_url': '<?php echo admin_url('admin-post.php'); ?>',
        'nonce': '<?php echo $nonce; ?>',
        'product_statuses': <?php echo json_encode(General_Helper::get_product_statuses()); ?>,
        'search_taxonomies': {
            list: <?php echo json_encode(General_Helper::get_all_taxonomies()); ?>,
            terms: <?php echo json_encode($terms_for_taxonomies);?>,
            include: [ 'statuses', 'product_tag', 'product_cat'],
            include_from_server: <?php echo json_encode(
                General_Helper::get_var('search_include_taxonomies', [], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY)
            ) ?>,
            exclude: ['product_tag', 'product_cat'],
            exclude_from_server: <?php echo json_encode(
                General_Helper::get_var('search_exclude_taxonomies', [], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY)
            ) ?>
        }
    };

</script>
<div class="wrap product-editor-loading">
    <div class="product-editor-loading__loader"></div>
</div>
<div class="wrap product-editor">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Product Editor', 'product-editor' ); ?></h1>

	<?php
	// Promo banner for free users
	$is_free_user = function_exists( 'pe_fs' ) && ! pe_fs()->can_use_premium_code();
	if ( $is_free_user ) :
	?>
	<div class="pe-editor-promo-banner">
		<div class="pe-promo-left">
			<span class="pe-promo-badge">🎁 <?php esc_html_e( 'Limited Offer', 'product-editor' ); ?></span>
			<span class="pe-promo-text"><?php esc_html_e( 'Get 15% off Premium with code', 'product-editor' ); ?> <code>PROMO15</code></span>
		</div>
		<div class="pe-promo-right">
			<a href="<?php echo esc_url( pe_fs()->get_upgrade_url() ); ?>" class="pe-promo-cta" target="_blank">
				<?php esc_html_e( 'Upgrade Now', 'product-editor' ); ?> →
			</a>
		</div>
	</div>
	<style>
	.pe-editor-promo-banner {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #fff;
		padding: 12px 20px;
		border-radius: 8px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin: 15px 0;
		flex-wrap: wrap;
		gap: 10px;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
	}
	.pe-promo-left {
		display: flex;
		align-items: center;
		gap: 15px;
		flex-wrap: wrap;
	}
	.pe-promo-badge {
		background: rgba(255,255,255,0.2);
		padding: 5px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
	}
	.pe-promo-text {
		font-size: 14px;
	}
	.pe-promo-text code {
		background: #fff;
		color: #764ba2;
		padding: 3px 8px;
		border-radius: 4px;
		font-weight: bold;
		margin-left: 5px;
	}
	.pe-promo-cta {
		background: #fff;
		color: #667eea !important;
		padding: 10px 20px;
		border-radius: 5px;
		text-decoration: none;
		font-weight: 600;
		transition: all 0.3s ease;
		white-space: nowrap;
	}
	.pe-promo-cta:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.2);
		color: #764ba2 !important;
	}
	@media (max-width: 600px) {
		.pe-editor-promo-banner {
			flex-direction: column;
			text-align: center;
		}
		.pe-promo-left {
			flex-direction: column;
		}
	}
	</style>
	<?php endif; ?>

	<div class="ajax-info">
		<div class="inner"></div>
	</div>
	<div class="lds-dual-ring"></div>
    <fieldset class="dynamic_prices">
        <?php
        $is_multiply     = get_option( 'pe_dynamic_is_multiply', false );
        $is_add          = get_option( 'pe_dynamic_is_add', false );
        $multiply_number = get_option( 'pe_dynamic_multiply_value', '' );
        $add_number      = get_option( 'pe_dynamic_add_value', '' );
        $dynamic_tooltip = wp_kses(
                __('Instantly applies change rules to all prices without changing the original price values.<br/>For example, it can be used to change prices relative to the exchange rate.', 'product-editor' ),
                array( 'br' => array() )
        );
        ?>
        <h2 class="dynamic_prices__h2">
            <?php esc_html_e( 'Dynamic price changes (beta)', 'product-editor' ); ?>&nbsp;&nbsp;
            <span class="lbl-toggle"></span>
        </h2>&nbsp;&nbsp;
        <span class="pe-help-tip" data-tooltip="<?php echo $dynamic_tooltip; ?>"></span>
        <form method="post"  class="dynamic_prices__form" style="display: none;">
            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>"/>
            <input type="hidden" name="action" value="pe_change_dynamic_price"/>
            <div class="form-group">
                <label><input type="checkbox" name="is_multiply" <?php echo ( $is_multiply ? 'checked' : '' ); ?>>
                    <?php esc_html_e( 'Multiply prices by value:', 'product-editor' ); ?>
                </label>&nbsp;
                <input type="number"
                       min="0"
                       step="0.000000001"
                       name="multiply_value"
                       placeholder="<?php esc_html_e( 'Number from 0 to +&#8734;', 'product-editor' ); ?>"
                       value="<?php echo esc_attr( $multiply_number ); ?>"
                >
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_add"  <?php echo ($is_add ? 'checked' : ''); ?>>
                    <?php esc_html_e( 'Add a value to prices:', 'product-editor' ); ?>
                </label>&nbsp;
                <input type="number"
                       step="0.01"
                       name="add_value"
                       placeholder="<?php esc_html_e( 'Number from -&#8734; to +&#8734;', 'product-editor' ); ?>"
                       value="<?php echo esc_attr( $add_number ); ?>"
                >
            </div>
            <br/>
            <input type="submit" value="<?php esc_html_e( 'Save', 'product-editor' ); ?>" class="button">
        </form>
    </fieldset>
	<hr/>
    <?php
    $search_tooltip_text = wp_kses(
        __('For variable products, search conditions apply only to their main products, their variations do not participate in search.<br/>For example, there are 2 variable products with color attributes red and blue. In one product, a variation with the attribute red has been created, while the other such variation is not available.<br/>When searching by the taxonomy with the value red, both products with all their variations will be displayed.', 'product-editor' ),
        array( 'br' => array() )
    );
    ?>
    <fieldset>
		<h2 class="search__h2"><?php esc_html_e( 'Search options', 'product-editor' ); ?></h2>
        <span class="pe-help-tip" data-tooltip="<?php echo $search_tooltip_text; ?>"></span>
		<form method="get" action="<?php echo get_option( 'woocommerce_navigation_enabled', 'no' ) === 'no' ? admin_url('edit.php') : admin_url('admin.php')?>">
            <?php if ( get_option( 'woocommerce_navigation_enabled', 'no' ) === 'no' ):?>
			<input type="hidden" name="post_type" value="product"/>
            <?php endif; ?>
			<input type="hidden" name="page" value="product-editor"/>
			<div class="form-group">
				<label><?php esc_html_e( 'Number of items per page:', 'product-editor' ); ?></label>&nbsp;
				<input type="number"
							 min="1"
							 max="1000"
							 name="limit"
							 value="<?php echo esc_attr( General_Helper::get_var( 'limit', 10 ) ); ?>"
				>
				&nbsp;&nbsp;<label><input type="checkbox" value="1" name="show_variations" <?php echo 1 == $show_variations ? 'checked' : ''; ?>>
                    <?php esc_html_e( 'Show variations', 'product-editor' ); ?>
				</label>
			</div>
			<div class="form-group">

			</div>
            <fieldset class="search-fieldset include">
                <legend><?php esc_html_e( 'Products must have:', 'product-editor' ); ?></legend>
                <div class="form-group">
                    <label><?php esc_html_e( 'Category:', 'product-editor' ); ?>&nbsp;
                        <input type="text" name="product_cats" class="form-control selectCats" value="<?php echo esc_attr( $search_select_args['in_product_cats'] ); ?>" >
                    </label>
                    &nbsp;&nbsp;
                    <label><?php esc_html_e( 'Tags:', 'product-editor' ); ?>&nbsp;
                        <input type="text" name="tags" class="form-control selectTags" value="<?php echo esc_attr( $search_select_args['in_tags'] ); ?>" >
                    </label>
                    &nbsp;&nbsp;
                    <label><?php esc_html_e( 'Statuses:', 'product-editor' ); ?>&nbsp;
                        <input type="text" name="statuses" class="form-control selectStatuses" value="<?php echo esc_attr( $search_select_args['status'] ); ?>" >
                    </label>
                </div>
                <div class="form-group" >
                    <label><?php esc_html_e( 'Enable search by taxonomy:', 'product-editor' ); ?>&nbsp;
                        <input type="text" class="form-control selectTaxonomy" data-type="include"/>
                    </label>
                    <button class="button button--plus" type="button" data-type="include" ><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) )?>img/plus-icon.svg"/></button>
                </div>
            </fieldset><br/>
            <fieldset class="search-fieldset exclude">
                <legend><?php esc_html_e( 'Products must have no:', 'product-editor' ); ?></legend>
                <div class="form-group">
                    <label><?php esc_html_e( 'Category:', 'product-editor' ); ?>&nbsp;
                        <input type="text" name="exclude_product_cats" class="form-control selectCats" value="<?php echo esc_attr( $search_select_args['exclude_product_cats'] ); ?>" >
                    </label>
                    &nbsp;&nbsp;
                    <label><?php esc_html_e( 'Tags:', 'product-editor' ); ?>&nbsp;
                        <input type="text" name="exclude_tags" class="form-control selectTags" value="<?php echo esc_attr( $search_select_args['exclude_tags'] ); ?>" >
                    </label>
                </div>
                <div class="form-group" >
                    <label><?php esc_html_e( 'Enable search by taxonomy:', 'product-editor' ); ?>&nbsp;
                        <input type="text" class="form-control selectTaxonomy" data-type="exclude" />
                    </label>
                    <button class="button button--plus" type="button" data-type="exclude" ><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) )?>img/plus-icon.svg"/></button>
                </div>
            </fieldset>
            <div class="form-group">
                <label><?php esc_html_e( 'Name:', 'product-editor' ); ?>&nbsp;
                    <input type="search"
                           name="s"
                           value="<?php echo esc_attr( General_Helper::get_var( 's', '' ) ); ?>"
                    />
                </label>
                &nbsp;&nbsp;
                <label><?php esc_html_e( 'Exact match:', 'product-editor' ); ?>&nbsp;
                    <input type="checkbox"
                           name="exact_match" <?php echo ( $is_exact_match ? 'checked' : '' ); ?>
                    />
                </label>
            </div>
            <br/>
			<input type="submit" value="<?php esc_html_e( 'Search', 'product-editor' ); ?>" class="button">
            <a href="javascript://" class="reset_form button button-link-delete"><?php esc_html_e( 'Reset', 'product-editor' ); ?></a>
		</form>

	</fieldset>
	<br>
	<hr/>
	<?php
	$round_tooltip_text = wp_kses(
	        __('Examples of rounding up:<br/>precision -2 price 21856.234 = 21900<br/>precision -1 price 21856.234 = 21860<br/>precision 0 price 21856.234 = 21857<br/>precision 1 price 21856.234 = 21856.3<br/>precision 2 price 21856.234 = 21856.24', 'product-editor' ),
            array( 'br' => array() )
    );
	?>
	<form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="bulk-changes">
		<input type="hidden" name="action" value="bulk_changes">
		<fieldset>
			<h2><?php esc_html_e( 'Bulk change', 'product-editor' ); ?></h2>
			<div class="form-group">
				<label>
					<span class="title"><?php esc_html_e( 'Price:', 'product-editor' ); ?></span>&nbsp;
                </label>
                <select class="change_regular_price change_to" name="change_regular_price">
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Change to:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Increase existing price by (fixed amount or %):', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Decrease existing price by (fixed amount or %):', 'product-editor' ); ?></option>
                    <option value="4"><?php esc_html_e( 'Multiply existing price by a value', 'product-editor' ); ?></option>
                </select>
                <input type="text" name="_regular_price" pattern="^[0-9\., ]*%?\w{0,3}\s*$" autocomplete="off">
                <select class="round_regular_price round_input" name="round_regular_price">
                    <option value=""><?php esc_html_e( '— Without rounding —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Round up, with the number of decimal places:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Round down, with the number of decimal places:', 'product-editor' ); ?></option>
                </select>
                <input type="number" name="precision_regular_price" class="precision_regular_price precision_input" min="-9" max="9" placeholder="0" autocomplete="off" >
                <span class="pe-help-tip precision_regular_price" data-tooltip="<?php echo $round_tooltip_text; ?>"></span>

			</div>
			<div class="form-group">
				<label>
					<span class="title"><?php esc_html_e( 'Sale price:', 'product-editor' ); ?></span>&nbsp;
                </label>
                <select class="change_sale_price change_to" name="change_sale_price">
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Change to:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Increase existing sale price by (fixed amount or %):', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Decrease existing sale price by (fixed amount or %):', 'product-editor' ); ?></option>
                    <option value="4"><?php esc_html_e( 'Set to regular price decreased by (fixed amount or %):', 'product-editor' ); ?></option>
                </select>
                <input type="text" name="_sale_price" pattern="^[0-9\., ]*%?\w{0,3}\s*$" autocomplete="off">
                <select class="round_sale_price round_input" name="round_sale_price">
                    <option value=""><?php esc_html_e( '— Without rounding —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Round up, with the number of decimal places:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Round down, with the number of decimal places:', 'product-editor' ); ?></option>
                </select>
                <input type="number" name="precision_sale_price" class="precision_sale_price precision_input" min="-9" max="9" placeholder="0" autocomplete="off" >
                <span class="pe-help-tip precision_sale_price" data-tooltip="<?php echo $round_tooltip_text; ?>"></span>
			</div>
			<div class="form-group">
				<label>
					<span class="title"><?php esc_html_e( 'Sale date:', 'product-editor' ); ?></span>&nbsp;
                </label>
                <select class="change_sale_date_from" name="change_date_on_sale_from">
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Change to:', 'product-editor' ); ?></option>
                </select>
				<input type="text" class="date-picker" name="_sale_date_from" value="" placeholder="<?php esc_html_e( 'From&hellip;', 'product-editor' ); ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" autocomplete="off">
			</div>
			<div class="form-group">
				<label>
					<span class="title"><?php esc_html_e( 'Sale end date:', 'product-editor' ); ?></span>&nbsp;
                </label>
                <select class="change_sale_date_to" name="change_date_on_sale_to">
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Change to:', 'product-editor' ); ?></option>
                </select>
				<input type="text" class="date-picker" name="_sale_date_to" value="" placeholder="<?php esc_html_e( 'To&hellip;', 'product-editor' ); ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" autocomplete="off">
			</div>

            <div class="form-group">
                <label>
                    <span class="title"><?php esc_html_e( 'Tags:', 'product-editor' ); ?></span>&nbsp;
                </label>
                <select class="" name="change_tags">
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Add:', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Remove:', 'product-editor' ); ?></option>
                </select>
                <input type="text" name="_tags" class="selectTagsEdit" />
            </div>

            <!-- Quick Discount - PREMIUM FEATURE -->
            <?php $is_premium = Product_Editor_License::can_use_advanced_features(); ?>
            <div class="form-group pe-premium-field pe-quick-discount <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Quick Discount:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <div class="pe-quick-discount-fields">
                    <select name="change_quick_discount" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                        <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                        <option value="1"><?php esc_html_e( 'Apply discount', 'product-editor' ); ?></option>
                    </select>
                    <input type="number" name="_quick_discount_percent" min="1" max="99" step="1" placeholder="<?php esc_attr_e( '% off', 'product-editor' ); ?>" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <span class="pe-discount-separator"><?php esc_html_e( 'from', 'product-editor' ); ?></span>
                    <input type="text" class="date-picker" name="_quick_discount_from" placeholder="<?php esc_attr_e( 'Start date', 'product-editor' ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" autocomplete="off" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <span class="pe-discount-separator"><?php esc_html_e( 'to', 'product-editor' ); ?></span>
                    <input type="text" class="date-picker" name="_quick_discount_to" placeholder="<?php esc_attr_e( 'End date', 'product-editor' ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" autocomplete="off" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                </div>
                <?php if ( ! $is_premium ): ?>
                    <p class="pe-premium-hint"><?php esc_html_e( '🚀 Create scheduled promotions in one click!', 'product-editor' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Stock Management - PREMIUM FEATURE -->
            <?php $is_premium = Product_Editor_License::can_use_advanced_features(); ?>
            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Stock Quantity:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_stock_quantity" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set to:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Increase by:', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Decrease by:', 'product-editor' ); ?></option>
                </select>
                <input type="number" name="_stock_quantity" step="1" autocomplete="off" <?php echo ! $is_premium ? 'disabled placeholder="' . esc_attr__( 'Premium Feature', 'product-editor' ) . '"' : ''; ?>>
            </div>

            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Stock Status:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_stock_status" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set to:', 'product-editor' ); ?></option>
                </select>
                <select name="_stock_status" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value="instock"><?php esc_html_e( 'In stock', 'product-editor' ); ?></option>
                    <option value="outofstock"><?php esc_html_e( 'Out of stock', 'product-editor' ); ?></option>
                    <option value="onbackorder"><?php esc_html_e( 'On backorder', 'product-editor' ); ?></option>
                </select>
            </div>

            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Manage Stock:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_manage_stock" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set to:', 'product-editor' ); ?></option>
                </select>
                <select name="_manage_stock" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value="1"><?php esc_html_e( 'Yes', 'product-editor' ); ?></option>
                    <option value="0"><?php esc_html_e( 'No', 'product-editor' ); ?></option>
                </select>
            </div>

            <!-- Categories - PREMIUM -->
            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Categories:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_categories" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set (replace):', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Add:', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Remove:', 'product-editor' ); ?></option>
                </select>
                <input type="text" name="_categories" class="selectCategoriesEdit" <?php echo ! $is_premium ? 'disabled placeholder="' . esc_attr__( 'Premium Feature', 'product-editor' ) . '"' : ''; ?> />
            </div>

            <!-- SKU - PREMIUM -->
            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'SKU:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_sku" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set to:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Add prefix:', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Add suffix:', 'product-editor' ); ?></option>
                    <option value="4"><?php esc_html_e( 'Find and replace:', 'product-editor' ); ?></option>
                </select>
                <input type="text" name="_sku" placeholder="<?php echo ! $is_premium ? esc_attr__( 'Premium Feature', 'product-editor' ) : esc_attr__( 'New value', 'product-editor' ); ?>" autocomplete="off" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                <input type="text" name="_sku_find" placeholder="<?php esc_attr_e( 'Find (for replace)', 'product-editor' ); ?>" autocomplete="off" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
            </div>

            <!-- Weight - PREMIUM -->
            <div class="form-group pe-premium-field <?php echo ! $is_premium ? 'pe-premium-locked' : ''; ?>">
                <label>
                    <span class="title"><?php esc_html_e( 'Weight:', 'product-editor' ); ?></span>
                    <?php if ( ! $is_premium ): ?>
                        <span class="pe-premium-badge">⭐ PREMIUM</span>
                    <?php endif; ?>
                </label>
                <select class="" name="change_weight" <?php echo ! $is_premium ? 'disabled' : ''; ?>>
                    <option value=""><?php esc_html_e( '— No change —', 'product-editor' ); ?></option>
                    <option value="1"><?php esc_html_e( 'Set to:', 'product-editor' ); ?></option>
                    <option value="2"><?php esc_html_e( 'Increase by:', 'product-editor' ); ?></option>
                    <option value="3"><?php esc_html_e( 'Decrease by:', 'product-editor' ); ?></option>
                </select>
                <input type="number" name="_weight" step="0.01" autocomplete="off" <?php echo ! $is_premium ? 'disabled placeholder="' . esc_attr__( 'Premium Feature', 'product-editor' ) . '"' : ''; ?>>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="not_processing_zero_price_products">
                    <span class="title"><?php esc_html_e( 'Do not change products with zero price', 'product-editor' ); ?></span>&nbsp;
                </label>
            </div>
			<br>
			<div class="form-group">
				<input type="submit" class="button" value="<?php esc_html_e( 'Change Selected', 'product-editor' ); ?>">&nbsp;&nbsp;
                <?php if ( ! empty( $reverse_step ) ): ?>
                    <a href="javascript://" class="do_reverse"
                       data-id="<?php echo esc_attr( $reverse_step['id'] ) ?>">
                        <?php echo esc_html__( 'Undo the change: ', 'product-editor' ) . esc_html($reverse_step['name']); ?>
                    </a>
                <?php else: ?>
                <a href="javascript://" class="do_reverse" style="display: none;"></a>
                <?php endif; ?>
			</div>
		</fieldset>
	</form>
	<br><br>
	<div class="tablenav">
		<?php
		$page_links = paginate_links(
			array(
				'base'      => add_query_arg( 'paged', '%#%' ),
				'format'    => '',
				'prev_text' => __( '&laquo;', 'text-domain' ),
				'next_text' => __( '&raquo;', 'text-domain' ),
				'total'     => $num_of_pages,
				'current'   => sanitize_text_field( General_Helper::get_var( 'paged', 1 ) ),
			)
		);

		if ( $page_links ) {
			$page_links = str_replace( '<a class="', '<a class="button ', $page_links );
			$page_links = str_replace( '<span', '&nbsp;&nbsp;<span', $page_links );
			$page_links = str_replace( 'span>', 'span>&nbsp;&nbsp;', $page_links );
		}
		?>
		<ul class="subsubsub">
			<li>
				<b><?php esc_html_e( 'Total found:', 'product-editor' ); ?> <?php echo esc_html( $total ); ?></b>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
			</li>
			<li><b><?php esc_html_e( 'Items on page:', 'product-editor' ); ?> <?php echo esc_html( $num_on_page ); ?></b></li>
		</ul>
        <div class="show-cols">
            <span class="show-cols__link"><?php esc_html_e( 'Columns', 'product-editor' ); ?></span>
            <ul class="show-cols__list" >
                <?php foreach ($visible_columns as $column_name => $val):?>
                    <li>
                        <label>
                            <input type="checkbox" data-name="<?=$column_name?>" data-class="<?= $val['class'] ?>" <?= $val['visible'] ? 'checked': ''?> />
                            <?= esc_html_e($val['caption']) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
		<div class="tablenav-pages"><?php echo $page_links; ?></div>
	</div>

	<table class="pe-product-table wp-list-table widefat fixed striped table-view-list">
		<thead>
		<tr>
			<th class="check-column-t">
				<label><?php esc_html_e( 'Base', 'product-editor' ); ?><br/><input class="cb-pr-all" type="checkbox"></label>
			</th>
			<th class="check-column-t">
				<label><?php esc_html_e( 'Variations', 'product-editor' ); ?><br/><input class="cb-vr-all" type="checkbox"></label>
			</th>
			<th scope="col" class="td-id manage-column col-id">
				<span>ID</span>
			</th>
            <th scope="col" class="td-sku manage-column">
                <span>SKU</span>
            </th>
			<th scope="col" class="td-name manage-column">
				<span><?php esc_html_e( 'Name', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-status manage-column col-status">
				<span><?php esc_html_e( 'Status', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-type manage-column">
				<span><?php esc_html_e( 'Type', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-price manage-column">
				<span><?php esc_html_e( 'Displayed price', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-regular-price manage-column">
				<span><?php esc_html_e( 'Regular price', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-sale-price manage-column">
				<span><?php esc_html_e( 'Sale price', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-date-on-sale-from manage-column">
				<span><?php esc_html_e( 'Sale date', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-date-on-sale-to manage-column">
				<span><?php esc_html_e( 'Sale end date', 'product-editor' ); ?></span>
			</th>
            <th scope="col" class="td-tags manage-column">
				<span><?php esc_html_e( 'Tags', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-stock-quantity manage-column">
				<span><?php esc_html_e( 'Stock', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-stock-status manage-column">
				<span><?php esc_html_e( 'Stock Status', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-categories manage-column">
				<span><?php esc_html_e( 'Categories', 'product-editor' ); ?></span>
			</th>
			<th scope="col" class="td-weight manage-column">
				<span><?php esc_html_e( 'Weight', 'product-editor' ); ?></span>
			</th>

		</tr>
		</thead>
		<tbody>
		<?php
		require 'product-editor-admin-table-rows.php';
		?>
		</tbody>
	</table>
</div>

<!-- Modal Upgrade CTA -->
<div id="pe-upgrade-modal" class="pe-modal" style="display:none;">
	<div class="pe-modal-overlay"></div>
	<div class="pe-modal-content">
		<button class="pe-modal-close">&times;</button>
		<div class="pe-modal-icon">🚀</div>
		<h2><?php esc_html_e( 'Unlock Unlimited Editing!', 'product-editor' ); ?></h2>
		<p class="pe-modal-subtitle"><?php esc_html_e( 'You\'ve reached the 50 product limit for free users.', 'product-editor' ); ?></p>

		<div class="pe-modal-promo">
			<span class="pe-promo-gift">🎁</span>
			<div>
				<strong><?php esc_html_e( 'Special Offer!', 'product-editor' ); ?></strong><br>
				<?php esc_html_e( 'Get 15% off with code', 'product-editor' ); ?> <code>PROMO15</code>
			</div>
		</div>

		<ul class="pe-modal-features">
			<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Unlimited bulk editing', 'product-editor' ); ?></li>
			<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Schedule price changes', 'product-editor' ); ?></li>
			<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '50 undo operations', 'product-editor' ); ?></li>
			<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Priority support', 'product-editor' ); ?></li>
		</ul>

		<a href="<?php echo esc_url( Product_Editor_License::get_upgrade_url() ); ?>" class="pe-modal-cta" target="_blank">
			<?php esc_html_e( 'Upgrade Now & Save 15%', 'product-editor' ); ?> →
		</a>
		<p class="pe-modal-guarantee"><?php esc_html_e( '30-day money-back guarantee', 'product-editor' ); ?></p>
	</div>
</div>

<!-- Modal Review Request -->
<div id="pe-review-modal" class="pe-modal" style="display:none;">
	<div class="pe-modal-overlay"></div>
	<div class="pe-modal-content pe-review-content">
		<button class="pe-modal-close">&times;</button>
		<div class="pe-modal-icon">💜</div>
		<h2><?php esc_html_e( 'Enjoying Product Editor?', 'product-editor' ); ?></h2>
		<p class="pe-modal-subtitle"><?php esc_html_e( 'Your feedback helps us improve and helps other store owners find us!', 'product-editor' ); ?></p>

		<div class="pe-review-stars" id="pe-review-stars">
			<span class="pe-star" data-rating="1">★</span>
			<span class="pe-star" data-rating="2">★</span>
			<span class="pe-star" data-rating="3">★</span>
			<span class="pe-star" data-rating="4">★</span>
			<span class="pe-star" data-rating="5">★</span>
		</div>
		<p class="pe-review-hint"><?php esc_html_e( 'Click to rate', 'product-editor' ); ?></p>

		<div class="pe-review-actions" style="display:none;">
			<a href="https://wordpress.org/support/plugin/product-editor/reviews/#new-post" class="pe-modal-cta pe-review-go" target="_blank">
				<?php esc_html_e( 'Leave a Review on WordPress.org', 'product-editor' ); ?> →
			</a>
			<button class="pe-review-later"><?php esc_html_e( 'Maybe later', 'product-editor' ); ?></button>
		</div>

		<div class="pe-review-feedback" style="display:none;">
			<p><?php esc_html_e( 'We\'re sorry to hear that! How can we improve?', 'product-editor' ); ?></p>
			<textarea id="pe-feedback-text" placeholder="<?php esc_attr_e( 'Tell us what\'s not working for you...', 'product-editor' ); ?>"></textarea>
			<button class="button button-primary pe-send-feedback"><?php esc_html_e( 'Send Feedback', 'product-editor' ); ?></button>
			<button class="pe-review-later"><?php esc_html_e( 'No thanks', 'product-editor' ); ?></button>
		</div>
	</div>
</div>

<style>
/* Modal Styles */
.pe-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 999999;
	display: flex;
	align-items: center;
	justify-content: center;
}

.pe-modal-overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.7);
	backdrop-filter: blur(3px);
}

.pe-modal-content {
	position: relative;
	background: #fff;
	padding: 40px;
	border-radius: 12px;
	max-width: 480px;
	width: 90%;
	text-align: center;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	animation: peModalSlideIn 0.3s ease;
}

@keyframes peModalSlideIn {
	from {
		opacity: 0;
		transform: translateY(-30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

.pe-modal-close {
	position: absolute;
	top: 15px;
	right: 15px;
	background: none;
	border: none;
	font-size: 24px;
	cursor: pointer;
	color: #999;
	width: 30px;
	height: 30px;
	line-height: 30px;
	padding: 0;
}

.pe-modal-close:hover {
	color: #333;
}

.pe-modal-icon {
	font-size: 50px;
	margin-bottom: 15px;
}

.pe-modal-content h2 {
	margin: 0 0 10px;
	font-size: 24px;
	color: #1d2327;
}

.pe-modal-subtitle {
	color: #666;
	margin: 0 0 20px;
	font-size: 15px;
}

.pe-modal-promo {
	background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
	color: #fff;
	padding: 12px 20px;
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 12px;
	margin-bottom: 20px;
}

.pe-modal-promo .pe-promo-gift {
	font-size: 24px;
}

.pe-modal-promo code {
	background: #fff;
	color: #ee5a24;
	padding: 2px 8px;
	border-radius: 4px;
	font-weight: bold;
}

.pe-modal-features {
	list-style: none;
	padding: 0;
	margin: 0 0 25px;
	text-align: left;
}

.pe-modal-features li {
	padding: 8px 0;
	display: flex;
	align-items: center;
	gap: 10px;
	color: #333;
}

.pe-modal-features .dashicons {
	color: #46b450;
}

.pe-modal-cta {
	display: inline-block;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: #fff !important;
	padding: 15px 35px;
	border-radius: 8px;
	text-decoration: none;
	font-weight: 600;
	font-size: 16px;
	transition: all 0.3s ease;
	box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.pe-modal-cta:hover {
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
	color: #fff;
}

.pe-modal-guarantee {
	color: #888;
	font-size: 13px;
	margin: 15px 0 0;
}

/* Review Modal Specific */
.pe-review-stars {
	font-size: 40px;
	margin: 20px 0 10px;
	cursor: pointer;
}

.pe-star {
	color: #ddd;
	transition: color 0.2s, transform 0.2s;
	display: inline-block;
}

.pe-star:hover,
.pe-star.active {
	color: #f0c14b;
	transform: scale(1.1);
}

.pe-star.hover {
	color: #f0c14b;
}

.pe-review-hint {
	color: #999;
	font-size: 13px;
	margin: 0 0 20px;
}

.pe-review-actions {
	margin-top: 20px;
}

.pe-review-later {
	background: none;
	border: none;
	color: #999;
	cursor: pointer;
	margin-top: 15px;
	font-size: 13px;
	display: block;
	width: 100%;
}

.pe-review-later:hover {
	color: #666;
}

.pe-review-feedback textarea {
	width: 100%;
	min-height: 100px;
	margin: 10px 0;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 5px;
	resize: vertical;
}

.pe-send-feedback {
	margin-top: 10px;
}
</style>

<script>
(function($) {
	'use strict';

	// Upgrade Modal Functions
	window.peShowUpgradeModal = function() {
		$('#pe-upgrade-modal').fadeIn(200);
		$('body').css('overflow', 'hidden');
	};

	window.peHideUpgradeModal = function() {
		$('#pe-upgrade-modal').fadeOut(200);
		$('body').css('overflow', '');
	};

	// Close modal events
	$('#pe-upgrade-modal .pe-modal-close, #pe-upgrade-modal .pe-modal-overlay').on('click', function() {
		peHideUpgradeModal();
	});

	// Review Modal Functions
	var reviewModalShown = localStorage.getItem('pe_review_shown');
	var bulkOpsCount = parseInt(localStorage.getItem('pe_bulk_ops_count') || '0');

	window.peIncrementBulkOps = function() {
		bulkOpsCount++;
		localStorage.setItem('pe_bulk_ops_count', bulkOpsCount);

		// Show review modal after 5 successful operations (and not already shown)
		if (bulkOpsCount >= 5 && !reviewModalShown) {
			setTimeout(function() {
				peShowReviewModal();
			}, 1000);
		}
	};

	window.peShowReviewModal = function() {
		$('#pe-review-modal').fadeIn(200);
		$('body').css('overflow', 'hidden');
	};

	window.peHideReviewModal = function() {
		$('#pe-review-modal').fadeOut(200);
		$('body').css('overflow', '');
	};

	// Review stars interaction
	$('#pe-review-stars .pe-star').on('mouseenter', function() {
		var rating = $(this).data('rating');
		$('#pe-review-stars .pe-star').each(function() {
			if ($(this).data('rating') <= rating) {
				$(this).addClass('hover');
			} else {
				$(this).removeClass('hover');
			}
		});
	});

	$('#pe-review-stars').on('mouseleave', function() {
		$('#pe-review-stars .pe-star').removeClass('hover');
	});

	$('#pe-review-stars .pe-star').on('click', function() {
		var rating = $(this).data('rating');

		// Mark as shown
		localStorage.setItem('pe_review_shown', 'true');
		reviewModalShown = true;

		// Set active stars
		$('#pe-review-stars .pe-star').each(function() {
			if ($(this).data('rating') <= rating) {
				$(this).addClass('active');
			} else {
				$(this).removeClass('active');
			}
		});

		if (rating >= 4) {
			// Good rating - show WordPress.org link
			$('.pe-review-hint').hide();
			$('.pe-review-feedback').hide();
			$('.pe-review-actions').fadeIn();
		} else {
			// Low rating - show feedback form
			$('.pe-review-hint').hide();
			$('.pe-review-actions').hide();
			$('.pe-review-feedback').fadeIn();
		}
	});

	// Close review modal
	$('#pe-review-modal .pe-modal-close, #pe-review-modal .pe-modal-overlay, .pe-review-later').on('click', function() {
		peHideReviewModal();
	});

	// Send feedback
	$('.pe-send-feedback').on('click', function() {
		var feedback = $('#pe-feedback-text').val();
		if (feedback.trim()) {
			// Save feedback locally or send to server
			console.log('Feedback:', feedback);
			alert('<?php echo esc_js( __( 'Thank you for your feedback! We\'ll use it to improve.', 'product-editor' ) ); ?>');
		}
		peHideReviewModal();
	});

	// When user clicks review link
	$('.pe-review-go').on('click', function() {
		localStorage.setItem('pe_review_completed', 'true');
		setTimeout(function() {
			peHideReviewModal();
		}, 500);
	});

})(jQuery);
</script>
