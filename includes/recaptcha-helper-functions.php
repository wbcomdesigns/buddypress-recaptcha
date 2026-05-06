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

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wbc_get_recaptcha_site_key' ) ) {
	/**
	 * Get the site key (backward compatibility).
	 *
	 * @deprecated Use wbc_captcha_service_manager()->get_site_key()
	 * @return string Site key.
	 */
	function wbc_get_recaptcha_site_key() {
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			return wbc_captcha_service_manager()->get_site_key();
		}

		// Fallback to direct option access for backward compatibility.
		// Try v3 first, then v2.
		$site_key = trim( get_option( 'wc_settings_tab_recapcha_site_key_v3', '' ) );
		if ( empty( $site_key ) ) {
			$site_key = trim( get_option( 'wc_settings_tab_recapcha_site_key', '' ) );
		}
		return $site_key;
	}
}

if ( ! function_exists( 'wbc_get_recaptcha_secret_key' ) ) {
	/**
	 * Get the secret key (backward compatibility).
	 *
	 * @deprecated Use wbc_captcha_service_manager()->get_secret_key()
	 * @return string Secret key.
	 */
	function wbc_get_recaptcha_secret_key() {
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			return wbc_captcha_service_manager()->get_secret_key();
		}

		// Fallback to direct option access for backward compatibility.
		// Try v3 first, then v2.
		$secret_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key_v3', '' ) );
		if ( empty( $secret_key ) ) {
			$secret_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key', '' ) );
		}
		return $secret_key;
	}
}

if ( ! function_exists( 'wbc_is_recaptcha_configured' ) ) {
	/**
	 * Check if captcha is properly configured (backward compatibility).
	 *
	 * @deprecated Use wbc_captcha_service_manager()->is_configured()
	 * @return bool True if both site and secret keys are set.
	 */
	function wbc_is_recaptcha_configured() {
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			return wbc_captcha_service_manager()->is_configured();
		}

		// Fallback.
		$site_key   = wbc_get_recaptcha_site_key();
		$secret_key = wbc_get_recaptcha_secret_key();
		return ! empty( $site_key ) && ! empty( $secret_key );
	}
}

if ( ! function_exists( 'wbc_is_recaptcha_enabled' ) ) {
	/**
	 * Check if captcha is enabled for a specific context (backward compatibility).
	 *
	 * @deprecated Use wbc_captcha_service_manager()->is_enabled_for_context()
	 * @param string $context The context to check.
	 * @return bool
	 */
	function wbc_is_recaptcha_enabled( $context = '' ) {
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			return wbc_captcha_service_manager()->is_enabled_for_context( $context );
		}

		// Fallback - check common enable options.
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

if ( ! function_exists( 'wbc_get_no_conflict_option' ) ) {
	/**
	 * Get the no conflict option (backward compatibility).
	 *
	 * @deprecated Use service-specific options.
	 * @return string 'yes' or 'no'.
	 */
	function wbc_get_no_conflict_option() {
		// Check both v2 and v3 no conflict options.
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

if ( ! function_exists( 'wb_recaptcha_ip_matches_entry' ) ) {
	/**
	 * Match a user IP against a single whitelist entry.
	 *
	 * Supports three entry forms (admin docs / UI promise all three):
	 *  - Exact IPv4/IPv6 address (e.g. `192.168.1.10`).
	 *  - Inclusive range, dash-separated (e.g. `192.168.1.1-192.168.1.50`).
	 *  - CIDR block (e.g. `10.0.0.0/24`).
	 *
	 * Falls back to a strict equality check for entries we can't parse.
	 *
	 * @param string $user_ip The user's IP address.
	 * @param string $entry   The whitelist entry to compare against.
	 * @return bool
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wb_recaptcha_ip_matches_entry( $user_ip, $entry ) {
		$user_ip = trim( (string) $user_ip );
		$entry   = trim( (string) $entry );

		if ( '' === $user_ip || '' === $entry ) {
			return false;
		}

		// CIDR notation (IPv4 only — admins targeting v6 should list ranges).
		if ( false !== strpos( $entry, '/' ) ) {
			list( $subnet, $bits ) = array_pad( explode( '/', $entry, 2 ), 2, null );
			$bits                  = (int) $bits;
			$subnet_long           = ip2long( $subnet );
			$ip_long               = ip2long( $user_ip );
			if ( false === $subnet_long || false === $ip_long || $bits < 0 || $bits > 32 ) {
				return false;
			}
			$mask = ( 0 === $bits ) ? 0 : ( ~0 << ( 32 - $bits ) ) & 0xFFFFFFFF;
			return ( $ip_long & $mask ) === ( $subnet_long & $mask );
		}

		// Range notation: "start-end" (IPv4 only).
		if ( false !== strpos( $entry, '-' ) ) {
			list( $start, $end ) = array_pad( explode( '-', $entry, 2 ), 2, null );
			$start_long          = ip2long( trim( (string) $start ) );
			$end_long            = ip2long( trim( (string) $end ) );
			$ip_long             = ip2long( $user_ip );
			if ( false === $start_long || false === $end_long || false === $ip_long ) {
				return false;
			}
			if ( $start_long > $end_long ) {
				$tmp        = $start_long;
				$start_long = $end_long;
				$end_long   = $tmp;
			}
			return $ip_long >= $start_long && $ip_long <= $end_long;
		}

		// Plain address.
		return $user_ip === $entry;
	}
}

if ( ! function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) ) {
	/**
	 * Check if IP is whitelisted (backward compatibility).
	 *
	 * @return bool
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wb_recaptcha_restriction_recaptcha_by_ip() {
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			return wbc_captcha_service_manager()->is_ip_whitelisted();
		}

		// Fallback.
		$ip_list = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( empty( $ip_list ) ) {
			return false;
		}

		$user_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( empty( $user_ip ) ) {
			return false;
		}

		$entries = array_map( 'trim', explode( ',', $ip_list ) );
		foreach ( $entries as $entry ) {
			if ( '' === $entry ) {
				continue;
			}
			if ( wb_recaptcha_ip_matches_entry( $user_ip, $entry ) ) {
				return true;
			}
		}
		return false;
	}
}
