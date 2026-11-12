<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Ninja Forms Integration
 *
 * Handles CAPTCHA rendering and validation for Ninja Forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ninjaforms-classes
 */

/**
 * Ninja Forms CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Ninja Forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ninjaforms-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class NinjaForms_Form {

	/**
	 * Add the CAPTCHA after the form's fields.
	 *
	 * ninja_forms_display_after_fields is a filter whose HTML Ninja Forms'
	 * JavaScript renders inside the form (echoing here printed it outside).
	 *
	 * @param string $html    HTML other plugins added.
	 * @param int    $form_id Form ID.
	 * @return string
	 */
	public function render_ninjaforms_captcha( $html = '', $form_id = 0 ) {
		$manager = wbc_captcha_service_manager();
		$service = $manager->get_active_service();
		if ( ! $service ) {
			return $html;
		}

		ob_start();
		$manager->render( 'ninjaforms' );
		$captcha = ob_get_clean();
		if ( '' === trim( $captcha ) ) {
			return $html;
		}

		return $html . '<div class="wbc-nf-captcha" data-wbc-field="' . esc_attr( $service->get_response_field_name() ) . '">' . $captcha . '</div>';
	}

	/**
	 * Verify the token wbc-captcha-refresh.js adds to the submission's extra
	 * data (Ninja Forms posts only its own fields). On failure, answer in Ninja
	 * Forms' own response shape so the form shows the error.
	 *
	 * @param array $form_data Submission data.
	 * @return array
	 */
	public function validate_ninjaforms_captcha( $form_data ) {
		$token = isset( $form_data['extra']['wbc_captcha'] ) ? sanitize_text_field( (string) $form_data['extra']['wbc_captcha'] ) : '';

		if ( wbc_captcha_service_manager()->verify( 'ninjaforms', $token ) ) {
			return $form_data;
		}

		wp_send_json(
			array(
				'data'   => array( 'form_id' => isset( $form_data['id'] ) ? $form_data['id'] : 0 ),
				'errors' => array( 'form' => array( 'wbc_captcha' => wbc_get_captcha_error_message( 'ninjaforms', 'invalid' ) ) ),
				'debug'  => array(),
			)
		);
	}
}
