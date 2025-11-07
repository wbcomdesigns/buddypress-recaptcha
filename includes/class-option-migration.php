<?php
/**
 * Option Migration Class
 * Migrates old option names to new standardized naming convention
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles migration of option names to standardized format
 */
class WBC_Option_Migration {

	/**
	 * Migration version
	 */
	const MIGRATION_VERSION = '2.0.0';

	/**
	 * Migration option key
	 */
	const MIGRATION_KEY = 'wbc_option_migration_v2_completed';

	/**
	 * Old to new option name mapping
	 *
	 * @var array
	 */
	private static $option_map = array(
		// Fix typos: recapcha -> recaptcha
		'wbc_recapcha_version'                           => 'wbc_recaptcha_version',
		'wbc_recapcha_enable_on_wplogin'                 => 'wbc_recaptcha_enable_on_wplogin',
		'wbc_recapcha_enable_on_wpregister'              => 'wbc_recaptcha_enable_on_wpregister',
		'wbc_recapcha_enable_on_wplostpassword'          => 'wbc_recaptcha_enable_on_wplostpassword',
		'wbc_recapcha_enable_on_login'                   => 'wbc_recaptcha_enable_on_login',
		'wbc_recapcha_enable_on_signup'                  => 'wbc_recaptcha_enable_on_signup',
		'wbc_recapcha_enable_on_lostpassword'            => 'wbc_recaptcha_enable_on_lostpassword',
		'wbc_recapcha_enable_on_signup_bp'               => 'wbc_recaptcha_enable_on_signup_bp',
		'wbc_recapcha_enable_on_guestcheckout'           => 'wbc_recaptcha_enable_on_guestcheckout',
		'wbc_recapcha_enable_on_logincheckout'           => 'wbc_recaptcha_enable_on_logincheckout',
		'wbc_recapcha_enable_on_comment'                 => 'wbc_recaptcha_enable_on_comment',
		'wbc_recapcha_ip_to_skip_captcha'                => 'wbc_recaptcha_ip_to_skip_captcha',
		'wbc_recapcha_custom_wp_login_form_login'        => 'wbc_recaptcha_custom_wp_login_form_login',
		'wbc_recapcha__v3_custom_wp_login_form_login'    => 'wbc_recaptcha_v3_custom_wp_login_form_login',

		'recapcha_enable_on_bbpress_topic'               => 'wbc_recaptcha_enable_on_bbpress_topic',
		'recapcha_enable_on_bbpress_reply'               => 'wbc_recaptcha_enable_on_bbpress_reply',

		// Standardize prefixes: wc_settings_tab_ -> wbc_recaptcha_v2_
		'wc_settings_tab_recapcha_site_key'              => 'wbc_recaptcha_v2_site_key',
		'wc_settings_tab_recapcha_secret_key'            => 'wbc_recaptcha_v2_secret_key',
		'wc_settings_tab_demo_recapcha_v2_theme'         => 'wbc_recaptcha_v2_theme',
		'wc_settings_tab_demo_recapcha_v2_size'          => 'wbc_recaptcha_v2_size',

		// v3 options
		'wc_settings_tab_recapcha_site_key_v3'           => 'wbc_recaptcha_v3_site_key',
		'wc_settings_tab_recapcha_secret_key_v3'         => 'wbc_recaptcha_v3_secret_key',
		'wc_settings_tab_demo_recapcha_v3_theme'         => 'wbc_recaptcha_v3_theme',
		'wc_settings_tab_demo_recapcha_v3_badge'         => 'wbc_recaptcha_v3_badge',
		'wc_settings_tab_demo_recapcha_v3_score'         => 'wbc_recaptcha_v3_score',
	);

	/**
	 * Run migration
	 */
	public static function migrate() {
		// Check if migration already completed
		if ( get_option( self::MIGRATION_KEY ) ) {
			return;
		}

		$migrated_count = 0;

		foreach ( self::$option_map as $old_name => $new_name ) {
			$value = get_option( $old_name );

			// Only migrate if old option exists
			if ( false !== $value ) {
				// Set new option
				update_option( $new_name, $value );

				// Delete old option
				delete_option( $old_name );

				$migrated_count++;

				// Log migration if debug enabled
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( sprintf(
						'[BuddyPress reCAPTCHA] Migrated option: %s -> %s',
						$old_name,
						$new_name
					) );
				}
			}
		}

		// Mark migration as complete
		update_option( self::MIGRATION_KEY, self::MIGRATION_VERSION );

		// Log completion
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $migrated_count > 0 ) {
			error_log( sprintf(
				'[BuddyPress reCAPTCHA] Option migration completed. Migrated %d options.',
				$migrated_count
			) );
		}
	}

	/**
	 * Get new option name from old name
	 *
	 * @param string $old_name Old option name
	 * @return string|null New option name or null if not found
	 */
	public static function get_new_option_name( $old_name ) {
		return isset( self::$option_map[ $old_name ] ) ? self::$option_map[ $old_name ] : null;
	}

	/**
	 * Check if option has been migrated
	 *
	 * @param string $old_name Old option name
	 * @return bool
	 */
	public static function is_option_migrated( $old_name ) {
		if ( ! get_option( self::MIGRATION_KEY ) ) {
			return false;
		}

		$new_name = self::get_new_option_name( $old_name );
		if ( ! $new_name ) {
			return false;
		}

		// Check if new option exists and old doesn't
		return ( false !== get_option( $new_name ) ) && ( false === get_option( $old_name ) );
	}

	/**
	 * Get option value with backward compatibility
	 * Tries new name first, falls back to old name
	 *
	 * @param string $new_name New option name
	 * @param mixed  $default  Default value
	 * @return mixed
	 */
	public static function get_option_with_fallback( $new_name, $default = false ) {
		$value = get_option( $new_name );

		if ( false !== $value ) {
			return $value;
		}

		// Find old name
		$old_name = array_search( $new_name, self::$option_map, true );
		if ( $old_name ) {
			$value = get_option( $old_name );
			if ( false !== $value ) {
				return $value;
			}
		}

		return $default;
	}
}
