<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
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
		// Check if CAPTCHA is enabled for CF7.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_cf7' );
		if ( 'yes' !== $is_enabled ) {
			return $form;
		}

		// Get CAPTCHA HTML from service manager.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			ob_start();
			wbc_captcha_service_manager()->render( 'cf7' );
			$captcha_html = ob_get_clean();

			// Inject CAPTCHA before the submit button.
			// CF7 uses <input type="submit" for submit buttons.
			$form = preg_replace(
				'/(<input[^>]*type=["\']submit["\'][^>]*>)/i',
				$captcha_html . '$1',
				$form
			);
		}

		return $form;
	}

	/**
	 * Treat a failed CAPTCHA as spam, the way CF7's own reCAPTCHA integration
	 * does. (wpcf7_validate passes an array of tags, so invalidating it never
	 * stopped the mail.)
	 *
	 * @param bool                 $spam       Whether CF7 already flagged it.
	 * @param WPCF7_Submission|null $submission The submission.
	 * @return bool
	 */
	public function validate_cf7_captcha( $spam, $submission = null ) {
		if ( $spam ) {
			return $spam;
		}

		if ( ! wbc_verify_captcha( 'cf7' ) ) {
			if ( $submission && method_exists( $submission, 'add_spam_log' ) ) {
				$submission->add_spam_log(
					array(
						'agent'  => 'wbc-captcha',
						'reason' => wbc_get_captcha_error_message( 'cf7', 'invalid' ),
					)
				);
			}
			return true;
		}

		return false;
	}
}
