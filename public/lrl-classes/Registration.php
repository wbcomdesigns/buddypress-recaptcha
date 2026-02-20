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
class Registration {

	/**
	 * Render captcha on registration form
	 */
	public function woo_extra_wp_register_form() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'wp_register' );
		}
	}

	/**
	 * Validate registration form captcha
	 *
	 * @param WP_Error $errors               Registration errors.
	 * @param string   $sanitized_user_login Sanitized username.
	 * @param string   $user_email           User email.
	 * @return WP_Error
	 */
	public function woo_extra_validate_extra_register_fields( $errors, $sanitized_user_login, $user_email ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Verify captcha using the service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'wp_register' ) ) {
				$error_message = wbc_get_captcha_error_message( 'wp_register', 'invalid' );
				$errors->add( 'captcha_error', $error_message );
			}
		}

		return $errors;
	}
}
