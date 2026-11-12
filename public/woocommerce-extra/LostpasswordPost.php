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
		// Resets sent from wp-admin (Users > Send password reset, its bulk action, and
		// Edit User > Send Reset Link) call retrieve_password(), which fires this same
		// hook, but no admin screen renders a CAPTCHA, so verifying here would always
		// block them. A user who can edit users can already reset any password, so
		// exempting them removes no protection. Logged-out requests are still verified.
		// Not is_admin(): front-end AJAX lost-password forms also run through admin-ajax.php.
		if ( current_user_can( 'edit_users' ) ) {
			return $validation_errors;
		}

		// lostpassword_post fires for both the WordPress and the WooCommerce
		// lost-password forms. Pick the context by which handler is running, never
		// by a field the visitor sends: WooCommerce processes its form on
		// `wp_loaded` (WC_Form_Handler::process_lost_password, after checking its
		// own nonce); everything else is the WordPress form. Each form is then held
		// to its own toggle, and the WooCommerce form is no longer checked against
		// the WordPress toggle it never rendered a CAPTCHA for.
		$is_woo_form = doing_action( 'wp_loaded' ) && class_exists( 'WC_Form_Handler' ) && isset( $_POST['wc_reset_password'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verified its nonce before calling retrieve_password().
		$context     = $is_woo_form ? 'woo_lostpassword' : 'wp_lostpassword';

		// verify() returns true when the chosen context is not enabled, so this is
		// a no-op unless the matching "Lost Password Form" toggle is on.
		if ( function_exists( 'wbc_verify_captcha' ) && ! wbc_verify_captcha( $context ) ) {
			$error_message = wbc_get_captcha_error_message( $context, 'invalid' );
			$validation_errors->add( 'captcha_error', $error_message );
		}

		return $validation_errors;
	}
}
