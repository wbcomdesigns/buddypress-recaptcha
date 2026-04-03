<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Elementor Pro Integration
 *
 * Handles CAPTCHA rendering and validation for Elementor Pro forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/elementorpro-classes
 */

/**
 * Elementor Pro CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Elementor Pro forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/elementorpro-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class ElementorPro_Form {

	/**
	 * Render CAPTCHA in Elementor Pro forms
	 *
	 * Adds CAPTCHA HTML after form fields.
	 *
	 * @param array  $item       Form item.
	 * @param int    $item_index Item index.
	 * @param object $form       Form object.
	 * @return void
	 */
	public function render_elementorpro_captcha( $item, $item_index, $form ) {
		// Check if CAPTCHA is enabled for Elementor Pro.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_elementorpro' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Only render once per form (on the last field).
		$fields = $form->get_settings( 'form_fields' );
		if ( count( $fields ) - 1 !== $item_index ) {
			return;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="elementor-field-group elementor-column elementor-field-type-captcha elementor-col-100">';
			wbc_captcha_service_manager()->render( 'elementorpro' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA for Elementor Pro submissions
	 *
	 * Validates the CAPTCHA response during form processing.
	 *
	 * @param object $record The form record.
	 * @param object $ajax_handler The AJAX handler.
	 * @return void
	 */
	public function validate_elementorpro_captcha( $record, $ajax_handler ) {
		// Check if CAPTCHA is enabled for Elementor Pro.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_elementorpro' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'elementorpro' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'elementorpro', 'invalid' );

				// Add error to AJAX handler.
				$ajax_handler->add_error_message( $error_message );
			}
		}
	}
}
