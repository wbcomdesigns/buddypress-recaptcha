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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_BuddyPress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_BuddyPress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-buddypress-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_BuddyPress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_BuddyPress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recaptcha-for-buddypress-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Function load the recaptcha css and js.
	 *
	 * @return void
	 */
	public function woo_recaptcha_load_styles_and_js() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $woo_recaptcha_version ) ) {
								$wbc_recapcha_v2_lang = esc_html( get_option( 'wbc_recapcha_v2_lang' ) );
			if ( '' !== $wbc_recapcha_v2_lang ) {
				wp_register_script( 'wbc-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha&hl=' . $wbc_recapcha_v2_lang, array(), '1.0', true );
				wp_register_script( 'wbc-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha&render=explicit&hl=' . $wbc_recapcha_v2_lang, array(), '2.0', true );
			} else {
				wp_register_script( 'wbc-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha', array(), '1.0', true );
				wp_register_script( 'wbc-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha&render=explicit', array(), '2.0', true );
			}
			$is_enabled                         = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
			$is_enabled_logincheckout           = get_option( 'wbc_recapcha_enable_on_logincheckout' );
			$wbc_recapcha_enable_on_payfororder = get_option( 'wbc_recapcha_enable_on_payfororder' );
			$wbc_recapcha_no_conflict           = get_option( 'wbc_recapcha_no_conflict' );

			if ( function_exists( 'is_wc_endpoint_url' ) ) {
				if ( is_user_logged_in() && is_wc_endpoint_url( get_option( 'woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method' ) ) ) {

					if ( 'yes' === $wbc_recapcha_no_conflict ) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
									wp_dequeue_script( $handle );
									wp_deregister_script( $handle );
									break;
								}
							}
						}
					}
					wp_enqueue_script( 'wbc-woo-captcha' );
				}
			}

			if ( function_exists( 'is_checkout' ) ) {
				if ( 'yes' === $is_enabled && ( ! is_user_logged_in() || $wbc_recapcha_enable_on_payfororder ) && is_checkout() ) {

					if ( 'yes' === $wbc_recapcha_no_conflict ) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
									wp_dequeue_script( $handle );
									wp_deregister_script( $handle );
									break;
								}
							}
						}
					}
								wp_enqueue_script( 'wbc-woo-captcha-explicit' );
				} elseif ( 'yes' === ( $is_enabled_logincheckout || $wbc_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

					if ( 'yes' === $wbc_recapcha_no_conflict ) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
									wp_dequeue_script( $handle );
									wp_deregister_script( $handle );
									break;
								}
							}
						}
					}
					wp_enqueue_script( 'wbc-woo-captcha-explicit' );
				}
			}
		} else {

			$site_key = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
			wp_register_script( 'wbc-woo-captcha-v3', 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha&render=' . esc_html( $site_key ), array( 'jquery' ), '1.0', true );
			$is_enabled                         = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
			$is_enabled_logincheckout           = get_option( 'wbc_recapcha_enable_on_logincheckout' );
			$wbc_recapcha_enable_on_payfororder = get_option( 'wbc_recapcha_enable_on_payfororder' );
			$wbc_recapcha_no_conflict           = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_is_enabled_bbpress_replay      = get_option( 'wbc_recapcha_enable_on_bbpress_replay' );
			$wbc_is_enabled_bbpress_topic       = get_option( 'wbc_recapcha_enable_on_bbpress_topic' );
			if ( is_user_logged_in() && function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( get_option( 'woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method' ) ) ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'wbc-woo-captcha-v3' );
			}

			if ( 'yes' === $is_enabled && ( ! is_user_logged_in() || $wbc_recapcha_enable_on_payfororder ) && is_checkout() ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
							wp_enqueue_script( 'wbc-woo-captcha-v3' );
			} elseif ( 'yes' === ( $is_enabled_logincheckout || $wbc_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'wbc-woo-captcha-v3' );
			} elseif ( ( 'yes' === $wbc_is_enabled_bbpress_replay ) && is_user_logged_in() && is_singular( 'topic' ) ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'wbc-woo-captcha-v3' );
			} elseif ( ( 'yes' === $wbc_is_enabled_bbpress_topic ) && is_user_logged_in() && is_singular( 'forum' ) ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'wbc-woo-captcha-v3' );
			}
		}
	}

	/**
	 * Function added the header meta data.
	 */
	public function woo_recaptcha_add_header_metadata_for_ie() {
		echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
	}

	/**
	 * Function return's the goole recaptcha api url.
	 *
	 * @param  string $url Get a Google recaptcha api url.
	 * @return string $url Return the URL.
	 */
	public function google_recaptcha_defer_parsing_of_js( $url ) {
		if ( strpos( $url, 'https://www.google.com/recaptcha/api.js?from=wbc_recaptcha' ) !== false ) {
			return str_replace( ' src', ' defer src', $url );
		}
		return $url;
	}

	/**
	 * Function checks the browser is IE or not.
	 */
	public function woo_recaptcha_check_is_ie_browser() {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		$bad_browser = preg_match( '~MSIE|Internet Explorer~i', sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) || preg_match( '~Trident/7.0(.*)?; rv:11.0~', sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) );
		return $bad_browser;
	}

	/**
	 * Function load the language file.
	 */
	public function woo_load_lang_for_woo_recaptcha() {

		load_plugin_textdomain( 'buddypress-recaptcha', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}
