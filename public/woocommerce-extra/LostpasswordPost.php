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
class LostpasswordPost {

	/**
	 * Validate lost password captcha
	 *
	 * @param WP_Error $validation_errors Validation errors.
	 * @return WP_Error
	 */
	public function woocomm_validate_lostpassword_captcha( $validation_errors ) {
		// Verify captcha using the service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'woo_lostpassword' ) ) {
				$error_message = wbc_get_captcha_error_message( 'woo_lostpassword', 'invalid' );
				$validation_errors->add( 'captcha_error', $error_message );
			}
		}

		return $validation_errors;
	}
}