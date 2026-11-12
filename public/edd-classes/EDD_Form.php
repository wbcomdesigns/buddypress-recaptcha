<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Easy Digital Downloads Integration
 *
 * Handles CAPTCHA rendering and validation for EDD forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/edd-classes
 */

/**
 * Easy Digital Downloads CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with EDD checkout, login, and registration forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/edd-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class EDD_Form {

	/**
	 * Render CAPTCHA in EDD checkout form
	 *
	 * Adds CAPTCHA HTML to the checkout form.
	 *
	 * @return void
	 */
	public function render_edd_checkout_captcha() {
		// Check if CAPTCHA is enabled for EDD checkout.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_checkout' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div id="edd-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'edd_checkout' );
			echo '</div>';
		}
	}

	/**
	 * Render CAPTCHA in EDD login form
	 *
	 * Adds CAPTCHA HTML to the login form.
	 *
	 * @return void
	 */
	public function render_edd_login_captcha() {
		// Check if CAPTCHA is enabled for EDD login.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div id="edd-login-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'edd_login' );
			echo '</div>';
		}
	}

	/**
	 * Render CAPTCHA in EDD registration form
	 *
	 * Adds CAPTCHA HTML to the registration form.
	 *
	 * @return void
	 */
	public function render_edd_register_captcha() {
		// Check if CAPTCHA is enabled for EDD registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div id="edd-register-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'edd_register' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA for EDD checkout
	 *
	 * Validates the CAPTCHA response during checkout processing.
	 *
	 * @param array $valid_data Valid data from checkout.
	 * @param array $post_data  Posted data.
	 * @return void
	 */
	public function validate_edd_checkout_captcha( $valid_data, $post_data ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// EDD runs these checks twice per purchase: an AJAX pre-check (edd_ajax,
		// never creates the order) and then the real form post. Tokens are
		// single-use, so verify only on the real post that places the order.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- EDD verified its nonce.
		if ( ! empty( $_POST['edd_ajax'] ) ) {
			return;
		}

		if ( ! wbc_verify_captcha( 'edd_checkout' ) ) {
			edd_set_error( 'invalid_captcha', wbc_get_captcha_error_message( 'edd_checkout', 'invalid' ) );
		}
	}

	/**
	 * Validate the EDD login form.
	 *
	 * Runs on edd_user_login before EDD's own handler (priority 10), which
	 * signs the user in without checking edd_get_errors() first, so on failure
	 * that handler is removed for this request and the form shows the error.
	 *
	 * @param array $data Posted login form data.
	 */
	public function validate_edd_login_captcha( $data ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ( wbc_verify_captcha( 'edd_login' ) ) {
			return;
		}
		edd_set_error( 'invalid_captcha', wbc_get_captcha_error_message( 'edd_login', 'invalid' ) );
		remove_action( 'edd_user_login', 'edd_process_login_form' );
	}

	/**
	 * Validate CAPTCHA for EDD registration
	 *
	 * Validates the CAPTCHA response during registration.
	 *
	 * @param array $data Registration data.
	 * @return void
	 */
	public function validate_edd_register_captcha( $data ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// Check if CAPTCHA is enabled for EDD registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'edd_register' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'edd_register', 'invalid' );

				// Add error to EDD's error handling.
				edd_set_error( 'invalid_captcha', $error_message );
			}
		}
	}
}
