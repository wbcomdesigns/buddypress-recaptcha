<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_Login {

	/**
	 * Render captcha on login form
	 */
	public function woo_extra_wp_login_form() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'wp_login' );
		}
	}

	/**
	 * Render the login CAPTCHA inside forms built by wp_login_form().
	 *
	 * `wp_login_form()` is what the core Login/Logout block, the login widget and
	 * most theme login modals output, and it fires NONE of the render hooks above
	 * (`login_form` is only fired by wp-login.php itself). Meanwhile the verifier on
	 * `wp_authenticate_user` runs for every login POST. Without this filter the two
	 * sides disagree: nothing renders, everything is verified, and login is
	 * impossible from any of those forms.
	 *
	 * This must be a *returning* filter, not an echo: `wp_login_form()` assembles its
	 * markup into a string first, so anything echoed from here lands before the
	 * opening `<form>` tag and the token/nonce fields would never be submitted.
	 *
	 * @since 2.2.0
	 *
	 * @param string $content Existing markup for the middle of the login form.
	 * @return string Markup with the CAPTCHA appended.
	 */
	public function filter_login_form_middle( $content = '' ) {
		if ( ! function_exists( 'wbc_captcha_service_manager' ) ) {
			return $content;
		}

		ob_start();
		wbc_captcha_service_manager()->render( 'wp_login' );
		$captcha = ob_get_clean();

		if ( empty( $captcha ) ) {
			return $content;
		}

		return $content . $captcha;
	}
}
