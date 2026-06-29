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
class LostpasswordPost {

	/**
	 * Validate lost password captcha
	 *
	 * @param WP_Error $validation_errors Validation errors.
	 * @return WP_Error
	 */
	public function woocomm_validate_lostpassword_captcha( $validation_errors ) {
		// lostpassword_post fires for BOTH the WordPress core lost-password form
		// and the WooCommerce one. Pick the context by the nonce field the form
		// rendered, so the correct enable-flag is honoured and the single-use
		// CAPTCHA token is verified exactly once. WooCommerce renders
		// 'woo-lostpassword-nonce'; WP core renders 'wp-lostpassword-nonce'.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is verified inside the service verify() for the chosen context.
		$context = isset( $_POST['woo-lostpassword-nonce'] ) ? 'woo_lostpassword' : 'wp_lostpassword';

		// verify() returns true when the chosen context is not enabled, so this is
		// a no-op unless the matching "Lost Password Form" toggle is on.
		if ( function_exists( 'wbc_verify_captcha' ) && ! wbc_verify_captcha( $context ) ) {
			$error_message = wbc_get_captcha_error_message( $context, 'invalid' );
			$validation_errors->add( 'captcha_error', $error_message );
		}

		return $validation_errors;
	}
}
