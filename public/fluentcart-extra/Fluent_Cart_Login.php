<?php
/**
 * FluentCart Login CAPTCHA Integration
 *
 * Handles CAPTCHA rendering and validation for FluentCart customer login form.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/fluentcart-extra
 */

/**
 * FluentCart Login CAPTCHA handler
 *
 * Integrates CAPTCHA protection with FluentCart customer login form.
 * Uses the shortcode [fluent_cart_login_form] form.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/fluentcart-extra
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Fluent_Cart_Login {

	/**
	 * Render CAPTCHA on login form
	 *
	 * Hooked to: fluent_cart/views/checkout_page_login_form
	 *
	 * @param array $view_data View data passed to the login form.
	 * @return void
	 */
	public function render_login_captcha( $view_data ) {
		// Check if CAPTCHA is enabled for FluentCart login
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_login' );

		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return;
		}

		// Render CAPTCHA using service manager
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="fct-form-group fct-captcha-wrapper" style="margin: 15px 0;">';
			wbc_captcha_service_manager()->render( 'fluent_cart_login' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA on login submission
	 *
	 * Hooked to: authenticate filter (priority 20, before password check)
	 *
	 * @param WP_User|WP_Error|null $user     WP_User or WP_Error object from previous authenticate filter.
	 * @param string                $username Username or email address.
	 * @param string                $password Password.
	 * @return WP_User|WP_Error User object on success, WP_Error on failure.
	 */
	public function validate_login_captcha( $user, $username, $password ) {
		// Only validate if this is a FluentCart login attempt
		// Check for FluentCart-specific nonce or referer
		if ( ! isset( $_POST['fc_login_nonce'] ) && ! isset( $_POST['fluent_cart_login'] ) ) {
			return $user;
		}

		// Skip if already an error
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// Check if CAPTCHA is enabled for FluentCart login
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_login' );

		if ( 'yes' !== $is_enabled ) {
			return $user;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return $user;
		}

		// Verify CAPTCHA
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'fluent_cart_login' ) ) {
				// Get appropriate error message
				$error_message = wbc_get_captcha_error_message( 'fluent_cart_login', 'invalid' );

				// Return WP_Error
				return new WP_Error( 'captcha_error', $error_message );
			}
		}

		return $user;
	}

	/**
	 * Validate CAPTCHA on FluentCart AJAX login
	 *
	 * FluentCart may use AJAX for login. This handles that case.
	 * Hooked to: fluent_cart/before_login_process
	 *
	 * @param array $credentials Login credentials array.
	 * @return void Dies with error if CAPTCHA validation fails.
	 */
	public function validate_ajax_login_captcha( $credentials ) {
		// Check if CAPTCHA is enabled for FluentCart login
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_login' );

		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return;
		}

		// Verify CAPTCHA
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'fluent_cart_login' ) ) {
				// Get appropriate error message
				$error_message = wbc_get_captcha_error_message( 'fluent_cart_login', 'invalid' );

				// For AJAX, send JSON error response
				if ( wp_doing_ajax() ) {
					wp_send_json_error(
						array(
							'message' => $error_message,
							'errors'  => array(
								'captcha' => $error_message,
							),
						)
					);
				} else {
					// For non-AJAX, die with error
					wp_die( esc_html( $error_message ), esc_html__( 'CAPTCHA Verification Failed', 'buddypress-recaptcha' ), array( 'back_link' => true ) );
				}
			}
		}
	}
}
