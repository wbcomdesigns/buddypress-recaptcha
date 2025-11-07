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
 * Plugin URI:        https://wbcomdesigns.com/downloads/recaptcha-for-buddypress/
 * Description:       Complete CAPTCHA solution with support for reCAPTCHA v2, v3, Cloudflare Turnstile, hCaptcha, and ALTCHA. Protect WordPress, WooCommerce, BuddyPress, bbPress, and 10+ popular form builders from spam and bots with a modular, easy-to-manage interface.
 * Version:           2.0.0
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
	define( 'RFB_PLUGIN_VERSION', '2.0.0' );
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
function activate_recaptcha_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-recaptcha-for-buddypress-activator.php';
	Recaptcha_For_BuddyPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class- recaptcha-for-buddypress-deactivator.php
 */
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
require plugin_dir_path( __FILE__ ) . 'includes/class-recaptcha-for-buddypress.php';

require plugin_dir_path( __FILE__ ) . 'bp-recaptcha-update-checker/plugin-update-checker.php';
	use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
	$myUpdateChecker = PucFactory::buildUpdateChecker(
		'https://demos.wbcomdesigns.com/exporter/free-plugins/buddypress-recaptcha.json',
		__FILE__, // Full path to the main plugin file or functions.php.
		'buddypress-recaptcha'
	);

/**
 * Plugin activation check.
 *
 * Note: Plugin works standalone with WordPress core forms.
 * Additional integrations (WooCommerce, BuddyPress, bbPress)
 * are automatically detected and enabled via modular settings system.
 *
 * @since 1.0.0
 * @return void
 */
function wb_recaptcha_plugin_activation() {
	register_activation_hook( __FILE__, 'activate_recaptcha_for_woocommerce' );
}
add_action( 'plugins_loaded', 'wb_recaptcha_plugin_activation' );


/**
 * Redirect to plugin settings page after activated.
 *
 * @param string $plugin Get a plugin base url.
 */
function wb_recaptcha_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin && ( class_exists( 'WooCommerce' ) || class_exists( 'BuddyPress' ) || class_exists( 'bbPress' ) ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
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
function run_recaptcha_for_woocommerce() {
	$plugin          = new Recaptcha_For_BuddyPress();
	$plugin->run();

}
run_recaptcha_for_woocommerce();

function wb_recaptcha_get_the_user_ip() {
	// Only trust REMOTE_ADDR as other headers can be spoofed
	$ipaddress = '';
	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		// Validate IP address
		if ( ! filter_var( $ipaddress, FILTER_VALIDATE_IP ) ) {
			$ipaddress = '';
		}
	}

	return $ipaddress;
}
