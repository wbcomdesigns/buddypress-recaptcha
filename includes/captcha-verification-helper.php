<?php
/**
 * Captcha Verification Helper Functions
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wbc_verify_captcha' ) ) {
	/**
	 * Verify captcha for a given context using the service manager.
	 *
	 * @param string $context The context where captcha is being verified.
	 * @param array  $args    Optional arguments.
	 * @return bool True if verified, false on failure.
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wbc_verify_captcha( $context, $args = array() ) {
		// Check if service manager is available.
		if ( ! function_exists( 'wbc_captcha_service_manager' ) || ! wbc_captcha_service_manager() ) {
			// If service manager not available, pass validation.
			return true;
		}

		// Use service manager for verification.
		return wbc_captcha_service_manager()->verify( $context, null, $args );
	}
}

if ( ! function_exists( 'wbc_get_captcha_error_message' ) ) {
	/**
	 * Get captcha error message for context and error type.
	 *
	 * @param string $context    The context.
	 * @param string $error_type Type of error: 'blank', 'invalid', 'no_response'.
	 * @return string
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wbc_get_captcha_error_message( $context, $error_type = 'invalid' ) {
		// Get the active service.
		$service = null;
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			$service = wbc_captcha_service_manager()->get_active_service();
		}

		// Default messages.
		$default_messages = array(
			'blank'       => __( 'Please complete the security check to continue.', 'buddypress-recaptcha' ),
			'invalid'     => __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ),
			'no_response' => __( 'Unable to verify security check. Please refresh the page and try again.', 'buddypress-recaptcha' ),
		);

		// Try to get custom message from service.
		if ( $service && method_exists( $service, 'get_error_message' ) ) {
			$custom_message = $service->get_error_message( $context, $error_type );
			if ( ! empty( $custom_message ) ) {
				return $custom_message;
			}
		}

		// Fallback to checking options for backward compatibility.
		$version = get_option( 'wbc_recapcha_version', 'v2' );

		if ( 'v3' === $version ) {
			$option_map = array(
				'blank'       => 'wbc_recapcha_error_msg_captcha_blank_v3',
				'invalid'     => 'wbc_recapcha_error_msg_v3_invalid_captcha',
				'no_response' => 'wbc_recapcha_error_msg_captcha_no_response_v3',
			);
		} else {
			$option_map = array(
				'blank'       => 'wc_settings_tab_recapcha_error_msg_captcha_blank',
				'invalid'     => 'wc_settings_tab_recapcha_error_msg_captcha_invalid',
				'no_response' => 'wc_settings_tab_recapcha_error_msg_captcha_no_response',
			);
		}

		$custom_message = '';
		if ( isset( $option_map[ $error_type ] ) ) {
			$custom_message = get_option( $option_map[ $error_type ] );
		}

		if ( ! empty( $custom_message ) ) {
			// Replace [recaptcha] placeholder.
			$custom_message = str_replace( '[recaptcha]', __( 'Security check', 'buddypress-recaptcha' ), $custom_message );
			return $custom_message;
		}

		return isset( $default_messages[ $error_type ] ) ? $default_messages[ $error_type ] : $default_messages['invalid'];
	}
}
