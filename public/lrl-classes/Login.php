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
}