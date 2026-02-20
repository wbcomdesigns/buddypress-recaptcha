<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Ultimate Member Integration
 *
 * Handles CAPTCHA rendering and validation for Ultimate Member forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ultimatemember-classes
 */

/**
 * Ultimate Member CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Ultimate Member login, registration, and password reset forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ultimatemember-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class UltimateMember_Form {

	/**
	 * Render CAPTCHA in Ultimate Member login form
	 *
	 * Adds CAPTCHA HTML to the login form.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function render_um_login_captcha( $args ) {
		// Check if this is a login form.
		if ( ! isset( $args['mode'] ) || 'login' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM login.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="um-field um-field-captcha">';
			wbc_captcha_service_manager()->render( 'um_login' );
			echo '</div>';
		}
	}

	/**
	 * Render CAPTCHA in Ultimate Member registration form
	 *
	 * Adds CAPTCHA HTML to the registration form.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function render_um_register_captcha( $args ) {
		// Check if this is a registration form.
		if ( ! isset( $args['mode'] ) || 'register' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="um-field um-field-captcha">';
			wbc_captcha_service_manager()->render( 'um_register' );
			echo '</div>';
		}
	}

	/**
	 * Render CAPTCHA in Ultimate Member password reset form
	 *
	 * Adds CAPTCHA HTML to the password reset form.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function render_um_password_captcha( $args ) {
		// Check if this is a password reset form.
		if ( ! isset( $args['mode'] ) || 'password' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM password reset.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_password' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="um-field um-field-captcha">';
			wbc_captcha_service_manager()->render( 'um_password' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA for Ultimate Member login
	 *
	 * Validates the CAPTCHA response during login.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function validate_um_login_captcha( $args ) {
		// Check if this is a login form.
		if ( ! isset( $args['mode'] ) || 'login' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM login.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_login' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'um_login' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'um_login', 'invalid' );

				// Add error to Ultimate Member's error handling.
				UM()->form()->add_error( 'captcha', $error_message );
			}
		}
	}

	/**
	 * Validate CAPTCHA for Ultimate Member registration
	 *
	 * Validates the CAPTCHA response during registration.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function validate_um_register_captcha( $args ) {
		// Check if this is a registration form.
		if ( ! isset( $args['mode'] ) || 'register' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM registration.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_register' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'um_register' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'um_register', 'invalid' );

				// Add error to Ultimate Member's error handling.
				UM()->form()->add_error( 'captcha', $error_message );
			}
		}
	}

	/**
	 * Validate CAPTCHA for Ultimate Member password reset
	 *
	 * Validates the CAPTCHA response during password reset.
	 *
	 * @param array $args Form arguments.
	 * @return void
	 */
	public function validate_um_password_captcha( $args ) {
		// Check if this is a password reset form.
		if ( ! isset( $args['mode'] ) || 'password' !== $args['mode'] ) {
			return;
		}

		// Check if CAPTCHA is enabled for UM password reset.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_um_password' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'um_password' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'um_password', 'invalid' );

				// Add error to Ultimate Member's error handling.
				UM()->form()->add_error( 'captcha', $error_message );
			}
		}
	}
}
