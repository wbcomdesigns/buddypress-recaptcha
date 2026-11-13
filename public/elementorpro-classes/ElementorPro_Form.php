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
	 * Put the CAPTCHA just before the form's submit button.
	 *
	 * Runs on elementor/widget/render_content. (It used to be added on the
	 * per-field filter, which printed it before the last field and returned
	 * nothing from a filter.)
	 *
	 * @param string                 $content Widget HTML.
	 * @param \Elementor\Widget_Base $widget  The widget.
	 * @return string
	 */
	public function render_elementorpro_captcha( $content, $widget ) {
		if ( ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) || 'form' !== $widget->get_name() ) {
			return $content;
		}

		ob_start();
		wbc_captcha_service_manager()->render( 'elementorpro' );
		$captcha = trim( ob_get_clean() );
		if ( '' === $captcha ) {
			return $content;
		}

		$captcha = '<div class="elementor-field-group elementor-column elementor-field-type-captcha elementor-col-100">' . $captcha . '</div>';
		$marker  = '<div class="elementor-field-group elementor-column elementor-field-type-submit';
		$pos     = strpos( $content, $marker );

		return false === $pos ? $content : substr_replace( $content, $captcha, $pos, 0 );
	}

	/**
	 * Keep protected Form widgets out of Elementor's element cache.
	 *
	 * The cache serves the stored widget HTML on later page loads without
	 * running the widget, so the provider script is never enqueued (the
	 * widget is inert and every submit fails) and a provider switch keeps the
	 * old markup. A dynamic widget is rendered fresh on every load.
	 *
	 * @param bool                    $is_dynamic Whether Elementor treats the element as dynamic.
	 * @param array                   $raw_data   Element data.
	 * @param \Elementor\Element_Base $element    The element.
	 * @return bool
	 */
	public function keep_form_out_of_element_cache( $is_dynamic, $raw_data, $element ) {
		if ( $is_dynamic || ! is_object( $element ) || ! method_exists( $element, 'get_name' ) || 'form' !== $element->get_name() ) {
			return $is_dynamic;
		}
		return 'yes' === get_option( 'wbc_recaptcha_enable_on_elementorpro' );
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
