<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase -- Legacy filename convention.
/**
 * Option Name Compatibility Helper
 *
 * Handles backward compatibility for option names that have typos
 * Maps old option names with "recapcha" to correct "recaptcha" names
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get option with backward compatibility for typo.
 *
 * @param string $option_name The option name to retrieve.
 * @param mixed  $default Default value if option doesn't exist.
 * @return mixed Option value.
 */
function wbc_get_option_compat( $option_name, $default = false ) {
	// First try to get the option with correct spelling.
	$value = get_option( $option_name, null );

	// If not found and contains "recaptcha", try with typo "recapcha".
	if ( null === $value && false !== strpos( $option_name, 'recaptcha' ) ) {
		$typo_name = str_replace( 'recaptcha', 'recapcha', $option_name );
		$value     = get_option( $typo_name, null );

		// If found with typo, migrate to correct name.
		if ( null !== $value ) {
			update_option( $option_name, $value );
			// Optionally delete old option after migration.
			// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
			// delete_option( $typo_name );
		}
	}

	// If still not found, try the reverse (if looking for typo, check correct spelling).
	if ( null === $value && false !== strpos( $option_name, 'recapcha' ) ) {
		$correct_name = str_replace( 'recapcha', 'recaptcha', $option_name );
		$value        = get_option( $correct_name, null );
	}

	return ( null === $value ) ? $default : $value;
}

/**
 * Update option with backward compatibility.
 *
 * @param string $option_name The option name to update.
 * @param mixed  $value The new value.
 * @return bool Success status.
 */
function wbc_update_option_compat( $option_name, $value ) {
	// Update both versions for backward compatibility.
	$result = update_option( $option_name, $value );

	// If option contains "recaptcha", also update typo version for compatibility.
	if ( false !== strpos( $option_name, 'recaptcha' ) ) {
		$typo_name = str_replace( 'recaptcha', 'recapcha', $option_name );
		update_option( $typo_name, $value );
	}

	// If option contains "recapcha" (typo), also update correct version.
	if ( false !== strpos( $option_name, 'recapcha' ) ) {
		$correct_name = str_replace( 'recapcha', 'recaptcha', $option_name );
		update_option( $correct_name, $value );
	}

	return $result;
}

/**
 * Option name mapping for migration.
 * Maps old typo names to correct names.
 *
 * @return array Option name mapping.
 */
function wbc_get_option_name_mapping() {
	return array(
		// Enable options.
		'wbc_recapcha_enable_on_wplogin'             => 'wbc_recaptcha_enable_on_wplogin',
		'wbc_recapcha_enable_on_wpregister'          => 'wbc_recaptcha_enable_on_wpregister',
		'wbc_recapcha_enable_on_wplostpassword'      => 'wbc_recaptcha_enable_on_wplostpassword',
		'wbc_recapcha_enable_on_login'               => 'wbc_recaptcha_enable_on_login',
		'wbc_recapcha_enable_on_signup'              => 'wbc_recaptcha_enable_on_signup',
		'wbc_recapcha_enable_on_lostpassword'        => 'wbc_recaptcha_enable_on_lostpassword',
		'wbc_recapcha_enable_on_guestcheckout'       => 'wbc_recaptcha_enable_on_guestcheckout',
		'wbc_recapcha_enable_on_logincheckout'       => 'wbc_recaptcha_enable_on_logincheckout',
		'wbc_recapcha_enable_on_payfororder'         => 'wbc_recaptcha_enable_on_payfororder',
		'wbc_recapcha_enable_on_buddypress'          => 'wbc_recaptcha_enable_on_buddypress',
		'wbc_recapcha_enable_on_bbpress_topic'       => 'wbc_recaptcha_enable_on_bbpress_topic',
		'wbc_recapcha_enable_on_bbpress_reply'       => 'wbc_recaptcha_enable_on_bbpress_reply',
		'wbc_recapcha_enable_on_comment'             => 'wbc_recaptcha_enable_on_comment',
		'wbc_recapcha_enable_on_order_tracking'      => 'wbc_recaptcha_enable_on_order_tracking',

		// Key options.
		'wc_settings_tab_recapcha_site_key'          => 'wc_settings_tab_recaptcha_site_key',
		'wc_settings_tab_recapcha_secret_key'        => 'wc_settings_tab_recaptcha_secret_key',
		'wc_settings_tab_recapcha_site_key_v3'       => 'wc_settings_tab_recaptcha_site_key_v3',
		'wc_settings_tab_recapcha_secret_key_v3'     => 'wc_settings_tab_recaptcha_secret_key_v3',

		// Other settings.
		'wbc_recapcha_version'                       => 'wbc_recaptcha_version',
		'wbc_recapcha_ip_to_skip_captcha'            => 'wbc_recaptcha_ip_to_skip_captcha',
		'wbc_recapcha_no_conflict'                   => 'wbc_recaptcha_no_conflict',
		'wbc_recapcha_disable_submitbtn'             => 'wbc_recaptcha_disable_submitbtn',
		'wbc_recapcha_checkout_timeout'              => 'wbc_recaptcha_checkout_timeout',

		// Error messages.
		'wbc_recapcha_error_msg_captcha_blank'       => 'wbc_recaptcha_error_msg_captcha_blank',
		'wbc_recapcha_error_msg_captcha_invalid'     => 'wbc_recaptcha_error_msg_captcha_invalid',
		'wbc_recapcha_error_msg_captcha_no_response' => 'wbc_recaptcha_error_msg_captcha_no_response',
	);
}

/**
 * Run option name migration.
 * Migrates all options from typo names to correct names.
 *
 * @return array List of migrated option names.
 */
function wbc_migrate_option_names() {
	$mapping  = wbc_get_option_name_mapping();
	$migrated = array();

	foreach ( $mapping as $old_name => $new_name ) {
		// Get value from old option name.
		$value = get_option( $old_name, null );

		if ( null !== $value ) {
			// Save to new option name.
			update_option( $new_name, $value );
			$migrated[] = $old_name . ' -> ' . $new_name;

			// Keep old option for backward compatibility.
			// Uncomment to delete old options after migration.
			// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
			// delete_option( $old_name );
		}
	}

	// Mark migration as complete.
	if ( ! empty( $migrated ) ) {
		update_option( 'wbc_option_names_migrated', true );
		update_option( 'wbc_option_names_migration_log', $migrated );
	}

	return $migrated;
}

/**
 * Check if option name migration is needed.
 *
 * @return bool True if migration is needed.
 */
function wbc_needs_option_migration() {
	return ! get_option( 'wbc_option_names_migrated', false );
}

// Run migration on admin_init if needed.
add_action(
	'admin_init',
	function () {
		if ( wbc_needs_option_migration() ) {
			wbc_migrate_option_names();
		}
	},
	5
);
