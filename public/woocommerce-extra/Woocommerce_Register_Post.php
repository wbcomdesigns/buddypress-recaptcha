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
class Woocommerce_Register_Post {

	/**
	 * Validate signup captcha
	 *
	 * @param string   $username           Username.
	 * @param string   $email              Email.
	 * @param WP_Error $validation_errors  Validation errors.
	 * @return void
	 */
	public function woocomm_validate_signup_captcha( $username, $email, $validation_errors ) {
		// "Create an account" at checkout (classic or block) is covered by the
		// checkout toggle. No registration widget is shown there, so checking
		// it here would fail every such order.
		if ( did_action( 'woocommerce_checkout_process' ) || did_action( 'woocommerce_store_api_checkout_update_order_from_request' ) ) {
			return;
		}

		if ( ! wbc_verify_captcha( 'woo_register' ) ) {
			$validation_errors->add( 'captcha', wbc_get_captcha_error_message( 'woo_register', 'invalid' ) );
		}
	}
}
