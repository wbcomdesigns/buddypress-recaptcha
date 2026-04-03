<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Woocommerce_Lostpassword {

	/**
	 * Function displays the woocommerce lost password captcha.
	 *
	 * @return void
	 */
	public function woo_extra_lostpassword_fields() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'woo_lostpassword' );
		}
	}

	/**
	 * Validate captcha on WooCommerce lost password.
	 * Note: Actual validation is handled by LostpasswordPost class.
	 *
	 * @return void
	 */
	public function validate_lostpassword_captcha() {
		// This method is kept for backward compatibility.
		// Actual validation happens in LostpasswordPost.
	}
}
