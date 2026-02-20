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
class Woocommerce_After_Checkout_Validation {

	/**
	 * Validate checkout captcha
	 *
	 * @param array    $fields            Checkout fields.
	 * @param WP_Error $validation_errors Validation errors.
	 * @return WP_Error
	 */
	public function woocomm_validate_checkout_captcha( $fields, $validation_errors ) {
		// Determine checkout context (guest or logged-in).
		$context = is_user_logged_in() ? 'woo_checkout_login' : 'woo_checkout_guest';

		// Check if captcha is enabled for this context.
		$is_enabled = is_user_logged_in()
			? get_option( 'wbc_recaptcha_enable_on_logincheckout' )
			: get_option( 'wbc_recaptcha_enable_on_guestcheckout' );

		if ( 'yes' !== $is_enabled ) {
			return $validation_errors;
		}

		// Verify nonce.
		$nonce_value = '';
		if ( isset( $_REQUEST['woocommerce-process-checkout-nonce'] ) && ! empty( $_REQUEST['woocommerce-process-checkout-nonce'] ) ) {
			$nonce_value = sanitize_text_field( wp_unslash( $_REQUEST['woocommerce-process-checkout-nonce'] ) );
		} elseif ( isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ) {
			$nonce_value = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
			$validation_errors->add( 'g-recaptcha_error', __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ) );
			return $validation_errors;
		}

		// Check transient to avoid duplicate verification.
		if ( 'yes' === get_transient( $nonce_value ) ) {
			return $validation_errors;
		}

		// Check payment request type.
		$payment_request_skip = get_option( 'wbc_recaptcha_login_recpacha_for_req_btn', 'no' );
		if ( 'no' === $payment_request_skip && isset( $_POST['payment_request_type'] ) ) {
			$payment_request_type = wc_clean( sanitize_text_field( wp_unslash( $_POST['payment_request_type'] ) ) );
			if ( in_array( $payment_request_type, array( 'apple_pay', 'payment_request_api' ), true ) ) {
				return $validation_errors;
			}
		}

		// Verify captcha using the service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( $context ) ) {
				// Get appropriate error message.
				$error_message = wbc_get_captcha_error_message( $context, 'invalid' );
				$validation_errors->add( 'g-recaptcha_error', $error_message );
			} else {
				// Set transient on success.
				$timeout = get_option( 'wbc_recaptcha_checkout_timeout', 3 );
				if ( $timeout > 0 ) {
					set_transient( $nonce_value, 'yes', ( $timeout * 60 ) );
				}
			}
		}

		return $validation_errors;
	}
}
