<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
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
	public function render_gravityforms_captcha( $form_string, $form ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Check if CAPTCHA is enabled for Gravity Forms.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_gravityforms' );
		if ( 'yes' !== $is_enabled ) {
			return $form_string;
		}

		ob_start();
		wbc_captcha_service_manager()->render( 'gravityforms' );
		$captcha_html = ob_get_clean();

		// Before the LAST footer: the submit footer of a single-page form, or
		// the last page's footer of a multi-page form (gform_page_footer).
		// Matches GF 2.4 and 2.5+ markup (quote style, gform_footer / gform-footer).
		if ( preg_match_all( '~<div\s+class=([\'"])[^\'"]*\bgform[_-](?:page[_-])?footer\b~i', $form_string, $m, PREG_OFFSET_CAPTURE ) ) {
			$last        = end( $m[0] );
			$form_string = substr_replace( $form_string, $captcha_html, $last[1], 0 );
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
		// Check if CAPTCHA is enabled for Gravity Forms.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_gravityforms' );
		if ( 'yes' !== $is_enabled ) {
			return $validation_result;
		}

		// Skip if already failed validation.
		if ( ! $validation_result['is_valid'] ) {
			return $validation_result;
		}

		// Multi-page forms validate on every page step; the widget is only on
		// the last page, so verify only on the final submit (target page 0).
		$form_id     = isset( $validation_result['form']['id'] ) ? (int) $validation_result['form']['id'] : 0;
		$target_page = isset( $_POST[ 'gform_target_page_number_' . $form_id ] ) ? absint( $_POST[ 'gform_target_page_number_' . $form_id ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Gravity Forms handles its own nonce.
		if ( 0 !== $target_page ) {
			return $validation_result;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'gravityforms' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'gravityforms', 'invalid' );

				// Mark form as invalid.
				$validation_result['is_valid'] = false;

				// Add error message to the first field (or create a validation message).
				$form = $validation_result['form'];
				if ( ! empty( $form['fields'] ) ) {
					$validation_result['form']['fields'][0]['failed_validation']  = true;
					$validation_result['form']['fields'][0]['validation_message'] = $error_message;
				}
			}
		}

		return $validation_result;
	}
}
