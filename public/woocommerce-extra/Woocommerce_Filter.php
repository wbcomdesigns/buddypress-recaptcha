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
class Woocommerce_Filter {

	/**
	 * Login gate for "Login Form" protection, hooked to `authenticate` at 99.
	 *
	 * Runs after WordPress has looked the user up and checked the password, and
	 * replaces whatever it found with the CAPTCHA error when the CAPTCHA fails. So:
	 *
	 * - Every form that logs in with the core `log` field is covered: wp-login.php,
	 *   wp_login_form() forms, and theme pop-ups that call wp_signon() over AJAX
	 *   (Reign, BuddyX Pro). They all render the wp_login widget.
	 * - An unknown username and a wrong password get the same CAPTCHA error, so
	 *   usernames cannot be probed without solving the CAPTCHA.
	 * - Password logins over XML-RPC are refused: no XML-RPC client can show a
	 *   CAPTCHA, so they were an unlimited brute-force path around it. Application
	 *   passwords (random, not guessable) keep working, and a site can allow
	 *   passwords again with the `wbc_allow_xmlrpc_password_login` filter.
	 * - A form whose own CAPTCHA already passed in this request (MemberPress login,
	 *   for example) is not checked a second time; tokens are single-use.
	 *
	 * @param WP_User|WP_Error|null $user     Result so far.
	 * @param string                $username Submitted username.
	 * @param string                $password Submitted password.
	 * @return WP_User|WP_Error|null
	 */
	public function woo_wp_verify_login_captcha( $user, $username = '', $password = '' ) {
		if ( did_action( 'application_password_did_authenticate' ) ) {
			return $user;
		}

		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			if ( '' === (string) $username && '' === (string) $password ) {
				return $user;
			}
			if ( apply_filters( 'wbc_allow_xmlrpc_password_login', false, $username ) ) {
				return $user;
			}
			return new WP_Error(
				'wbc_xmlrpc_password_login_disabled',
				__( 'Password logins over XML-RPC are disabled on this site. Use an application password instead.', 'buddypress-recaptcha' )
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the CAPTCHA service verifies the form nonce.
		if ( ! isset( $_POST['log'] ) ) {
			return $user;
		}

		if ( function_exists( 'wbc_captcha_service_manager' ) && wbc_captcha_service_manager()->has_verified_this_request() ) {
			return $user;
		}

		if ( function_exists( 'wbc_verify_captcha' ) && ! wbc_verify_captcha( 'wp_login' ) ) {
			$error_msg = wbc_get_captcha_error_message( 'wp_login', 'invalid' );
			return new WP_Error( 'captcha_invalid', '<strong>' . __( 'Error:', 'buddypress-recaptcha' ) . '</strong> ' . $error_msg );
		}

		return $user;
	}

	/**
	 * Display order tracking captcha
	 *
	 * @return void
	 */
	public function woo_display_order_tracking_captcha() {
		// Use the service manager to render captcha.
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
		// Check if captcha is enabled for order tracking.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_order_tracking' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify captcha.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'order_tracking' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'order_tracking', 'invalid' );
				wc_add_notice( $error_msg, 'error' );
			}
		}
	}

	/**
	 * Verify comment captcha
	 *
	 * @param array $commentdata Comment data.
	 * @return array
	 */
	public function woo_verify_comment_captcha( $commentdata ) {
		// Check if captcha is enabled for comments.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_comment' );
		if ( 'yes' !== $is_enabled ) {
			return $commentdata;
		}

		// Replies sent from wp-admin (Comments > Reply, the Dashboard Activity widget)
		// run through core's replyto-comment AJAX handler, which has already checked
		// its nonce and the user's edit_post capability. No admin screen renders a
		// CAPTCHA, so verifying here would always reject the reply.
		if ( doing_action( 'wp_ajax_replyto-comment' ) ) {
			return $commentdata;
		}

		// Moderators always, other members per "Skip for Logged-in Users".
		if ( wbc_skip_comment_captcha_for_current_user() ) {
			return $commentdata;
		}

		// Verify captcha.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'comment' ) ) {
				$error_msg = wbc_get_captcha_error_message( 'comment', 'invalid' );
				wp_die(
					esc_html( $error_msg ),
					esc_html__( 'Comment Submission Failed', 'buddypress-recaptcha' ),
					array(
						'response'  => 403,
						'back_link' => true,
					)
				);
			}
		}

		return $commentdata;
	}
}
