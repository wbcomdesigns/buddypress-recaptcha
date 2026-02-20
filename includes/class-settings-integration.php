<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Legacy filename convention.
/**
 * Settings Integration for Service Architecture
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles integration between admin settings and service architecture
 */
class WBC_Settings_Integration {

	/**
	 * Initialize settings integration
	 */
	public static function init() {
		// Add hooks for settings updates.
		add_action( 'update_option_wbc_captcha_service', array( __CLASS__, 'on_service_change' ), 10, 3 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_migrate_settings' ) );
	}

	/**
	 * Handle service change
	 *
	 * @param mixed  $old_value Old option value.
	 * @param mixed  $new_value New option value.
	 * @param string $option    Option name.
	 */
	public static function on_service_change( $old_value, $new_value, $option ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Clear any cached service instance.
		if ( function_exists( 'wbc_captcha_service_manager' ) && wbc_captcha_service_manager() ) {
			// Force reinitialization on next request.
			update_option( 'wbc_captcha_service_reinit', true );
		}
	}

	/**
	 * Maybe migrate settings from old structure
	 */
	public static function maybe_migrate_settings() {
		// Check if we've already migrated.
		if ( get_option( 'wbc_settings_migrated_2_0' ) ) {
			// Run service ID fix migration (for existing 2.0 installations).
			self::maybe_fix_service_id_underscores();
			return;
		}

		// Perform migration.
		self::migrate_settings();

		// Mark as migrated.
		update_option( 'wbc_settings_migrated_2_0', true );
	}

	/**
	 * Fix service ID underscores to hyphens (for existing installations)
	 *
	 * Prior to commit 1c8c962, service IDs were saved with underscores (recaptcha_v2)
	 * but service manager expected hyphens (recaptcha-v2). This migration fixes
	 * existing installations that have the old underscore format.
	 *
	 * @since 1.7.3
	 */
	private static function maybe_fix_service_id_underscores() {
		// Check if we've already done this fix.
		if ( get_option( 'wbc_service_id_hyphen_fix_done' ) ) {
			return;
		}

		$current_service = get_option( 'wbc_captcha_service' );

		// Map old underscore IDs to new hyphen IDs.
		$id_map = array(
			'recaptcha_v2' => 'recaptcha-v2',
			'recaptcha_v3' => 'recaptcha-v3',
		);

		// If current value uses underscore, update to hyphen.
		if ( isset( $id_map[ $current_service ] ) ) {
			update_option( 'wbc_captcha_service', $id_map[ $current_service ] );
		}

		// Mark as done.
		update_option( 'wbc_service_id_hyphen_fix_done', true );
	}

	/**
	 * Migrate settings to new structure
	 */
	private static function migrate_settings() {
		// Ensure service selection option exists.
		if ( false === get_option( 'wbc_captcha_service' ) ) {
			// Try to determine from old version setting.
			$version = get_option( 'wbc_recapcha_version', '' );
			if ( is_string( $version ) && 'v3' === strtolower( $version ) ) {
				$service = 'recaptcha-v3';
			} elseif ( is_string( $version ) && 'v2' === strtolower( $version ) ) {
				$service = 'recaptcha-v2';
			} else {
				// Check which keys are configured.
				$v3_site = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				if ( ! empty( $v3_site ) ) {
					$service = 'recaptcha-v3';
				} else {
					$service = 'recaptcha-v2';
				}
			}
			update_option( 'wbc_captcha_service', $service );

			// Remove old version option.
			delete_option( 'wbc_recapcha_version' );
		}

		// Migrate Turnstile keys if they exist in wrong format.
		$turnstile_migrations = array(
			'wbc_turnstile_site_key_old'   => 'wbc_turnstile_site_key',
			'wbc_turnstile_secret_key_old' => 'wbc_turnstile_secret_key',
		);

		foreach ( $turnstile_migrations as $old_option => $new_option ) {
			$old_value = get_option( $old_option );
			if ( false !== $old_value && false === get_option( $new_option ) ) {
				update_option( $new_option, $old_value );
				delete_option( $old_option );
			}
		}
	}

	/**
	 * Get service-specific option
	 *
	 * @param string $option_name Base option name.
	 * @param string $service_id  Service ID (optional, uses active service if not provided).
	 * @param mixed  $default     Default value.
	 * @return mixed
	 */
	public static function get_service_option( $option_name, $service_id = null, $default = false ) {
		if ( null === $service_id && function_exists( 'wbc_captcha_service_manager' ) ) {
			$service = wbc_captcha_service_manager()->get_active_service();
			if ( $service ) {
				$service_id = $service->get_service_id();
			}
		}

		if ( empty( $service_id ) ) {
			return $default;
		}

		// Build service-specific option name.
		$full_option_name = $option_name . '_' . $service_id;
		$value            = get_option( $full_option_name, $default );

		// If no service-specific option, try base option.
		if ( false === $value || empty( $value ) ) {
			$value = get_option( $option_name, $default );
		}

		return $value;
	}
}
