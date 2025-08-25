<?php
/**
 * Settings Migration Class for BuddyPress reCAPTCHA
 *
 * Handles migration from old settings structure to simplified structure
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Migration Class
 */
class WBC_Settings_Migration {
	
	/**
	 * Run the migration
	 *
	 * @return array Migration results
	 */
	public static function migrate() {
		$results = array(
			'success' => true,
			'migrated' => array(),
			'errors' => array(),
		);
		
		// Check if migration has already been done
		$migration_done = get_option( 'wbc_settings_migration_v2_completed' );
		if ( 'yes' === $migration_done ) {
			$results['message'] = 'Migration already completed.';
			return $results;
		}
		
		// Detect active service based on existing settings
		$active_service = self::detect_active_service();
		if ( $active_service ) {
			update_option( 'wbc_captcha_service', $active_service );
			$results['migrated'][] = 'Active service set to: ' . $active_service;
		}
		
		// Migrate integration settings
		self::migrate_integration_settings( $results );
		
		// Migrate appearance settings
		self::migrate_appearance_settings( $results );
		
		// Migrate advanced settings
		self::migrate_advanced_settings( $results );
		
		// Mark migration as completed
		update_option( 'wbc_settings_migration_v2_completed', 'yes' );
		
		// Enable simplified settings by default
		update_option( 'wbc_use_simplified_settings', 'yes' );
		
		$results['message'] = 'Settings migration completed successfully.';
		
		return $results;
	}
	
	/**
	 * Detect which service is currently active
	 *
	 * @return string|false Service ID or false if none detected
	 */
	private static function detect_active_service() {
		// Check for existing version setting
		$version = get_option( 'wbc_recapcha_version' );
		if ( 'v3' === $version ) {
			return 'recaptcha_v3';
		} elseif ( 'v2' === $version ) {
			return 'recaptcha_v2';
		}
		
		// Check for Turnstile keys
		$turnstile_site_key = get_option( 'wbc_turnstile_site_key' );
		$turnstile_secret_key = get_option( 'wbc_turnstile_secret_key' );
		if ( ! empty( $turnstile_site_key ) && ! empty( $turnstile_secret_key ) ) {
			return 'turnstile';
		}
		
		// Check for v3 keys
		$v3_site_key = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
		$v3_secret_key = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
		if ( ! empty( $v3_site_key ) && ! empty( $v3_secret_key ) ) {
			return 'recaptcha_v3';
		}
		
		// Check for v2 keys
		$v2_site_key = get_option( 'wc_settings_tab_recapcha_site_key' );
		$v2_secret_key = get_option( 'wc_settings_tab_recapcha_secret_key' );
		if ( ! empty( $v2_site_key ) && ! empty( $v2_secret_key ) ) {
			return 'recaptcha_v2';
		}
		
		// Default to v2 if nothing is detected
		return 'recaptcha_v2';
	}
	
	/**
	 * Migrate integration settings
	 *
	 * @param array $results Migration results array
	 */
	private static function migrate_integration_settings( &$results ) {
		// Map of old options to new (if different)
		$integration_mappings = array(
			// WordPress forms
			'wbc_recapcha_enable_on_wplogin' => 'wbc_recapcha_enable_on_wplogin',
			'wbc_recapcha_enable_on_wpregister' => 'wbc_recapcha_enable_on_wpregister',
			'wbc_recapcha_enable_on_wplostpassword' => 'wbc_recapcha_enable_on_wplostpassword',
			
			// WooCommerce forms
			'wbc_recapcha_enable_on_login' => 'wbc_recapcha_enable_on_login',
			'wbc_recapcha_enable_on_signup' => 'wbc_recapcha_enable_on_signup',
			'wbc_recapcha_enable_on_lostpassword' => 'wbc_recapcha_enable_on_lostpassword',
			'wbc_recapcha_enable_on_guestcheckout' => 'wbc_recapcha_enable_on_guestcheckout',
			'wbc_recapcha_enable_on_logincheckout' => 'wbc_recapcha_enable_on_logincheckout',
			'wbc_recapcha_enable_on_payfororder' => 'wbc_recapcha_enable_on_payfororder',
			
			// BuddyPress
			'wbc_recapcha_enable_on_buddypress' => 'wbc_recapcha_enable_on_buddypress',
			
			// bbPress
			'wbc_recapcha_enable_on_bbpress_topic' => 'wbc_recapcha_enable_on_bbpress_topic',
			'wbc_recapcha_enable_on_bbpress_reply' => 'wbc_recapcha_enable_on_bbpress_reply',
			
			// Other
			'wbc_recapcha_enable_on_comment' => 'wbc_recapcha_enable_on_comment',
			'wbc_recapcha_enable_on_order_tracking' => 'wbc_recapcha_enable_on_order_tracking',
		);
		
		foreach ( $integration_mappings as $old_key => $new_key ) {
			$value = get_option( $old_key );
			if ( false !== $value ) {
				// Keep the same value, just ensure it's stored
				update_option( $new_key, $value );
				$results['migrated'][] = 'Integration setting: ' . $old_key;
			}
		}
	}
	
	/**
	 * Migrate appearance settings
	 *
	 * @param array $results Migration results array
	 */
	private static function migrate_appearance_settings( &$results ) {
		// Migrate reCAPTCHA v2 appearance
		$theme = get_option( 'wbc_recaptcha_theme' );
		if ( false === $theme ) {
			// Check for v2-specific theme option
			$theme = get_option( 'wbc_recaptcha_v2_theme' );
		}
		if ( false !== $theme ) {
			update_option( 'wbc_recaptcha_theme', $theme );
			$results['migrated'][] = 'reCAPTCHA v2 theme';
		}
		
		$size = get_option( 'wbc_recaptcha_size' );
		if ( false === $size ) {
			// Check for v2-specific size option
			$size = get_option( 'wbc_recaptcha_v2_size' );
		}
		if ( false !== $size ) {
			update_option( 'wbc_recaptcha_size', $size );
			$results['migrated'][] = 'reCAPTCHA v2 size';
		}
		
		// Migrate reCAPTCHA v3 badge position
		$badge = get_option( 'wbc_recaptcha_v3_badge' );
		if ( false === $badge ) {
			// Check for alternative naming
			$badge = get_option( 'wbc_recaptcha_badge_v3' );
		}
		if ( false !== $badge ) {
			update_option( 'wbc_recaptcha_v3_badge', $badge );
			$results['migrated'][] = 'reCAPTCHA v3 badge position';
		}
		
		// Migrate language setting
		$language = get_option( 'wbc_recaptcha_language' );
		if ( false === $language ) {
			// Check for alternative naming
			$language = get_option( 'wbc_recaptcha_lang' );
		}
		if ( false !== $language ) {
			update_option( 'wbc_recaptcha_language', $language );
			$results['migrated'][] = 'Language setting';
		}
	}
	
	/**
	 * Migrate advanced settings
	 *
	 * @param array $results Migration results array
	 */
	private static function migrate_advanced_settings( &$results ) {
		// Migrate IP whitelist
		$ip_whitelist = get_option( 'wbc_recapcha_ip_to_skip_captcha' );
		if ( false !== $ip_whitelist ) {
			update_option( 'wbc_recapcha_ip_to_skip_captcha', $ip_whitelist );
			$results['migrated'][] = 'IP whitelist';
		}
		
		// Migrate no conflict mode
		$no_conflict = get_option( 'wbc_recapcha_no_conflict' );
		if ( false === $no_conflict ) {
			// Check v3 specific setting
			$no_conflict = get_option( 'wbc_recapcha_no_conflict_v3' );
		}
		if ( false !== $no_conflict ) {
			update_option( 'wbc_recapcha_no_conflict', $no_conflict );
			$results['migrated'][] = 'No conflict mode';
		}
		
		// Migrate disable submit button
		$disable_submit = get_option( 'wbc_recapcha_disable_submitbtn' );
		if ( false !== $disable_submit ) {
			update_option( 'wbc_recapcha_disable_submitbtn', $disable_submit );
			$results['migrated'][] = 'Disable submit button';
		}
		
		// Migrate checkout timeout
		$checkout_timeout = get_option( 'wbc_recapcha_checkout_timeout' );
		if ( false !== $checkout_timeout ) {
			update_option( 'wbc_recapcha_checkout_timeout', $checkout_timeout );
			$results['migrated'][] = 'Checkout timeout';
		}
		
		// Migrate error messages
		$error_mappings = array(
			'wbc_recapcha_error_msg_captcha_blank' => 'wbc_recapcha_error_msg_captcha_blank',
			'wbc_recapcha_error_msg_captcha_invalid' => 'wbc_recapcha_error_msg_captcha_invalid',
			'wbc_recapcha_error_msg_captcha_no_response' => 'wbc_recapcha_error_msg_captcha_no_response',
			'wbc_recapcha_error_msg_captcha_blank_v3' => 'wbc_recapcha_error_msg_captcha_blank',
			'wbc_recapcha_error_msg_v3_invalid_captcha' => 'wbc_recapcha_error_msg_captcha_invalid',
			'wbc_recapcha_error_msg_captcha_no_response_v3' => 'wbc_recapcha_error_msg_captcha_no_response',
		);
		
		foreach ( $error_mappings as $old_key => $new_key ) {
			$value = get_option( $old_key );
			if ( false !== $value && ! empty( $value ) ) {
				// Don't override if the new key already has a value
				$existing = get_option( $new_key );
				if ( false === $existing || empty( $existing ) ) {
					update_option( $new_key, $value );
					$results['migrated'][] = 'Error message: ' . $old_key;
				}
			}
		}
		
		// Migrate v3 score threshold
		$score_threshold = get_option( 'wbc_recaptcha_v3_score_threshold' );
		if ( false === $score_threshold ) {
			// Check alternative naming
			$score_threshold = get_option( 'wbc_recaptcha_score_threshold_v3' );
		}
		if ( false !== $score_threshold ) {
			update_option( 'wbc_recaptcha_v3_score_threshold', $score_threshold );
			$results['migrated'][] = 'reCAPTCHA v3 score threshold';
		}
	}
	
	/**
	 * Rollback migration (for debugging/development)
	 *
	 * @return bool Success status
	 */
	public static function rollback() {
		// Remove migration flag
		delete_option( 'wbc_settings_migration_v2_completed' );
		
		// Disable simplified settings
		update_option( 'wbc_use_simplified_settings', 'no' );
		
		return true;
	}
	
	/**
	 * Check if migration is needed
	 *
	 * @return bool True if migration is needed
	 */
	public static function is_migration_needed() {
		$migration_done = get_option( 'wbc_settings_migration_v2_completed' );
		return 'yes' !== $migration_done;
	}
}