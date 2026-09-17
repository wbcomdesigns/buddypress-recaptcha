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

if ( ! function_exists( 'wbc_skip_comment_captcha_for_current_user' ) ) {
	/**
	 * Whether the comment CAPTCHA is skipped for the current user.
	 *
	 * The comment form, the WooCommerce review form and the comment validator all
	 * call this, so a visitor is never shown a CAPTCHA that is not checked, or
	 * checked for one that was never shown.
	 *
	 * - Logged out: never skipped.
	 * - Can moderate comments: always skipped. They can approve any comment, so a
	 *   CAPTCHA on their own protects nothing.
	 * - Any other logged-in user: skipped when "Skip for Logged-in Users" is on.
	 *   Missing option means not skipped, so sites that never saved the setting
	 *   keep the behaviour they had.
	 *
	 * Developers can exempt more users (e.g. one role) with the existing
	 * `wbc_should_render_captcha` / `wbc_should_verify_captcha` filters; those run
	 * after this check, so they cannot re-require users this function exempts.
	 *
	 * @since 2.2.1
	 *
	 * @return bool True when the current user comments without a CAPTCHA.
	 */
	function wbc_skip_comment_captcha_for_current_user() { //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'moderate_comments' ) ) {
			return true;
		}

		return 'yes' === get_option( 'wbc_recaptcha_skip_comment_for_logged_in' );
	}
}

if ( ! function_exists( 'wbc_get_custom_captcha_error_option' ) ) {
	/**
	 * Resolve the admin-configured custom error message for an error type.
	 *
	 * The settings screen has written `wbc_recaptcha_error_msg_captcha_*` for some
	 * time, but every reader looked at the much older `wc_settings_tab_recapcha_*`
	 * (or the `*_v3`) keys, and nothing bridged the two — so customising an error
	 * message had no effect at all. This resolver is that bridge: it prefers the key
	 * the settings screen actually writes and falls back through the historic keys,
	 * so sites that set a message under any previous naming keep working.
	 *
	 * Lookup order per error type:
	 *   1. `wbc_recaptcha_error_msg_captcha_<type>`  — current settings-screen key.
	 *   2. `wbc_recapcha_error_msg_captcha_<type>`   — the "recapcha" typo spelling
	 *      that WBC_Settings_Migration writes when migrating older installs.
	 *   3. the version-specific legacy key (`*_v3` keys when the v3 flow is active).
	 *   4. `wc_settings_tab_recapcha_error_msg_captcha_<type>` — the original key,
	 *      and the only one any reader honoured before this bridge existed.
	 *
	 * @param string $error_type Type of error: 'blank', 'invalid', 'no_response'.
	 * @return string Custom message, or '' when the admin has not set one.
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wbc_get_custom_captcha_error_option( $error_type = 'invalid' ) {
		$current = array(
			'blank'       => 'wbc_recaptcha_error_msg_captcha_blank',
			'invalid'     => 'wbc_recaptcha_error_msg_captcha_invalid',
			'no_response' => 'wbc_recaptcha_error_msg_captcha_no_response',
		);

		$typo = array(
			'blank'       => 'wbc_recapcha_error_msg_captcha_blank',
			'invalid'     => 'wbc_recapcha_error_msg_captcha_invalid',
			'no_response' => 'wbc_recapcha_error_msg_captcha_no_response',
		);

		$legacy_v3 = array(
			'blank'       => 'wbc_recapcha_error_msg_captcha_blank_v3',
			'invalid'     => 'wbc_recapcha_error_msg_v3_invalid_captcha',
			'no_response' => 'wbc_recapcha_error_msg_captcha_no_response_v3',
		);

		$legacy = array(
			'blank'       => 'wc_settings_tab_recapcha_error_msg_captcha_blank',
			'invalid'     => 'wc_settings_tab_recapcha_error_msg_captcha_invalid',
			'no_response' => 'wc_settings_tab_recapcha_error_msg_captcha_no_response',
		);

		if ( ! isset( $current[ $error_type ] ) ) {
			$error_type = 'invalid';
		}

		$keys = array( $current[ $error_type ], $typo[ $error_type ] );

		// Only consult the v3-specific keys when the v3 flow is the active one.
		if ( 'v3' === get_option( 'wbc_recapcha_version', 'v2' ) ) {
			$keys[] = $legacy_v3[ $error_type ];
		}

		$keys[] = $legacy[ $error_type ];

		foreach ( $keys as $key ) {
			$value = get_option( $key );
			if ( is_string( $value ) && '' !== trim( $value ) ) {
				return $value;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wbc_get_captcha_error_message' ) ) {
	/**
	 * Get captcha error message for context and error type.
	 *
	 * Every integration builds its CAPTCHA error through this function, so the
	 * `wbc_captcha_error_message` filter below reaches all of them.
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

		/*
		 * Try to get a custom message from the active service.
		 *
		 * Guarded with is_callable(), NOT method_exists(): method_exists() is true for
		 * protected/private methods too, so when hCaptcha was the active service this
		 * call raised "Call to protected method ... from global scope" — a fatal on
		 * every failed verification, on every integration that reports an error.
		 * is_callable() reflects the visibility actually available from here.
		 */
		$message = '';
		if ( $service && is_callable( array( $service, 'get_error_message' ) ) ) {
			$message = $service->get_error_message( $context, $error_type );
		}

		// Fall back to the admin-configured message, resolved across the current and
		// all historic option keys.
		if ( empty( $message ) ) {
			$message = str_replace( '[recaptcha]', __( 'Security check', 'buddypress-recaptcha' ), wbc_get_custom_captcha_error_option( $error_type ) );
		}

		if ( empty( $message ) ) {
			$message = isset( $default_messages[ $error_type ] ) ? $default_messages[ $error_type ] : $default_messages['invalid'];
		}

		/**
		 * Filters the CAPTCHA error message shown to the visitor.
		 *
		 * Runs after the provider message, the admin-configured message (Advanced
		 * tab) and the built-in default are resolved, so it can override any of them
		 * per form.
		 *
		 * @since 2.2.1
		 *
		 * @param string $message    The resolved error message.
		 * @param string $context    Form context, e.g. 'wp_login', 'comment', 'woo_checkout_guest', 'cf7'.
		 * @param string $service_id Active provider id ('recaptcha-v2', 'recaptcha-v3', 'hcaptcha', 'turnstile', 'altcha'), or '' when none.
		 * @param string $error_type 'blank', 'invalid' or 'no_response'.
		 */
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return (string) apply_filters( 'wbc_captcha_error_message', $message, $context, $service ? $service->get_service_id() : '', $error_type );
	}
}
