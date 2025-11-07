<?php
/**
 * Helper functions for BuddyPress reCAPTCHA plugin
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 * 
 * These functions provide backward compatibility for existing code.
 * New code should use the service manager directly:
 * wbc_captcha_service_manager()->render( 'context' );
 * wbc_captcha_service_manager()->verify( 'context' );
 */

/**
 * Get the site key (backward compatibility)
 *
 * @deprecated Use wbc_captcha_service_manager()->get_site_key()
 * @return string Site key
 */
if ( ! function_exists( 'wbc_get_recaptcha_site_key' ) ) {
function wbc_get_recaptcha_site_key() {
	if ( function_exists( 'wbc_captcha_service_manager' ) ) {
		return wbc_captcha_service_manager()->get_site_key();
	}
	
	// Fallback to direct option access for backward compatibility
	// Try v3 first, then v2
	$site_key = trim( get_option( 'wc_settings_tab_recapcha_site_key_v3', '' ) );
	if ( empty( $site_key ) ) {
		$site_key = trim( get_option( 'wc_settings_tab_recapcha_site_key', '' ) );
	}
	return $site_key;
}
}

/**
 * Get the secret key (backward compatibility)
 *
 * @deprecated Use wbc_captcha_service_manager()->get_secret_key()
 * @return string Secret key
 */
if ( ! function_exists( 'wbc_get_recaptcha_secret_key' ) ) {
function wbc_get_recaptcha_secret_key() {
	if ( function_exists( 'wbc_captcha_service_manager' ) ) {
		return wbc_captcha_service_manager()->get_secret_key();
	}
	
	// Fallback to direct option access for backward compatibility
	// Try v3 first, then v2
	$secret_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key_v3', '' ) );
	if ( empty( $secret_key ) ) {
		$secret_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key', '' ) );
	}
	return $secret_key;
}
}

/**
 * Check if captcha is properly configured (backward compatibility)
 *
 * @deprecated Use wbc_captcha_service_manager()->is_configured()
 * @return bool True if both site and secret keys are set
 */
if ( ! function_exists( 'wbc_is_recaptcha_configured' ) ) {
function wbc_is_recaptcha_configured() {
	if ( function_exists( 'wbc_captcha_service_manager' ) ) {
		return wbc_captcha_service_manager()->is_configured();
	}
	
	// Fallback
	$site_key = wbc_get_recaptcha_site_key();
	$secret_key = wbc_get_recaptcha_secret_key();
	return ! empty( $site_key ) && ! empty( $secret_key );
}
}

/**
 * Check if captcha is enabled for a specific context (backward compatibility)
 *
 * @deprecated Use wbc_captcha_service_manager()->is_enabled_for_context()
 * @param string $context The context to check
 * @return bool
 */
if ( ! function_exists( 'wbc_is_recaptcha_enabled' ) ) {
function wbc_is_recaptcha_enabled( $context = '' ) {
	if ( function_exists( 'wbc_captcha_service_manager' ) ) {
		return wbc_captcha_service_manager()->is_enabled_for_context( $context );
	}
	
	// Fallback - check common enable options
	$enable_options = array(
		'wp_login'        => 'wbc_recapcha_enable_on_wplogin',
		'wp_register'     => 'wbc_recapcha_enable_on_wpregister',
		'wp_lostpassword' => 'wbc_recapcha_enable_on_wplostpassword',
		'woo_login'       => 'wbc_recapcha_enable_on_login',
		'woo_register'    => 'wbc_recapcha_enable_on_signup',
		'bp_register'     => 'wbc_recapcha_enable_on_buddypress',
	);
	
	if ( isset( $enable_options[ $context ] ) ) {
		return 'yes' === get_option( $enable_options[ $context ] );
	}
	
	return false;
}
}

/**
 * Get the no conflict option (backward compatibility)
 *
 * @deprecated Use service-specific options
 * @return string 'yes' or 'no'
 */
if ( ! function_exists( 'wbc_get_no_conflict_option' ) ) {
function wbc_get_no_conflict_option() {
	// Check both v2 and v3 no conflict options
	$no_conflict = get_option( 'wbc_recapcha_no_conflict' );
	if ( 'yes' === $no_conflict ) {
		return 'yes';
	}
	
	$no_conflict = get_option( 'wbc_recapcha_no_conflict_v3' );
	if ( 'yes' === $no_conflict ) {
		return 'yes';
	}
	
	return 'no';
}
}

/**
 * Check if IP is whitelisted (backward compatibility)
 *
 * @return bool
 */
if ( ! function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) ) {
function wb_recaptcha_restriction_recaptcha_by_ip() {
	if ( function_exists( 'wbc_captcha_service_manager' ) ) {
		return wbc_captcha_service_manager()->is_ip_whitelisted();
	}
	
	// Fallback
	$ip_list = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
	if ( empty( $ip_list ) ) {
		return false;
	}
	
	$user_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( empty( $user_ip ) ) {
		return false;
	}
	
	$ip_array = array_map( 'trim', explode( ',', $ip_list ) );
	return in_array( $user_ip, $ip_array, true );
}
}