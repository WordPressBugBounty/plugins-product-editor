<?php
/**
 * License and Premium Features Management (Freemius Integration)
 *
 * @link       https://github.com/speitzako-app/product-editor
 * @since      2.0.0
 *
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */

/**
 * License and Premium Features Management Class
 *
 * Handles license validation via Freemius SDK
 *
 * @since      2.0.0
 * @package    Product-Editor
 * @subpackage Product_Editor/includes
 */
class Product_Editor_License {

	/**
	 * Free version limits
	 */
	const FREE_PRODUCT_LIMIT = 50;
	const FREE_UNDO_LIMIT = 3;

	/**
	 * Check if the plugin is running in premium mode
	 *
	 * @return bool True if premium features are enabled
	 * @since 2.0.0
	 */
	public static function is_premium() {
		// For development: Force premium mode
		if ( defined( 'PRODUCT_EDITOR_FORCE_PREMIUM' ) && PRODUCT_EDITOR_FORCE_PREMIUM ) {
			return true;
		}

		// Check Freemius license status
		if ( function_exists( 'pe_fs' ) ) {
			// Check if user has an active paid license
			return pe_fs()->is_paying();
		}

		return false;
	}

	/**
	 * Check if user is on trial
	 *
	 * @return bool True if on trial
	 * @since 2.0.0
	 */
	public static function is_trial() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->is_trial();
		}
		return false;
	}

	/**
	 * Check if trial has ended
	 *
	 * @return bool True if trial ended
	 * @since 2.0.0
	 */
	public static function is_trial_ended() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->is_trial_utilized();
		}
		return false;
	}

	/**
	 * Get the license key (not used with Freemius, kept for compatibility)
	 *
	 * @return string|false License key or false
	 * @since 2.0.0
	 */
	public static function get_license_key() {
		if ( function_exists( 'pe_fs' ) && pe_fs()->is_premium() ) {
			$license = pe_fs()->_get_license();
			return $license ? $license->secret_key : false;
		}
		return false;
	}

	/**
	 * Get the license email
	 *
	 * @return string|false License email or false
	 * @since 2.0.0
	 */
	public static function get_license_email() {
		if ( function_exists( 'pe_fs' ) && pe_fs()->is_registered() ) {
			$user = pe_fs()->get_user();
			return $user ? $user->email : false;
		}
		return false;
	}

	/**
	 * Get product limit for current license
	 *
	 * @return int Maximum number of products that can be edited at once
	 * @since 2.0.0
	 */
	public static function get_product_limit() {
		// Premium gets unlimited
		if ( self::is_premium() ) {
			return PHP_INT_MAX;
		}
		return self::FREE_PRODUCT_LIMIT;
	}

	/**
	 * Get undo limit for current license
	 *
	 * @return int Maximum number of undo operations to keep
	 * @since 2.0.0
	 */
	public static function get_undo_limit() {
		// Premium gets 50 undo operations
		if ( self::is_premium() ) {
			return 50;
		}
		return self::FREE_UNDO_LIMIT;
	}

	/**
	 * Check if scheduling feature is available
	 *
	 * @return bool True if scheduling is available
	 * @since 2.0.0
	 */
	public static function can_use_scheduler() {
		return self::is_premium();
	}

	/**
	 * Check if advanced features are available
	 *
	 * @return bool True if advanced features are available
	 * @since 2.0.0
	 */
	public static function can_use_advanced_features() {
		return self::is_premium();
	}

	/**
	 * Get upgrade URL
	 *
	 * @return string URL to upgrade page
	 * @since 2.0.0
	 */
	public static function get_upgrade_url() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->get_upgrade_url();
		}
		// Fallback to pricing page
		return admin_url( 'edit.php?post_type=product&page=product-editor-pricing' );
	}

	/**
	 * Get trial URL
	 *
	 * @return string URL to start trial
	 * @since 2.0.0
	 */
	public static function get_trial_url() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->get_trial_url();
		}
		return self::get_upgrade_url();
	}

	/**
	 * Get account/license management URL
	 *
	 * @return string URL to account page
	 * @since 2.0.0
	 */
	public static function get_account_url() {
		if ( function_exists( 'pe_fs' ) ) {
			return pe_fs()->get_account_url();
		}
		return admin_url( 'edit.php?post_type=product&page=product-editor-account' );
	}

	/**
	 * Display upgrade notice
	 *
	 * @param string $feature The feature that requires premium
	 * @return string HTML for upgrade notice
	 * @since 2.0.0
	 */
	public static function get_upgrade_notice( $feature = '' ) {
		$message = $feature
			? sprintf( __( '🔒 %s is a Premium feature', 'product-editor' ), $feature )
			: __( '🚀 Unlock all features and save hours every week', 'product-editor' );

		$cta_text = __( 'Get Premium →', 'product-editor' );
		$upgrade_url = self::get_upgrade_url();

		return sprintf(
			'<div class="pe-upgrade-notice">
				<span class="pe-upgrade-message">%s</span>
				<a href="%s" class="pe-upgrade-button">%s</a>
			</div>',
			esc_html( $message ),
			esc_url( $upgrade_url ),
			esc_html( $cta_text )
		);
	}

	/**
	 * Get license status info
	 *
	 * @return array License status information
	 * @since 2.0.0
	 */
	public static function get_license_info() {
		$info = array(
			'is_premium' => self::is_premium(),
			'is_free' => ! self::is_premium(),
			'product_limit' => self::get_product_limit(),
			'undo_limit' => self::get_undo_limit(),
			'can_schedule' => self::can_use_scheduler(),
		);

		if ( function_exists( 'pe_fs' ) ) {
			$info['is_registered'] = pe_fs()->is_registered();
			$info['is_anonymous'] = pe_fs()->is_anonymous();

			if ( pe_fs()->is_paying() ) {
				$license = pe_fs()->_get_license();
				if ( $license ) {
					$info['plan_name'] = $license->plan->title;
					$info['expires'] = $license->expiration;
					$info['is_lifetime'] = $license->is_lifetime();
				}
			}
		}

		return $info;
	}

	/**
	 * Legacy compatibility methods
	 * These are kept for backward compatibility but don't do anything with Freemius
	 */

	/**
	 * Activate license (handled by Freemius checkout)
	 */
	public static function activate_license( $license_key, $email ) {
		return array(
			'success' => false,
			'message' => __( 'License activation is now handled by Freemius. Please use the Account menu.', 'product-editor' )
		);
	}

	/**
	 * Deactivate license (handled by Freemius)
	 */
	public static function deactivate_license() {
		return array(
			'success' => false,
			'message' => __( 'License management is now handled by Freemius. Please use the Account menu.', 'product-editor' )
		);
	}
}
