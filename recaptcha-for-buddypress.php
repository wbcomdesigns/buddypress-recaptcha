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
 * Plugin Name:       Wbcom Designs - BuddyPress reCaptcha
 * Plugin URI:        https://wbcomdesigns.com/downloads/recaptcha-for-buddypress/
 * Description:       Buddypress reCaptcha is the best solution for spam and BOT protection that provides an all-in-one captcha for Buddypress, WordPress, bbPress and woo-commerce. Various control options are provided to manage the captcha on required places.
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

/**
 * This Function checks the Woocommerce, BuddyPress and bbpress plugin is active or not.
 *
 * @return void
 */
function wb_recaptcha_required_plugin_activation_check() {
	if ( class_exists( 'WooCommerce' ) || class_exists( 'BuddyPress' ) || class_exists( 'bbPress' ) ) {
		register_activation_hook( __FILE__, 'activate_recaptcha_for_woocommerce' );
	} else {
		add_action( 'admin_notices', 'wb_recaptcha_required_plugin_admin_notice' );
		add_action( 'admin_init', 'wb_recaptcha_existing_checkin_plugin' );

	}
}
add_action( 'plugins_loaded', 'wb_recaptcha_required_plugin_activation_check' );

/**
 * Required plugins admin notice for reCaptcha for WooCommerce.
 */
function wb_recaptcha_required_plugin_admin_notice() {
	$wb_recaptcha   = __( 'BuddyPress reCAPTCHA Security', 'buddypress-recaptcha' );
	$woo_plugin     = __( 'WooCommerce', 'buddypress-recaptcha' );
	$bp_plugin      = __( 'BuddyPress', 'buddypress-recaptcha' );
	$bbpress_plugin = __( 'bbPress', 'buddypress-recaptcha' );

	echo '<div class="error"><p>'
	/* translators: %1s: reCaptcha for WooCommerce, %2$s: WooCommerce, %3$s: BuddyPress, %4$s: bbPress,    */
	. sprintf( esc_html__( 'To protect your site from spam, %1$s needs at least one of these plugins: %2$s, %3$s, or %4$s. Please install and activate one to continue.', 'buddypress-recaptcha' ), '<strong>' . esc_html( $wb_recaptcha ) . '</strong>', '<strong>' . esc_html( $woo_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>', '<strong>' . esc_html( $bbpress_plugin ) . '</strong>' )
	. '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}


/**
 * Function to remove buddypress recaptcha plugin if already exist.
 *
 * @since 1.0.0
 */
function wb_recaptcha_existing_checkin_plugin() {
	$wb_recaptcha_plugin = plugin_dir_path( __DIR__ ) . 'buddypress-recaptcha/buddypress-recaptcha';
	// Check to see if plugin is already active.
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}

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
