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

				<!-- Promo Banner -->
				<div class="pe-promo-banner">
					<span class="pe-promo-gift">🎁</span>
					<div class="pe-promo-content">
						<strong><?php esc_html_e( 'Limited Time — 15% off with code', 'product-editor' ); ?> <code class="pe-promo-code">PROMO15</code></strong>
					</div>
					<div class="pe-promo-timer" id="pe-promo-timer">
						<span class="pe-timer-label"><?php esc_html_e( 'Offer ends in:', 'product-editor' ); ?></span>
						<span class="pe-timer-countdown" id="pe-countdown"></span>
					</div>
				</div>

				<h3 style="margin:24px 0 16px"><?php esc_html_e( '🚀 Everything you get with Pro:', 'product-editor' ); ?></h3>
				<div class="pe-benefits-grid">
					<div class="pe-benefit">
						<span class="pe-benefit-icon">⚡</span>
						<h4><?php esc_html_e( 'No product limit', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Edit 10, 100 or 10,000 products at once. No more stopping at 20.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">📝</span>
						<h4><?php esc_html_e( 'Bulk titles & descriptions', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Set, prefix, suffix or find & replace across hundreds of products instantly.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">📸</span>
						<h4><?php esc_html_e( 'Bulk featured image', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Apply or remove a featured image across your entire selection in one click.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">📊</span>
						<h4><?php esc_html_e( 'CSV import & full export', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Import supplier price lists from Excel/CSV. Export your full catalog with all columns.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">🎯</span>
						<h4><?php esc_html_e( 'Conditional price rules', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'IF stock < 10 THEN decrease price by 15%. Automated pricing logic, zero code.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">📅</span>
						<h4><?php esc_html_e( 'Schedule price changes', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Plan Black Friday prices in advance. They go live automatically at the right time.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">↩️</span>
						<h4><?php esc_html_e( '50 undo operations', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Made a mistake on 500 products? One click reverts everything. Free plan has 3.', 'product-editor' ); ?></p>
					</div>
					<div class="pe-benefit">
						<span class="pe-benefit-icon">📋</span>
						<h4><?php esc_html_e( 'Activity log', 'product-editor' ); ?></h4>
						<p><?php esc_html_e( 'Full audit trail of every bulk change — who changed what, when, and how many products.', 'product-editor' ); ?></p>
					</div>
				</div>

				<!-- Mini testimonials -->
				<div class="pe-mini-testimonials">
					<div class="pe-mini-t">
						<span class="pe-mini-stars">★★★★★</span>
						<em>"CSV import saves me 1h every Monday with supplier price updates. ROI in the first week."</em>
						<span class="pe-mini-author">— Thomas L., dropshipper</span>
					</div>
					<div class="pe-mini-t">
						<span class="pe-mini-stars">★★★★★</span>
						<em>"The undo saved me once from a fat-fingered 90% discount on 200 products. Worth it alone."</em>
						<span class="pe-mini-author">— Alex R., sports store</span>
					</div>
				</div>

				<div class="pe-pricing">
					<h3><?php esc_html_e( 'Choose Your Plan', 'product-editor' ); ?></h3>
					<div class="pe-pricing-options">
						<div class="pe-price-option">
							<h4><?php esc_html_e( 'Annual', 'product-editor' ); ?></h4>
							<div class="pe-price">€39.99<span>/<?php esc_html_e( 'year', 'product-editor' ); ?></span></div>
							<p><?php esc_html_e( '1 site · All updates · Priority support', 'product-editor' ); ?></p>
						</div>
						<div class="pe-price-option pe-recommended">
							<div class="pe-badge"><?php esc_html_e( 'Best value', 'product-editor' ); ?></div>
							<h4><?php esc_html_e( 'Lifetime', 'product-editor' ); ?></h4>
							<div class="pe-price">€119<span><?php esc_html_e( ' once', 'product-editor' ); ?></span></div>
							<p><?php esc_html_e( '1 site · Forever · No renewal ever', 'product-editor' ); ?></p>
						</div>
					</div>

					<a href="<?php echo esc_url( Product_Editor_License::get_upgrade_url() ); ?>" class="button button-primary button-hero" target="_blank">
						<?php esc_html_e( 'Upgrade Now & Save 15% →', 'product-editor' ); ?>
					</a>
					<p style="margin:8px 0 0">
						<a href="<?php echo esc_url( Product_Editor_License::get_trial_url() ); ?>" target="_blank" style="color:#2271b1;font-size:13px">
							<?php esc_html_e( 'Or start your 14-day free trial — no credit card required', 'product-editor' ); ?>
						</a>
					</p>
					<p class="pe-guarantee"><?php esc_html_e( '✓ 30-day money-back guarantee  ✓ Secure payment via Freemius', 'product-editor' ); ?></p>

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
					</div>
				</div>
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
					<td>20</td>
					<td><?php esc_html_e( 'Unlimited', 'product-editor' ); ?></td>
					<td><strong><?php echo $is_premium ? esc_html__( 'Unlimited', 'product-editor' ) : '20'; ?></strong></td>
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

.pe-benefit-icon {
	font-size: 28px;
	margin-bottom: 10px;
	display: block;
}

/* Mini testimonials */
.pe-mini-testimonials {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 16px;
	margin: 24px 0;
}
.pe-mini-t {
	background: #fff;
	border: 1px solid #ddd;
	border-left: 4px solid #2271b1;
	border-radius: 6px;
	padding: 14px 16px;
	font-size: 13px;
	color: #444;
	line-height: 1.5;
}
.pe-mini-stars {
	color: #f0c14b;
	font-size: 12px;
	display: block;
	margin-bottom: 6px;
}
.pe-mini-author {
	display: block;
	margin-top: 8px;
	color: #888;
	font-size: 12px;
	font-style: normal;
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
