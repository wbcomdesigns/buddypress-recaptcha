<?php
/**
 * Forminator Integration
 *
 * Handles CAPTCHA rendering and validation for Forminator forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/forminator-classes
 */

/**
 * Forminator CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Forminator forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/forminator-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Forminator_Form {

	/**
	 * Render CAPTCHA in Forminator forms
	 *
	 * Adds CAPTCHA HTML before the submit button.
	 *
	 * @param string $html      The button markup HTML.
	 * @param int    $form_id   The form ID.
	 * @return string Modified HTML with CAPTCHA.
	 */
	public function render_forminator_captcha( $html, $form_id ) {
		// Check if CAPTCHA is enabled for Forminator
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_forminator' );
		if ( 'yes' !== $is_enabled ) {
			return $html;
		}

		// Get CAPTCHA HTML from service manager
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			ob_start();
			wbc_captcha_service_manager()->render( 'forminator' );
			$captcha_html = ob_get_clean();

			// Prepend CAPTCHA before button
			$html = $captcha_html . $html;
		}

		return $html;
	}

	/**
	 * Validate CAPTCHA for Forminator submissions
	 *
	 * Validates the CAPTCHA response and adds errors if validation fails.
	 *
	 * @param array $submit_errors  Current submit errors.
	 * @param int   $form_id        The form ID.
	 * @param array $field_data_array Field data array.
	 * @return array Modified submit errors.
	 */
	public function validate_forminator_captcha( $submit_errors, $form_id, $field_data_array ) {
		// Check if CAPTCHA is enabled for Forminator
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_forminator' );
		if ( 'yes' !== $is_enabled ) {
			return $submit_errors;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'forminator' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'forminator', 'invalid' );

				// Add error to submit errors array
				$submit_errors[] = array(
					'field_id' => 'captcha',
					'error_message' => $error_message,
				);
			}
		}

		return $submit_errors;
	}
}
