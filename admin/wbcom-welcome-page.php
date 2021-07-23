<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 *
 * @package Exit if accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
?>

<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
			<h2 class="wbcom-welcome-title"><?php esc_html_e( 'Wbcom Designs - reCaptcha', 'recaptcha-for-woocommerce' ); ?></h2>
			<p class="wbcom-welcome-description"><?php esc_html_e( 'reCaptcha for WooCommerce plugin allows users to check “User is authorised not a robot”.', 'recaptcha-for-woocommerce' ); ?></p>
			<p class="wbcom-welcome-description"><?php esc_html_e( 'This plugin provide to check reCaptch in WP Login page, WP Registration, WP Lost Password, WooCommerce Login, WooCommerce Registration, WooCommerce Lost Password, WooCommerce Order Page, BuddyPress Registration, BBPress Topic And Replay pages', 'recaptcha-for-woocommerce' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-video-link-wrapper">
			</div>

			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'recaptcha-for-woocommerce' ); ?></h3>
				<p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'recaptcha-for-woocommerce' ); ?></p>
				<hr>

				<div class="three-col">

					<div class="col">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'recaptcha-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'We have prepared an extensive guide on Wbcom Designs - reCaptcha to learn all aspects of the plugin. You will find most of your answers here.', 'recaptcha-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( '#' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'recaptcha-for-woocommerce' ); ?></a>
					</div>

					<div class="col">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'recaptcha-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'recaptcha-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'recaptcha-for-woocommerce' ); ?></a>
					</div>

					<div class="col">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'recaptcha-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'recaptcha-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'recaptcha-for-woocommerce' ); ?></a>
					</div>

				</div>

			</div>
		</div>

	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
