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
class WoocommerceFilter {

	/**
	 * Verify login captcha
	 *
	 * @param mixed  $user     User object or WP_Error
	 * @param string $password Password
	 * @return mixed User object or WP_Error
	 */
	public function woo_wp_verify_login_captcha( $user, $password ) {
		// Check if we need to verify
		if ( ! isset( $_POST['log'] ) ) {
			return $user;
		}

		// Use verification helper
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'wp_login' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'wp_login', 'invalid' );
				return new WP_Error( 'captcha_invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $error_msg );
			}
		}

		return $user;
	}

	/**
	 * Display order tracking captcha
	 *
	 * @return void
	 */
	public function woo_display_order_tracking_captcha() {
		// Use the service manager to render captcha
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'order_tracking' );
		}
	}

	/**
	 * Verify order tracking captcha
	 *
	 * @return void
	 */
	public function woo_verify_order_tracking_captcha() {
		// Check if captcha is enabled for order tracking
		$is_enabled = get_option( 'wbc_recapcha_enable_on_order_tracking' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify captcha
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'order_tracking' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'order_tracking', 'invalid' );
				wc_add_notice( $error_msg, 'error' );
			}
		}
	}

	/**
	 * Display comment form captcha
	 *
	 * @return void
	 */
	public function woo_display_comment_captcha() {
		// Use the service manager to render captcha
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'comment_form' );
		}
	}

	/**
	 * Verify comment captcha
	 *
	 * @param array $commentdata Comment data
	 * @return array
	 */
	public function woo_verify_comment_captcha( $commentdata ) {
		// Check if captcha is enabled for comments
		$is_enabled = get_option( 'wbc_recapcha_enable_on_comment' );
		if ( 'yes' !== $is_enabled ) {
			return $commentdata;
		}

		// Skip for logged-in users if configured
		if ( is_user_logged_in() ) {
			$skip_for_logged_in = get_option( 'wbc_recapcha_skip_comment_for_logged_in' );
			if ( 'yes' === $skip_for_logged_in ) {
				return $commentdata;
			}
		}

		// Verify captcha
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'comment_form' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'comment_form', 'invalid' );
				wp_die( 
					esc_html( $error_msg ),
					esc_html__( 'Comment Submission Failed', 'buddypress-recaptcha' ),
					array( 'response' => 403, 'back_link' => true )
				);
			}
		}

		return $commentdata;
	}
}