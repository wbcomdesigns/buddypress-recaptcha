<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Divi Builder Integration
 *
 * Handles CAPTCHA rendering and validation for Divi Builder contact forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/divi-classes
 */

/**
 * Divi Builder CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Divi Builder contact forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/divi-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Divi_Form {

	/**
	 * Render CAPTCHA in Divi Builder contact forms
	 *
	 * Adds CAPTCHA HTML to the contact form module.
	 *
	 * @param string $output The form output.
	 * @param string $module_slug The module slug.
	 * @return string Modified form output with CAPTCHA.
	 */
	public function render_divi_captcha( $output, $module_slug ) {
		// Only process contact form modules.
		if ( 'et_pb_contact_form' !== $module_slug ) {
			return $output;
		}

		// Check if CAPTCHA is enabled for Divi.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_divi' );
		if ( 'yes' !== $is_enabled ) {
			return $output;
		}

		// Render CAPTCHA using service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			ob_start();
			echo '<div class="et_pb_contact_field" data-id="captcha" data-type="captcha">';
			echo '<p class="clearfix">';
			wbc_captcha_service_manager()->render( 'divi' );
			echo '</p>';
			echo '</div>';
			$captcha_html = ob_get_clean();

			// Insert CAPTCHA before the submit button.
			$output = preg_replace(
				'/(<div class="et_contact_bottom_container">)/i',
				$captcha_html . '$1',
				$output
			);
		}

		return $output;
	}

	/**
	 * Validate CAPTCHA for Divi Builder contact form submissions
	 *
	 * Validates the CAPTCHA response during form processing.
	 *
	 * @param bool $success Whether the form submission is successful.
	 * @return bool Modified success status.
	 */
	public function validate_divi_captcha( $success ) {
		// Check if CAPTCHA is enabled for Divi.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_divi' );
		if ( 'yes' !== $is_enabled ) {
			return $success;
		}

		// Verify CAPTCHA using service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'divi' ) ) {
				// Get error message.
				$error_message = wbc_get_captcha_error_message( 'divi', 'invalid' );

				// Add error to Divi's error handling.
				wp_send_json(
					array(
						'error'   => true,
						'message' => $error_message,
					)
				);
			}
		}

		return $success;
	}
}
