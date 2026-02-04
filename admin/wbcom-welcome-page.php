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
			<p class="wbcom-welcome-description">
				<?php esc_html_e( 'Protect your BuddyPress community from spam and bots with Google reCAPTCHA integration.', 'buddypress-recaptcha' ); ?><br/>
				<?php esc_html_e( 'Add security checks to key areas: WordPress login/registration, BuddyPress signup, WooCommerce checkout, bbPress forums, and comment forms. Keep your community safe without disrupting the user experience.', 'buddypress-recaptcha' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-recaptcha' ); ?></h3>
				<p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-recaptcha' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-recaptcha' ); ?></h3>
						<p><?php esc_html_e( 'Our comprehensive documentation covers everything from initial setup to advanced configurations. Find answers to common questions and best practices for optimal security.', 'buddypress-recaptcha' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/docs/buddypress-recaptcha/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-recaptcha' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-recaptcha' ); ?></h3>
						<p><?php esc_html_e( 'Our dedicated support team is here to help you protect your site. Get expert assistance with setup, troubleshooting, and optimization.', 'buddypress-recaptcha' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-recaptcha' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-recaptcha' ); ?></h3>
						<p><?php esc_html_e( 'Your feedback helps us improve! Share your experience and suggestions to help us make the plugin even better.', 'buddypress-recaptcha' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-recaptcha' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>

	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
