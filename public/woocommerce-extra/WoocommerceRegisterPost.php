<?php
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
class WoocommerceRegisterPost {

	/**
	 * Validate signup captcha
	 *
	 * @param string   $username           Username
	 * @param string   $email              Email
	 * @param WP_Error $validation_errors  Validation errors
	 * @return void
	 */
	public function woocomm_validate_signup_captcha( $username, $email, $validation_errors ) {
		// Use verification helper
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'woo_register' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'woo_register', 'invalid' );
				$validation_errors->add( 'captcha', $error_msg );
			}
		}
	}
}