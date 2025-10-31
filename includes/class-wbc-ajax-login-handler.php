<?php
/**
 * AJAX Login Handler with CAPTCHA Protection
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 */

/**
 * AJAX Login Handler Class
 *
 * Handles AJAX login requests with CAPTCHA verification.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_AJAX_Login_Handler {

	/**
	 * Handle AJAX login request
	 */
	public function handle_ajax_login() {
		// Verify nonce
		if ( ! isset( $_POST['wbc_login_nonce'] ) || ! wp_verify_nonce( $_POST['wbc_login_nonce'], 'wbc_ajax_login_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed. Please refresh the page and try again.', 'buddypress-recaptcha' ),
			) );
		}

		// Verify CAPTCHA
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'widget_login' ) ) {
				$error_message = wbc_get_captcha_error_message( 'widget_login', 'invalid' );
				wp_send_json_error( array(
					'message' => $error_message,
				) );
			}
		}

		// Sanitize inputs
		$username = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
		$password = isset( $_POST['password'] ) ? $_POST['password'] : '';
		$remember = isset( $_POST['remember'] ) && 'yes' === $_POST['remember'];
		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( $_POST['redirect_to'] ) : home_url();

		// Validate required fields
		if ( empty( $username ) || empty( $password ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please enter both username and password.', 'buddypress-recaptcha' ),
			) );
		}

		// Attempt login
		$credentials = array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => $remember,
		);

		$user = wp_signon( $credentials, is_ssl() );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array(
				'message' => $user->get_error_message(),
			) );
		}

		// Login successful
		wp_send_json_success( array(
			'message'     => sprintf(
				/* translators: %s: user display name */
				__( 'Welcome back, %s!', 'buddypress-recaptcha' ),
				$user->display_name
			),
			'redirect_to' => $redirect_to,
			'user'        => array(
				'id'           => $user->ID,
				'display_name' => $user->display_name,
				'email'        => $user->user_email,
			),
		) );
	}
}
