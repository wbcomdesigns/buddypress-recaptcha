<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Ultimate Member Integration
 *
 * Handles CAPTCHA rendering and validation for Ultimate Member forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ultimatemember-classes
 */

/**
 * Ultimate Member CAPTCHA Handler
 *
 * Integrates CAPTCHA protection with Ultimate Member login, registration, and password reset forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/ultimatemember-classes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class UltimateMember_Form {

	/**
	 * Map a UM form mode to its CAPTCHA context.
	 *
	 * @param string $mode UM form mode.
	 * @return string Context, or '' for forms we do not protect.
	 */
	private function context_for( $mode ) {
		$contexts = array(
			'login'    => 'um_login',
			'register' => 'um_register',
			'password' => 'um_password',
		);
		return isset( $contexts[ $mode ] ) ? $contexts[ $mode ] : '';
	}

	/**
	 * Render the CAPTCHA after the fields of a UM login, registration or
	 * password-reset form.
	 *
	 * UM prints field errors only next to a field with the same key, and there
	 * is no "captcha" field, so the error is printed here (UM's login form
	 * already lists general errors at the top).
	 *
	 * @param array $args Form arguments, including 'mode'.
	 */
	public function render_um_captcha( $args ) {
		$mode    = isset( $args['mode'] ) ? $args['mode'] : '';
		$context = $this->context_for( $mode );
		if ( '' === $context ) {
			return;
		}

		echo '<div class="um-field um-field-captcha">';
		wbc_captcha_service_manager()->render( $context );
		if ( 'login' !== $mode && UM()->form()->has_error( 'captcha' ) ) {
			echo '<div class="um-field-error" role="alert">' . esc_html( wp_strip_all_tags( UM()->form()->errors['captcha'] ) ) . '</div>';
		}
		echo '</div>';
	}

	/**
	 * Validate the UM login, registration and password-reset forms.
	 *
	 * UM passes ( $post_form, $form_data ) and the form's mode lives in
	 * $form_data, so reading it from the first argument never matched and no
	 * UM form was ever checked. Password reset fires its own hook
	 * (um_reset_password_errors_hook) and has no form mode there.
	 *
	 * @param array $post_form Submitted fields.
	 * @param array $form_data Form settings, including 'mode'.
	 */
	public function validate_um_captcha( $post_form, $form_data = array() ) {
		$mode    = doing_action( 'um_reset_password_errors_hook' ) ? 'password' : ( isset( $form_data['mode'] ) ? $form_data['mode'] : '' );
		$context = $this->context_for( $mode );
		if ( '' === $context ) {
			return;
		}

		if ( ! wbc_verify_captcha( $context ) ) {
			UM()->form()->add_error( 'captcha', wbc_get_captcha_error_message( $context, 'invalid' ) );
		}
	}
}
