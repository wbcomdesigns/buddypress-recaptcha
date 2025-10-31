<?php
/**
 * Gravity Forms Integration
 *
 * Handles CAPTCHA rendering and validation for Gravity Forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/gravityforms-classes
 */

/**
 * Gravity Forms CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Gravity Forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/gravityforms-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class GravityForms_Form {

	/**
	 * Render CAPTCHA in Gravity Forms
	 *
	 * Adds CAPTCHA HTML before the submit button.
	 *
	 * @param string $form_string The form HTML.
	 * @param array  $form        The form object.
	 * @return string Modified form HTML with CAPTCHA.
	 */
	public function render_gravityforms_captcha( $form_string, $form ) {
		// Check if CAPTCHA is enabled for Gravity Forms
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_gravityforms' );
		if ( 'yes' !== $is_enabled ) {
			return $form_string;
		}

		// Get CAPTCHA HTML from service manager
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			ob_start();
			wbc_captcha_service_manager()->render( 'gravityforms' );
			$captcha_html = ob_get_clean();

			// Inject CAPTCHA before the submit button
			// Gravity Forms uses gform_footer for the submit button area
			$form_string = str_replace(
				'<div class=\'gform_footer',
				$captcha_html . '<div class=\'gform_footer',
				$form_string
			);
		}

		return $form_string;
	}

	/**
	 * Validate CAPTCHA for Gravity Forms submissions
	 *
	 * Validates the CAPTCHA response and adds validation errors.
	 *
	 * @param array $validation_result Validation result array.
	 * @return array Modified validation result.
	 */
	public function validate_gravityforms_captcha( $validation_result ) {
		// Check if CAPTCHA is enabled for Gravity Forms
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_gravityforms' );
		if ( 'yes' !== $is_enabled ) {
			return $validation_result;
		}

		// Skip if already failed validation
		if ( ! $validation_result['is_valid'] ) {
			return $validation_result;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'gravityforms' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'gravityforms', 'invalid' );

				// Mark form as invalid
				$validation_result['is_valid'] = false;

				// Add error message to the first field (or create a validation message)
				$form = $validation_result['form'];
				if ( ! empty( $form['fields'] ) ) {
					$validation_result['form']['fields'][0]['failed_validation'] = true;
					$validation_result['form']['fields'][0]['validation_message'] = $error_message;
				}
			}
		}

		return $validation_result;
	}
}
