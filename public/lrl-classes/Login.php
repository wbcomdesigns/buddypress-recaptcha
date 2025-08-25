<?php
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
class Login {

	/**
	 * Render captcha on login form
	 */
	public function woo_extra_wp_login_form() {
		// Use the service manager to render captcha
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'wp_login' );
		}
	}

	/**
	 * Verify captcha on login attempt
	 *
	 * @param WP_User|WP_Error $user     User object or error.
	 * @param string           $password User password.
	 * @return WP_User|WP_Error
	 */
	public function woo_extra_check_for_wp_login( $user, $password ) {
		// Skip if already an error
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// Skip for empty credentials
		if ( empty( $_POST['log'] ) || empty( $_POST['pwd'] ) ) {
			return $user;
		}

		// Verify captcha using the service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'wp_login' ) ) {
				$error_message = wbc_get_captcha_error_message( 'wp_login', 'invalid' );
				return new WP_Error( 'captcha_error', $error_message );
			}
		}

		return $user;
	}
}