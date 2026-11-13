<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_BuddyPress_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-buddypress-public.css', array(), $this->version, 'all' );

		/*
		 * Makes the provider widget follow the site's dark mode. The widget renders in a
		 * third-party iframe that CSS cannot reach, so its colour scheme can only be set
		 * through the data-theme attribute - and the site's dark mode is a client-side
		 * toggle, so it has to be done in the browser. Loaded in the head, before the
		 * provider script reaches the element.
		 */
		wp_enqueue_script(
			'wbc-captcha-theme',
			plugin_dir_url( __FILE__ ) . 'js/wbc-captcha-theme.js',
			array(),
			$this->version,
			false
		);
	}

	/**
	 * Load captcha scripts and styles dynamically
	 *
	 * @since    1.0.0
	 */
	public function woo_recaptcha_load_styles_and_js() {
		// Get service manager.
		if ( ! function_exists( 'wbc_captcha_service_manager' ) ) {
			return;
		}

		$service_manager = wbc_captcha_service_manager();
		if ( ! $service_manager ) {
			return;
		}

		$active_service = $service_manager->get_active_service();
		if ( ! $active_service || ! $active_service->is_configured() ) {
			return;
		}

		// Check if we need to load scripts on specific pages.
		$should_load = false;

		// Check WooCommerce pages.
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$is_enabled_guest = get_option( 'wbc_recaptcha_enable_on_guestcheckout' );
			$is_enabled_login = get_option( 'wbc_recaptcha_enable_on_logincheckout' );

			if ( ( ! is_user_logged_in() && 'yes' === $is_enabled_guest ) ||
				( is_user_logged_in() && 'yes' === $is_enabled_login ) ) {
				$should_load = true;
			}
		}

		// Check My Account pages.
		if ( function_exists( 'is_wc_endpoint_url' ) && is_user_logged_in() ) {
			if ( is_wc_endpoint_url( get_option( 'woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method' ) ) ) {
				$should_load = true;
			}
		}

		// Check bbPress pages.
		if ( function_exists( 'is_singular' ) ) {
			if ( is_singular( 'topic' ) && 'yes' === get_option( 'wbc_recaptcha_enable_on_bbpress_reply' ) ) {
				$should_load = true;
			}
			if ( is_singular( 'forum' ) && 'yes' === get_option( 'wbc_recaptcha_enable_on_bbpress_topic' ) ) {
				$should_load = true;
			}
		}

		// Load scripts if needed.
		if ( $should_load ) {
			// Enqueue service-specific scripts.
			if ( method_exists( $active_service, 'enqueue_scripts' ) ) {
				$active_service->enqueue_scripts( 'global' );
			}
		}
	}

	/**
	 * Add defer attribute to recaptcha scripts
	 *
	 * @param  string $url Script URL.
	 * @return string Modified URL.
	 */
	public function google_recaptcha_defer_parsing_of_js( $url ) {
		if ( strpos( $url, 'recaptcha/api.js' ) !== false || strpos( $url, 'turnstile/v0/api.js' ) !== false ) {
			return str_replace( ' src', ' defer src', $url );
		}
		return $url;
	}
}
