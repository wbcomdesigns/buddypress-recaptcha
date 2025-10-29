<?php
/**
 * FluentCart Registration CAPTCHA Integration
 *
 * Handles CAPTCHA rendering and validation for FluentCart customer registration form.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/fluentcart-extra
 */

/**
 * FluentCart Registration CAPTCHA handler
 *
 * Integrates CAPTCHA protection with FluentCart customer registration form.
 * Uses the shortcode [fluent_cart_registration_form] form.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/fluentcart-extra
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Fluent_Cart_Registration {

	/**
	 * Render CAPTCHA on registration form
	 *
	 * Hooked to: fluent_cart/views/checkout_page_registration_form
	 *
	 * @param array $view_data View data passed to the registration form.
	 * @return void
	 */
	public function render_registration_captcha( $view_data ) {
		// Check if CAPTCHA is enabled for FluentCart registration
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_register' );

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
			wbc_captcha_service_manager()->render( 'fluent_cart_register' );
			echo '</div>';
		}
	}

	/**
	 * Validate CAPTCHA on registration submission
	 *
	 * Hooked to: fluent_cart/validation/registration (priority 5, before user creation)
	 *
	 * @param array $errors    Validation errors array.
	 * @param array $post_data Posted form data.
	 * @return array Modified errors array.
	 */
	public function validate_registration_captcha( $errors, $post_data ) {
		// Check if CAPTCHA is enabled for FluentCart registration
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_register' );

		if ( 'yes' !== $is_enabled ) {
			return $errors;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return $errors;
		}

		// Verify CAPTCHA using the service manager
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'fluent_cart_register' ) ) {
				// Get appropriate error message
				$error_message = wbc_get_captcha_error_message( 'fluent_cart_register', 'invalid' );

				// Add error in WP_Error format
				if ( ! is_wp_error( $errors ) ) {
					$errors = new WP_Error();
				}
				$errors->add( 'captcha_error', $error_message );
			}
		}

		return $errors;
	}

	/**
	 * Validate CAPTCHA before user registration (alternative hook)
	 *
	 * This provides validation at the WordPress registration level.
	 * Hooked to: register_post filter
	 *
	 * @param string   $sanitized_user_login Sanitized username.
	 * @param string   $user_email           User email.
	 * @param WP_Error $errors               Registration errors.
	 * @return void
	 */
	public function validate_wp_registration_captcha( $sanitized_user_login, $user_email, $errors ) {
		// Only validate if this is a FluentCart registration
		// Check for FluentCart-specific nonce or referer
		if ( ! isset( $_POST['fc_registration_nonce'] ) && ! isset( $_POST['fluent_cart_register'] ) ) {
			return;
		}

		// Check if CAPTCHA is enabled for FluentCart registration
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_fluentcart_register' );

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
			if ( ! wbc_verify_captcha( 'fluent_cart_register' ) ) {
				// Get appropriate error message
				$error_message = wbc_get_captcha_error_message( 'fluent_cart_register', 'invalid' );
				$errors->add( 'captcha_error', $error_message );
			}
		}
	}
}
