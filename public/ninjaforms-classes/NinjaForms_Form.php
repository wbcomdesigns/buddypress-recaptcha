<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Ninja Forms Integration
 *
 * Handles CAPTCHA rendering and validation for Ninja Forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ninjaforms-classes
 */

/**
 * Ninja Forms CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Ninja Forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ninjaforms-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class NinjaForms_Form {

	/**
	 * Render CAPTCHA in Ninja Forms
	 *
	 * Adds CAPTCHA HTML after form fields before submit button.
	 *
	 * @return void
	 */
	public function render_ninjaforms_captcha() {
		// Check if CAPTCHA is enabled for Ninja Forms.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_ninjaforms' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'ninjaforms' );
		}
	}

	/**
	 * Validate CAPTCHA for Ninja Forms submissions
	 *
	 * Validates the CAPTCHA response during form processing.
	 *
	 * @param array $form_data Form submission data.
	 * @return array Modified form data with errors if validation fails.
	 */
	public function validate_ninjaforms_captcha( $form_data ) {
		// Check if CAPTCHA is enabled for Ninja Forms.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_ninjaforms' );
		if ( 'yes' !== $is_enabled ) {
			return $form_data;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'ninjaforms' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'ninjaforms', 'invalid' );

				// Add error to form data.
				$form_data['errors']['form']['captcha'] = $error_message;
			}
		}

		return $form_data;
	}
}
