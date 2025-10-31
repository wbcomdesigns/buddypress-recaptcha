<?php
/**
 * WPForms Integration
 *
 * Handles CAPTCHA rendering and validation for WPForms forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/wpforms-classes
 */

/**
 * WPForms CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with WPForms forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/wpforms-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WPForms_Form {

	/**
	 * Render CAPTCHA in WPForms form
	 *
	 * Hooks into wpforms_frontend_output to display CAPTCHA before submit button.
	 *
	 * @param string $form_data Form data and settings.
	 * @param null   $deprecated Deprecated parameter.
	 * @param string $title      Form title.
	 * @param string $description Form description.
	 * @param array  $errors     Form errors.
	 * @return void
	 */
	public function render_wpforms_captcha( $form_data, $deprecated, $title, $description, $errors ) {
		// Check if CAPTCHA is enabled for WPForms
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_wpforms' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Render CAPTCHA using service manager
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'wpforms' );
		}
	}

	/**
	 * Validate CAPTCHA for WPForms submissions
	 *
	 * Validates the CAPTCHA response during form processing.
	 *
	 * @param array $fields    Submitted form fields.
	 * @param array $entry     Form entry data.
	 * @param array $form_data Form data and settings.
	 * @return void
	 */
	public function validate_wpforms_captcha( $fields, $entry, $form_data ) {
		// Check if CAPTCHA is enabled for WPForms
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_wpforms' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'wpforms' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'wpforms', 'invalid' );

				// Add error to WPForms
				wpforms()->process->errors[ $form_data['id'] ]['header'] = $error_message;
			}
		}
	}
}
