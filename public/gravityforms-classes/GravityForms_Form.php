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
	 * Whether this POST is a page step that cannot save an entry.
	 *
	 * True only when Gravity Forms itself reports a non-zero target page for the
	 * (clamped) source page. A target of 0 is the final submit and is always
	 * verified; so is any case where GF's classes or method are unavailable.
	 *
	 * @param array|object $form The form object.
	 * @return bool
	 */
	public static function is_page_step( $form ) {
		if ( ! class_exists( 'GFFormDisplay' ) || ! is_callable( array( 'GFFormDisplay', 'get_target_page' ) ) ) {
			return false;
		}

		$form_id   = (int) ( is_object( $form ) ? $form->id : ( $form['id'] ?? 0 ) );
		$fields    = (array) ( is_object( $form ) ? $form->fields : ( $form['fields'] ?? array() ) );
		$last_page = 1;
		foreach ( $fields as $field ) {
			if ( 'page' === ( is_object( $field ) ? $field->type : ( $field['type'] ?? '' ) ) ) {
				++$last_page;
			}
		}

		$source = isset( $_POST[ 'gform_source_page_number_' . $form_id ] ) ? absint( $_POST[ 'gform_source_page_number_' . $form_id ] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Gravity Forms handles its own nonce.
		$source = max( 1, min( $source, $last_page ) );

		$field_values = array();
		if ( isset( $_POST['gform_field_values'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_parse_str( sanitize_text_field( wp_unslash( $_POST['gform_field_values'] ) ), $field_values ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		$target = GFFormDisplay::get_target_page( $form, $source, $field_values );

		return is_numeric( $target ) && (int) $target > 0;
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

		// Multi-page forms run this filter on every page step, but the widget is
		// only on the last page. Skip ONLY a genuine intermediate step, and decide
		// that from Gravity Forms' own computation of where this POST goes next,
		// never from a posted page number: source and target are both client-set,
		// so trusting either lets a final submit (target 0) through unchecked.
		// If GF cannot answer, fall through and verify.
		if ( self::is_page_step( $validation_result['form'] ?? array() ) ) {
			return $validation_result;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'gravityforms' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'gravityforms', 'invalid' );

				// Mark form as invalid.
				$validation_result['is_valid'] = false;

				// Attach the message to the last field so GF returns the visitor to the
				// last page, where the widget is (field 0 would send them to page 1).
				$last_key = array_key_last( (array) $validation_result['form']['fields'] );
				if ( null !== $last_key ) {
					$validation_result['form']['fields'][ $last_key ]['failed_validation']  = true;
					$validation_result['form']['fields'][ $last_key ]['validation_message'] = $error_message;
				}
			}
		}

		return $validation_result;
	}
}
