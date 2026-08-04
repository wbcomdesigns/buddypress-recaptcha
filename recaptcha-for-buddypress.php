<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Recaptcha_For_BuddyPress
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom CAPTCHA Manager
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-recaptcha/
 * Description:       Complete CAPTCHA solution with support for reCAPTCHA v2, v3, Cloudflare Turnstile, hCaptcha, and ALTCHA. Protect WordPress, WooCommerce, BuddyPress, bbPress, and popular form builders from spam and bots with a modular, easy-to-manage interface.
 * Version:           2.1.0
 * Requires at least: 6.5
 * Tested up to:      7.0
 * Requires PHP:      8.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-recaptcha
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'RFB_PLUGIN_VERSION' ) ) {
	define( 'RFB_PLUGIN_VERSION', '2.1.0' );
}

if ( ! defined( 'RFB_PLUGIN_FILE' ) ) {
	define( 'RFB_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'RFB_PLUGIN_BASENAME' ) ) {
	define( 'RFB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'RFB_PLUGIN_URL' ) ) {
	define( 'RFB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'RFB_PLUGIN_PATH' ) ) {
	define( 'RFB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class- recaptcha-for-buddypress-activator.php
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound	
function activate_recaptcha_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-recaptcha-for-buddypress-activator.php';
	Recaptcha_For_BuddyPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class- recaptcha-for-buddypress-deactivator.php
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound	
function deactivate_recaptcha_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-recaptcha-for-buddypress-deactivator.php';
	Recaptcha_For_BuddyPress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_recaptcha_for_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_recaptcha_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
require plugin_dir_path( __FILE__ ) . 'includes/class-recaptcha-for-buddypress.php';

// ---------------------------------------------------------------------------
// EDD Software Licensing SDK — automatic updates from wbcomdesigns.com.
//
// The SDK is vendored at vendor/easy-digital-downloads/edd-sl-sdk. Other Wbcom
// plugins (BuddyNext, Member Blog, Listora, ...) bundle the same SDK: its
// versioned function guards and shared Versions registry make the double load
// safe — the newest bundled copy wins and every plugin registers its own item
// on `edd_sl_sdk_registry`. The free product ships with a preset key so updates
// work with zero customer setup. License state never gates functionality — it
// only authorises update downloads.
// ---------------------------------------------------------------------------

add_action(
	'edd_sl_sdk_registry',
	function ( $registry ) {
		$registry->register(
			array(
				'id'        => 'buddypress-recaptcha',
				'url'       => 'https://wbcomdesigns.com',
				'item_id'   => 1246648,
				'item_name' => 'reCaptcha for BuddyPress',
				'version'   => RFB_PLUGIN_VERSION,
				'file'      => RFB_PLUGIN_FILE,
				'license'   => 'd23187723b1423b54ef206a3588a1845',
				'type'      => 'plugin',
			)
		);
	}
);

// Load the vendored EDD SL SDK only when the package is COMPLETE. A partial
// build or extract that keeps the entry file but drops the SDK's src/ tree
// would fatal inside the SDK the moment it instantiates a src class. Guard on
// the source being present and degrade to "updates disabled" with a soft admin
// notice instead of a white screen — licensing only gates updates, never
// features, so CAPTCHA protection keeps working.
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$rfb_sdk_path = plugin_dir_path( __FILE__ ) . 'vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php';
if ( file_exists( $rfb_sdk_path )
	&& file_exists( plugin_dir_path( __FILE__ ) . 'vendor/easy-digital-downloads/edd-sl-sdk/src/Versions.php' ) ) {
	require_once $rfb_sdk_path;
} elseif ( is_admin() ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-warning"><p>'
				. esc_html__( 'Wbcom CAPTCHA Manager: the bundled update SDK is incomplete, so automatic updates are turned off. Reinstall the plugin from a complete package to restore them. Every other feature works normally.', 'buddypress-recaptcha' )
				. '</p></div>';
		}
	);
}

// Activate the preset key against the store once per site so update downloads
// are authorised. Admin-only; retries on the next admin load until the store
// confirms the activation.
add_action(
	'admin_init',
	function () {
		$preset_key = 'd23187723b1423b54ef206a3588a1845';
		$option     = 'buddypress-recaptcha_license_key';
		$activated  = 'buddypress_recaptcha_preset_activated';

		// Already activated for this domain — skip.
		if ( get_option( $activated ) ) {
			return;
		}

		// Store the key so the SDK can find it.
		update_option( $option, $preset_key, false );

		// Activate with the EDD store.
		$response = wp_remote_post(
			'https://wbcomdesigns.com',
			array(
				'timeout' => 15,
				'body'    => array(
					'edd_action' => 'activate_license',
					'license'    => $preset_key,
					'item_id'    => 1246648,
					'url'        => home_url(),
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( 'valid' === ( $body['license'] ?? '' ) ) {
				update_option( $activated, 1, false );
			}
		}
	}
);

/**
 * Redirect to plugin settings page after activated.
 *
 * @param string $plugin Get a plugin base url.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound	
function wb_recaptcha_activation_redirect_settings( $plugin ) {

	// No host-plugin gate: this is a general WordPress CAPTCHA manager and protects
	// core WP login / register / lost-password / comments on their own. Gating the
	// redirect on WooCommerce/BuddyPress/bbPress left plain-WordPress users on the
	// plugins list with no signpost to the settings screen.
	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && 'activate' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) && $plugin === $_REQUEST['plugin'] ) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress-recaptcha' ) );
			exit;
		}
	}
}
add_action( 'activated_plugin', 'wb_recaptcha_activation_redirect_settings' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound	
function run_recaptcha_for_woocommerce() {
	$plugin = new Recaptcha_For_BuddyPress();
	$plugin->run();
}
run_recaptcha_for_woocommerce();

/**
 * Get the user's IP address.
 *
 * @since 1.0.0
 * @return string The user's IP address or empty string.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound	
function wb_recaptcha_get_the_user_ip() {
	// Only trust REMOTE_ADDR as other headers can be spoofed.
	$ipaddress = '';
	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		// Validate IP address.
		if ( ! filter_var( $ipaddress, FILTER_VALIDATE_IP ) ) {
			$ipaddress = '';
		}
	}

	return $ipaddress;
}
