<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Legacy filename convention.
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
	 * One-time 1.x settings repair marker (2.2.4).
	 */
	const REPAIR_KEY = 'wbc_option_migration_1x_repair';

	/**
	 * Old to new option name mapping
	 *
	 * @var array
	 */
	private static $option_map = array(
		// Fix typos: recapcha -> recaptcha.
		'wbc_recapcha_version'                        => 'wbc_recaptcha_version',
		'wbc_recapcha_enable_on_wplogin'              => 'wbc_recaptcha_enable_on_wplogin',
		'wbc_recapcha_enable_on_wpregister'           => 'wbc_recaptcha_enable_on_wpregister',
		'wbc_recapcha_enable_on_wplostpassword'       => 'wbc_recaptcha_enable_on_wplostpassword',
		'wbc_recapcha_enable_on_login'                => 'wbc_recaptcha_enable_on_login',
		'wbc_recapcha_enable_on_signup'               => 'wbc_recaptcha_enable_on_signup',
		'wbc_recapcha_enable_on_lostpassword'         => 'wbc_recaptcha_enable_on_lostpassword',
		// 1.x stored BuddyPress sign-up as signup_bp; 2.x reads and saves `buddypress`.
		'wbc_recapcha_enable_on_signup_bp'            => 'wbc_recaptcha_enable_on_buddypress',
		'wbc_recapcha_enable_on_guestcheckout'        => 'wbc_recaptcha_enable_on_guestcheckout',
		'wbc_recapcha_enable_on_logincheckout'        => 'wbc_recaptcha_enable_on_logincheckout',
		'wbc_recapcha_enable_on_comment'              => 'wbc_recaptcha_enable_on_comment',
		'wbc_recapcha_ip_to_skip_captcha'             => 'wbc_recaptcha_ip_to_skip_captcha',
		'wbc_recapcha_custom_wp_login_form_login'     => 'wbc_recaptcha_custom_wp_login_form_login',
		'wbc_recapcha__v3_custom_wp_login_form_login' => 'wbc_recaptcha_v3_custom_wp_login_form_login',

		'recapcha_enable_on_bbpress_topic'            => 'wbc_recaptcha_enable_on_bbpress_topic',
		'recapcha_enable_on_bbpress_reply'            => 'wbc_recaptcha_enable_on_bbpress_reply',

		// Standardize prefixes: wc_settings_tab_ -> wbc_recaptcha_v2_.
		'wc_settings_tab_recapcha_site_key'           => 'wbc_recaptcha_v2_site_key',
		'wc_settings_tab_recapcha_secret_key'         => 'wbc_recaptcha_v2_secret_key',
		'wc_settings_tab_demo_recapcha_v2_theme'      => 'wbc_recaptcha_v2_theme',
		'wc_settings_tab_demo_recapcha_v2_size'       => 'wbc_recaptcha_v2_size',

		// V3 options.
		'wc_settings_tab_recapcha_site_key_v3'        => 'wbc_recaptcha_v3_site_key',
		'wc_settings_tab_recapcha_secret_key_v3'      => 'wbc_recaptcha_v3_secret_key',
		'wc_settings_tab_demo_recapcha_v3_theme'      => 'wbc_recaptcha_v3_theme',
		'wc_settings_tab_demo_recapcha_v3_badge'      => 'wbc_recaptcha_v3_badge',
		'wc_settings_tab_demo_recapcha_v3_score'      => 'wbc_recaptcha_v3_score',
	);

	/**
	 * Run migration.
	 */
	public static function migrate() {
		// Sites already on 2.x ran the rename before the 1.x keys were mapped
		// correctly; repair them once, whatever the rename state.
		self::repair_1x_settings();

		// Check if migration already completed.
		if ( get_option( self::MIGRATION_KEY ) ) {
			return;
		}

		$migrated_count = 0;

		foreach ( self::$option_map as $old_name => $new_name ) {
			$value = get_option( $old_name );

			// Only migrate if old option exists.
			if ( false !== $value ) {
				// Set new option.
				update_option( $new_name, $value );

				// Delete old option.
				delete_option( $old_name );

				++$migrated_count;

				// Log migration if debug enabled.
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						sprintf(
							'[BuddyPress reCAPTCHA] Migrated option: %s -> %s',
							$old_name,
							$new_name
						)
					);
				}
			}
		}

		// Mark migration as complete.
		update_option( self::MIGRATION_KEY, self::MIGRATION_VERSION );

		// Log completion.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $migrated_count > 0 ) {
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				sprintf(
					'[BuddyPress reCAPTCHA] Option migration completed. Migrated %d options.',
					$migrated_count
				)
			);
		}
	}

	/**
	 * Restore 1.x settings that the 2.0 rename left under keys nothing reads.
	 *
	 * Runs once (REPAIR_KEY). Each value is only added where the 2.x key is
	 * still missing, so a choice the owner made after upgrading always wins.
	 * Per-form v3 thresholds need no copy: the v3 service reads the 1.x keys.
	 */
	private static function repair_1x_settings() {
		if ( get_option( self::REPAIR_KEY ) ) {
			return;
		}

		// BuddyPress sign-up: 1.x key, or where the 2.0 rename wrongly put it.
		foreach ( array( 'wbc_recapcha_enable_on_signup_bp', 'wbc_recaptcha_enable_on_signup_bp' ) as $old ) {
			$value = get_option( $old );
			if ( false !== $value ) {
				add_option( 'wbc_recaptcha_enable_on_buddypress', $value );
				break;
			}
		}

		// reCAPTCHA v2 widget theme: 1.x stored one per form, 2.x has one. The
		// login form's choice is what most visitors saw, so it leads.
		if ( false === get_option( 'wbc_recaptcha_theme' ) ) {
			$theme_keys = array(
				'wbc_recapcha_wplogin_theme',
				'wbc_recapcha_login_theme',
				'wbc_recapcha_signup_theme_bp',
				'wbc_recapcha_wpregister_theme',
				'wbc_recapcha_signup_theme',
				'wbc_recapcha_wplostpassword_theme',
				'wbc_recapcha_lostpassword_theme',
				'wbc_recapcha_guestcheckout_theme',
				'wbc_recapcha_woo_comment_theme',
				'wc_settings_tab_demo_recapcha_v2_theme',
				'wbc_recaptcha_v2_theme',
			);
			foreach ( $theme_keys as $key ) {
				$theme = get_option( $key );
				if ( in_array( $theme, array( 'light', 'dark' ), true ) ) {
					add_option( 'wbc_recaptcha_theme', $theme );
					break;
				}
			}
		}

		update_option( self::REPAIR_KEY, '2.2.4', false );
	}

	/**
	 * When the owner changes the global v3 threshold, drop the 1.x per-form
	 * values so the new global applies to every form, as the field promises.
	 * Saving the same value (any Quick Setup save) leaves them alone.
	 *
	 * @param mixed $old_value Previous threshold.
	 * @param mixed $value     New threshold.
	 */
	public static function forget_1x_thresholds( $old_value, $value ) {
		if ( (string) $old_value === (string) $value ) {
			return;
		}
		$keys = array(
			'wbc_recapcha_wp_login_score_threshold_v3',
			'wbc_recapcha_wp_register_score_threshold_v3',
			'wbc_recapcha_wp_lost_password_score_threshold_v3',
			'wbc_recapcha_login_score_threshold_v3',
			'wbc_recapcha_signup_score_threshold_v3',
			'wbc_recapcha_lostpassword_score_threshold_v3',
			'wbc_recapcha_checkout_score_threshold_v3',
			'wbc_recapcha_signup_score_threshold_v3_bp',
			'wbc_recapcha_bbpress_topic_score_threshold_v3',
			'wbc_recapcha_bbpress_reply_score_threshold_v3',
		);
		foreach ( $keys as $key ) {
			delete_option( $key );
		}
	}

	/**
	 * Get new option name from old name.
	 *
	 * @param string $old_name Old option name.
	 * @return string|null New option name or null if not found.
	 */
	public static function get_new_option_name( $old_name ) {
		return isset( self::$option_map[ $old_name ] ) ? self::$option_map[ $old_name ] : null;
	}

	/**
	 * Check if option has been migrated.
	 *
	 * @param string $old_name Old option name.
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

		// Check if new option exists and old doesn't.
		return ( false !== get_option( $new_name ) ) && ( false === get_option( $old_name ) );
	}

	/**
	 * Get option value with backward compatibility.
	 * Tries new name first, falls back to old name.
	 *
	 * @param string $new_name New option name.
	 * @param mixed  $default  Default value.
	 * @return mixed
	 */
	public static function get_option_with_fallback( $new_name, $default = false ) {
		$value = get_option( $new_name );

		if ( false !== $value ) {
			return $value;
		}

		// Find old name.
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
