<?php
/**
 * License page template
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

<div class="wrap product-editor-license-page">
	<h1><?php echo esc_html__( 'Product Editor - License Management', 'product-editor' ); ?></h1>

	<?php if ( $is_premium ) : ?>
		<!-- Premium License Active -->
		<div class="pe-license-card pe-premium-active">
			<div class="pe-license-status">
				<span class="dashicons dashicons-yes-alt"></span>
				<h2><?php esc_html_e( 'Premium License Active', 'product-editor' ); ?></h2>
			</div>

			<div class="pe-license-details">
				<p><strong><?php esc_html_e( 'License Key:', 'product-editor' ); ?></strong> <?php echo esc_html( $license_key ); ?></p>
				<p><strong><?php esc_html_e( 'Email:', 'product-editor' ); ?></strong> <?php echo esc_html( $license_email ); ?></p>
			</div>

			<div class="pe-premium-features">
				<h3><?php esc_html_e( 'Premium Features Unlocked:', 'product-editor' ); ?></h3>
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Unlimited product editing', 'product-editor' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Schedule price changes for future dates', 'product-editor' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Unlimited undo history (50 operations)', 'product-editor' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Email notifications for scheduled tasks', 'product-editor' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Priority support', 'product-editor' ); ?></li>
				</ul>
			</div>

			<form method="post" action="">
				<?php wp_nonce_field( 'pe_license_action', 'pe_license_nonce' ); ?>
				<button type="submit" name="pe_deactivate_license" class="button button-secondary">
					<?php esc_html_e( 'Deactivate License', 'product-editor' ); ?>
				</button>
			</form>
		</div>

	<?php else : ?>
		<!-- Free Version / Activate License -->
		<div class="pe-license-card pe-free-version">
			<div class="pe-license-status">
				<span class="dashicons dashicons-info"></span>
				<h2><?php esc_html_e( 'Free Version', 'product-editor' ); ?></h2>
			</div>

			<div class="pe-upgrade-benefits">
				<h3><?php esc_html_e( '🚀 Save hours every week with Premium:', 'product-editor' ); ?></h3>
				<div class="pe-benefits-grid">
					<div class="pe-benefit">
						<span class="dashicons dashicons-products"></span>
						<h4><?php esc_html_e( 'Unlimited Products', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Edit your entire catalog at once - no more batch limits!', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="dashicons dashicons-calendar-alt"></span>
						<h4><?php esc_html_e( 'Schedule Changes', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Set it and forget it - automate Black Friday, sales & promotions', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="dashicons dashicons-backup"></span>
						<h4><?php esc_html_e( 'Peace of Mind', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( '50 undo operations - easily fix any mistake', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="dashicons dashicons-email-alt"></span>
						<h4><?php esc_html_e( 'Stay Informed', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Get email alerts when scheduled tasks complete', 'product-editor' ); ?></p>
					</div>
				</div>

				<div class="pe-pricing">
					<!-- Promo Banner -->
					<div class="pe-promo-banner">
						<span class="pe-promo-gift">🎁</span>
						<div class="pe-promo-content">
							<strong><?php esc_html_e( 'Limited Time Offer!', 'product-editor' ); ?></strong>
							<span><?php esc_html_e( 'Get 15% off with code', 'product-editor' ); ?> <code class="pe-promo-code">PROMO15</code></span>
						</div>
						<div class="pe-promo-timer" id="pe-promo-timer">
							<span class="pe-timer-label"><?php esc_html_e( 'Ends in:', 'product-editor' ); ?></span>
							<span class="pe-timer-countdown" id="pe-countdown"></span>
						</div>
					</div>

					<h3><?php esc_html_e( 'Choose Your Plan', 'product-editor' ); ?></h3>
					<div class="pe-pricing-options">
						<div class="pe-price-option">
							<h4><?php esc_html_e( 'Annual', 'product-editor' ); ?></h4>
							<div class="pe-price">$39<span>/<?php esc_html_e( 'year', 'product-editor' ); ?></span></div>
							<p><?php esc_html_e( '1 site • Updates • Support', 'product-editor' ); ?></p>
						</div>
						<div class="pe-price-option pe-recommended">
							<div class="pe-badge"><?php esc_html_e( 'Most Popular', 'product-editor' ); ?></div>
							<h4><?php esc_html_e( 'Lifetime', 'product-editor' ); ?></h4>
							<div class="pe-price">$119<span><?php esc_html_e( ' once', 'product-editor' ); ?></span></div>
							<p><?php esc_html_e( '1 site • Forever yours • No renewal', 'product-editor' ); ?></p>
						</div>
					</div>

					<a href="<?php echo esc_url( Product_Editor_License::get_upgrade_url() ); ?>" class="button button-primary button-hero" target="_blank">
						<?php esc_html_e( 'Get Premium Now →', 'product-editor' ); ?>
					</a>
					<p class="pe-guarantee"><?php esc_html_e( '30-day money-back guarantee • Secure payment', 'product-editor' ); ?></p>

					<!-- Social Proof -->
					<div class="pe-social-proof">
						<div class="pe-proof-item">
							<span class="dashicons dashicons-star-filled"></span>
							<span class="dashicons dashicons-star-filled"></span>
							<span class="dashicons dashicons-star-filled"></span>
							<span class="dashicons dashicons-star-filled"></span>
							<span class="dashicons dashicons-star-filled"></span>
							<strong>5/5</strong> <?php esc_html_e( 'on WordPress.org', 'product-editor' ); ?>
						</div>
						<div class="pe-proof-item">
							<span class="dashicons dashicons-admin-users"></span>
							<strong>1,000+</strong> <?php esc_html_e( 'active installations', 'product-editor' ); ?>
						</div>
						<div class="pe-proof-item">
							<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
							<?php esc_html_e( 'Trusted by store owners worldwide', 'product-editor' ); ?>
						</div>
					</div>
				</div>
			</div>

			<div class="pe-activate-license">
				<h3><?php esc_html_e( 'Already have a license key?', 'product-editor' ); ?></h3>
				<form method="post" action="">
					<?php wp_nonce_field( 'pe_license_action', 'pe_license_nonce' ); ?>

					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="license_key"><?php esc_html_e( 'License Key', 'product-editor' ); ?></label>
							</th>
							<td>
								<input type="text" id="license_key" name="license_key" class="regular-text" placeholder="PE-XXXX-XXXX-XXXX-XXXX" required />
								<p class="description"><?php esc_html_e( 'Enter your license key in the format: PE-XXXX-XXXX-XXXX-XXXX', 'product-editor' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="license_email"><?php esc_html_e( 'License Email', 'product-editor' ); ?></label>
							</th>
							<td>
								<input type="email" id="license_email" name="license_email" class="regular-text" required />
								<p class="description"><?php esc_html_e( 'The email address used when purchasing the license', 'product-editor' ); ?></p>
							</td>
						</tr>
					</table>

					<p class="submit">
						<button type="submit" name="pe_activate_license" class="button button-primary">
							<?php esc_html_e( 'Activate License', 'product-editor' ); ?>
						</button>
					</p>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<div class="pe-current-limits">
		<h3><?php esc_html_e( 'Current Limits', 'product-editor' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Feature', 'product-editor' ); ?></th>
					<th><?php esc_html_e( 'Free Version', 'product-editor' ); ?></th>
					<th><?php esc_html_e( 'Premium Version', 'product-editor' ); ?></th>
					<th><?php esc_html_e( 'Your Current', 'product-editor' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Products per operation', 'product-editor' ); ?></td>
					<td>50</td>
					<td><?php esc_html_e( 'Unlimited', 'product-editor' ); ?></td>
					<td><strong><?php echo $is_premium ? esc_html__( 'Unlimited', 'product-editor' ) : '50'; ?></strong></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Undo history', 'product-editor' ); ?></td>
					<td>3</td>
					<td>50</td>
					<td><strong><?php echo $is_premium ? '50' : '3'; ?></strong></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Schedule changes', 'product-editor' ); ?></td>
					<td><?php esc_html_e( 'No', 'product-editor' ); ?></td>
					<td><?php esc_html_e( 'Yes', 'product-editor' ); ?></td>
					<td><strong><?php echo $is_premium ? esc_html__( 'Yes', 'product-editor' ) : esc_html__( 'No', 'product-editor' ); ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<style>
.pe-license-card {
	background: #fff;
	border: 1px solid #ccd0d4;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	margin: 20px 0;
	padding: 30px;
}

.pe-license-status {
	display: flex;
	align-items: center;
	margin-bottom: 20px;
}

.pe-license-status .dashicons {
	font-size: 40px;
	width: 40px;
	height: 40px;
	margin-right: 15px;
}

.pe-premium-active .dashicons {
	color: #46b450;
}

.pe-free-version .dashicons {
	color: #00a0d2;
}

.pe-license-details {
	background: #f9f9f9;
	padding: 15px;
	margin-bottom: 20px;
	border-radius: 3px;
}

.pe-premium-features ul {
	list-style: none;
	padding: 0;
}

.pe-premium-features li {
	padding: 8px 0;
	display: flex;
	align-items: center;
}

.pe-premium-features li .dashicons {
	color: #46b450;
	margin-right: 10px;
}

.pe-benefits-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 20px;
	margin: 20px 0;
}

.pe-benefit {
	padding: 20px;
	background: #f9f9f9;
	border-radius: 5px;
	text-align: center;
}

.pe-benefit .dashicons {
	font-size: 40px;
	width: 40px;
	height: 40px;
	color: #2271b1;
	margin-bottom: 10px;
}

.pe-benefit h4 {
	margin: 10px 0;
	font-size: 16px;
}

.pe-pricing {
	margin: 30px 0;
	padding: 20px;
	background: #f0f6fc;
	border-radius: 5px;
	text-align: center;
}

.pe-pricing-options {
	display: flex;
	justify-content: center;
	gap: 20px;
	margin: 20px 0;
}

.pe-price-option {
	padding: 25px;
	background: #fff;
	border: 2px solid #ddd;
	border-radius: 8px;
	min-width: 200px;
	position: relative;
}

.pe-price-option.pe-recommended {
	border-color: #2271b1;
	transform: scale(1.05);
}

.pe-badge {
	position: absolute;
	top: -12px;
	left: 50%;
	transform: translateX(-50%);
	background: #2271b1;
	color: #fff;
	padding: 4px 12px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: bold;
}

.pe-price {
	font-size: 36px;
	font-weight: bold;
	color: #2271b1;
	margin: 15px 0;
}

.pe-price span {
	font-size: 16px;
	color: #666;
}

.pe-activate-license {
	margin-top: 40px;
	padding-top: 30px;
	border-top: 2px solid #ddd;
}

.pe-current-limits {
	margin-top: 30px;
}

.pe-current-limits .widefat th,
.pe-current-limits .widefat td {
	padding: 12px;
}

.pe-guarantee {
	margin-top: 15px;
	color: #666;
	font-size: 13px;
}

.pe-pricing .button-hero {
	font-size: 18px;
	padding: 12px 36px;
	height: auto;
	background: #2271b1;
	border-color: #2271b1;
}

.pe-pricing .button-hero:hover {
	background: #135e96;
	border-color: #135e96;
}

/* Promo Banner */
.pe-promo-banner {
	background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
	color: #fff;
	padding: 15px 20px;
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 15px;
	margin-bottom: 25px;
	flex-wrap: wrap;
	box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
}

.pe-promo-gift {
	font-size: 28px;
}

.pe-promo-content {
	display: flex;
	flex-direction: column;
	gap: 3px;
}

.pe-promo-code {
	background: #fff;
	color: #ee5a24;
	padding: 3px 10px;
	border-radius: 4px;
	font-family: monospace;
	font-weight: bold;
	font-size: 14px;
}

.pe-promo-timer {
	background: rgba(0,0,0,0.2);
	padding: 8px 15px;
	border-radius: 5px;
	display: flex;
	flex-direction: column;
	align-items: center;
}

.pe-timer-label {
	font-size: 11px;
	opacity: 0.9;
}

.pe-timer-countdown {
	font-weight: bold;
	font-size: 16px;
	font-family: monospace;
}

/* Social Proof */
.pe-social-proof {
	display: flex;
	justify-content: center;
	gap: 30px;
	margin: 20px 0;
	flex-wrap: wrap;
}

.pe-proof-item {
	display: flex;
	align-items: center;
	gap: 8px;
	color: #666;
	font-size: 13px;
}

.pe-proof-item .dashicons {
	color: #f0c14b;
}

.pe-proof-item strong {
	color: #333;
}
</style>

<script>
(function() {
	// Timer FOMO - countdown de 48h qui se reset
	function initPromoTimer() {
		var timerEl = document.getElementById('pe-countdown');
		if (!timerEl) return;

		// Récupère ou crée la date de fin (stockée en localStorage)
		var storageKey = 'pe_promo_end_time';
		var endTime = localStorage.getItem(storageKey);
		var now = new Date().getTime();

		// Si pas de timer ou expiré, créer un nouveau (48h)
		if (!endTime || parseInt(endTime) < now) {
			endTime = now + (48 * 60 * 60 * 1000); // 48 heures
			localStorage.setItem(storageKey, endTime);
		}

		function updateTimer() {
			var now = new Date().getTime();
			var distance = parseInt(endTime) - now;

			if (distance < 0) {
				// Reset le timer
				endTime = now + (48 * 60 * 60 * 1000);
				localStorage.setItem(storageKey, endTime);
				distance = parseInt(endTime) - now;
			}

			var hours = Math.floor(distance / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			timerEl.textContent = hours.toString().padStart(2, '0') + ':' +
				minutes.toString().padStart(2, '0') + ':' +
				seconds.toString().padStart(2, '0');
		}

		updateTimer();
		setInterval(updateTimer, 1000);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initPromoTimer);
	} else {
		initPromoTimer();
	}
})();
</script>
