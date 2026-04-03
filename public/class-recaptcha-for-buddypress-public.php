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
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
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

		// Get site key for localization.
		$site_key = $active_service->get_site_key();

		// Enqueue public script.
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recaptcha-for-buddypress-public.js', array( 'jquery' ), $this->version, false );

		// Localize script with necessary data.
		wp_localize_script(
			$this->plugin_name,
			'bpRecaptcha',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'site_key'   => $site_key,
				'service_id' => $active_service->get_service_id(),
			)
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
			// Handle no-conflict mode.
			$this->handle_no_conflict_mode();

			// Enqueue service-specific scripts.
			if ( method_exists( $active_service, 'enqueue_scripts' ) ) {
				$active_service->enqueue_scripts( 'global' );
			}
		}
	}

	/**
	 * Handle no-conflict mode to prevent conflicts with other captcha plugins
	 */
	private function handle_no_conflict_mode() {
		$no_conflict = get_option( 'wbc_recaptcha_no_conflict' );
		if ( 'yes' !== $no_conflict ) {
			return;
		}

		global $wp_scripts;
		if ( ! isset( $wp_scripts->queue ) ) {
			return;
		}

		$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha', 'cloudflare.com/turnstile' );

		foreach ( $wp_scripts->queue as $handle ) {
			// Skip our own scripts.
			if ( strpos( $handle, 'wbc-' ) === 0 || strpos( $handle, 'buddypress-recaptcha' ) !== false ) {
				continue;
			}

			if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
				continue;
			}

			foreach ( $urls as $url ) {
				if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					break;
				}
			}
		}
	}

	/**
	 * Add header metadata for IE compatibility
	 */
	public function woo_recaptcha_add_header_metadata_for_ie() {
		if ( $this->woo_recaptcha_check_is_ie_browser() ) {
			echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
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

	/**
	 * Check if browser is Internet Explorer
	 *
	 * @return bool
	 */
	public function woo_recaptcha_check_is_ie_browser() {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		return preg_match( '~MSIE|Internet Explorer~i', $user_agent ) || preg_match( '~Trident/7.0(.*)?; rv:11.0~', $user_agent );
	}

	/**
	 * Load language files
	 *
	 * Note: Since WordPress 6.7, translations are loaded just-in-time.
	 * The load_plugin_textdomain() call has been removed to comply with
	 * Plugin Check requirements.
	 */
	public function woo_load_lang_for_woo_recaptcha() {
		// Intentionally left empty.
		// WordPress 6.7+ handles translation loading automatically.
	}
}
