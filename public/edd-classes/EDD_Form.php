<?php
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
		// Check if CAPTCHA is enabled for EDD checkout
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_checkout' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager
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
		// Check if CAPTCHA is enabled for EDD login
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager
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
		// Check if CAPTCHA is enabled for EDD registration
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager
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
	public function validate_edd_checkout_captcha( $valid_data, $post_data ) {
		// Check if CAPTCHA is enabled for EDD checkout
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_checkout' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'edd_checkout' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'edd_checkout', 'invalid' );

				// Add error to EDD's error handling
				edd_set_error( 'invalid_captcha', $error_message );
			}
		}
	}

	/**
	 * Validate CAPTCHA for EDD login
	 *
	 * Validates the CAPTCHA response during login.
	 *
	 * @param array $data Login data.
	 * @return void
	 */
	public function validate_edd_login_captcha( $data ) {
		// Check if CAPTCHA is enabled for EDD login
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'edd_login' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'edd_login', 'invalid' );

				// Add error to EDD's error handling
				edd_set_error( 'invalid_captcha', $error_message );
			}
		}
	}

	/**
	 * Validate CAPTCHA for EDD registration
	 *
	 * Validates the CAPTCHA response during registration.
	 *
	 * @param array $data Registration data.
	 * @return void
	 */
	public function validate_edd_register_captcha( $data ) {
		// Check if CAPTCHA is enabled for EDD registration
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_edd_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'edd_register' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'edd_register', 'invalid' );

				// Add error to EDD's error handling
				edd_set_error( 'invalid_captcha', $error_message );
			}
		}
	}
}
