<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * MemberPress Integration
 *
 * Handles CAPTCHA rendering and validation for MemberPress forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/memberpress-classes
 */

/**
 * MemberPress CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with MemberPress login and registration forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/memberpress-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class MemberPress_Form {

	/**
	 * Render CAPTCHA in MemberPress login form
	 *
	 * Adds CAPTCHA HTML to the login form.
	 *
	 * @return void
	 */
	public function render_memberpress_login_captcha() {
		// Check if CAPTCHA is enabled for MemberPress login.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_memberpress_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="mepr-login-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'memberpress_login' );
			echo '</div>';
		}
	}

	/**
	 * Render CAPTCHA in MemberPress registration form
	 *
	 * Adds CAPTCHA HTML to the registration form.
	 *
	 * @return void
	 */
	public function render_memberpress_register_captcha() {
		// Check if CAPTCHA is enabled for MemberPress registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_memberpress_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="mepr-signup-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'memberpress_register' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA for MemberPress login
	 *
	 * Validates the CAPTCHA response during login.
	 *
	 * @param WP_Error $errors WP_Error object.
	 * @return WP_Error Modified errors object.
	 */
	public function validate_memberpress_login_captcha( $errors ) {
		// Check if CAPTCHA is enabled for MemberPress login.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_memberpress_login' );
		if ( 'yes' !== $is_enabled ) {
			return $errors;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'memberpress_login' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'memberpress_login', 'invalid' );

				// Add error to WP_Error object.
				$errors->add( 'invalid_captcha', $error_message );
			}
		}

		return $errors;
	}

	/**
	 * Validate CAPTCHA for MemberPress registration
	 *
	 * Validates the CAPTCHA response during registration.
	 *
	 * @param array $errors Array of errors.
	 * @return array Modified errors array.
	 */
	public function validate_memberpress_register_captcha( $errors ) {
		// Check if CAPTCHA is enabled for MemberPress registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_memberpress_register' );
		if ( 'yes' !== $is_enabled ) {
			return $errors;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'memberpress_register' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'memberpress_register', 'invalid' );

				// Add error to errors array.
				$errors[] = $error_message;
			}
		}

		return $errors;
	}
}
