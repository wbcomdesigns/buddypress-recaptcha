<?php
/**
 * Contact Form 7 Integration
 *
 * Handles CAPTCHA rendering and validation for Contact Form 7 forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/cf7-classes
 */

/**
 * Contact Form 7 CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Contact Form 7 forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/cf7-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class CF7_Form {

	/**
	 * Render CAPTCHA in Contact Form 7 form
	 *
	 * Filters the CF7 form HTML to inject CAPTCHA before submit button.
	 *
	 * @param string $form The form HTML content.
	 * @return string Modified form HTML with CAPTCHA.
	 */
	public function render_cf7_captcha( $form ) {
		// Check if CAPTCHA is enabled for CF7
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_cf7' );
		if ( 'yes' !== $is_enabled ) {
			return $form;
		}

		// Get CAPTCHA HTML from service manager
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			ob_start();
			wbc_captcha_service_manager()->render( 'cf7' );
			$captcha_html = ob_get_clean();

			// Inject CAPTCHA before the submit button
			// CF7 uses <input type="submit" for submit buttons
			$form = preg_replace(
				'/(<input[^>]*type=["\']submit["\'][^>]*>)/i',
				$captcha_html . '$1',
				$form
			);
		}

		return $form;
	}

	/**
	 * Validate CAPTCHA for Contact Form 7 submissions
	 *
	 * Validates the CAPTCHA response and adds errors to CF7 validation result.
	 *
	 * @param WPCF7_Validation $result The validation result object.
	 * @param WPCF7_FormTag    $tag    The form tag being validated.
	 * @return WPCF7_Validation Modified validation result.
	 */
	public function validate_cf7_captcha( $result, $tag ) {
		// Check if CAPTCHA is enabled for CF7
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_cf7' );
		if ( 'yes' !== $is_enabled ) {
			return $result;
		}

		// Verify CAPTCHA using service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'cf7' ) ) {
				// Get error message
				$error_message = wbc_get_captcha_error_message( 'cf7', 'invalid' );

				// Add error to validation result
				$result->invalidate( $tag, $error_message );
			}
		}

		return $result;
	}
}
