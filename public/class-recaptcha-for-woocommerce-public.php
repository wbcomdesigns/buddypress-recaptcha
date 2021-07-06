<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_Woocommerce_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
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
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-woocommerce-public.css', array(), $this->version, 'all' );

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
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '+.js/recaptcha-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

	}

	public function woo_recaptcha_load_styles_and_js() {

		$reCapcha_version = get_option('i13_recapcha_version'); 
		if (''==$reCapcha_version) {
			$reCapcha_version='v2';
		}
			 
		if ('v2'== strtolower($reCapcha_version)) {
								$i13_recapcha_v2_lang=esc_html(get_option('i13_recapcha_v2_lang')); 
			//wp_register_style('i13-woo-styles', plugins_url('/public/css/styles.css', __FILE__), array(), '1.0');
			if (''!=$i13_recapcha_v2_lang) {
									
				wp_register_script('i13-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&hl=' . $i13_recapcha_v2_lang, array(), '1.0');
				wp_register_script('i13-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=explicit&hl=' . $i13_recapcha_v2_lang, array(), '2.0');
			} else {
				 wp_register_script('i13-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha', array(), '1.0');
				 wp_register_script('i13-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=explicit', array(), '2.0');
			}
			$is_enabled = get_option('i13_recapcha_enable_on_guestcheckout');
			  $is_enabled_on_payment_page = get_option('i13_recapcha_enable_on_addpaymentmethod');

			  $is_enabled_logincheckout = get_option('i13_recapcha_enable_on_logincheckout');
			  $i13_recapcha_enable_on_payfororder = get_option('i13_recapcha_enable_on_payfororder');
			  $i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict');

			if(function_exists('is_wc_endpoint_url')){
				if ('yes' == $is_enabled_on_payment_page && is_user_logged_in() && is_wc_endpoint_url(get_option('woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method')) ) {

					if ('yes'== $i13_recapcha_no_conflict) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
									wp_dequeue_script($handle);
									wp_deregister_script($handle);
									break;
								}
							}
						}
					}
					wp_enqueue_script('i13-woo-captcha');
				}
			}			

			if(function_exists('is_checkout')){
				if ('yes' == $is_enabled && ( !is_user_logged_in() || $i13_recapcha_enable_on_payfororder ) && is_checkout() ) {

					if ('yes'== $i13_recapcha_no_conflict) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
									wp_dequeue_script($handle);
									wp_deregister_script($handle);
									break;
								}
							}
						}
					}
								wp_enqueue_script('i13-woo-captcha-explicit');
				} else if ('yes' == ( $is_enabled_logincheckout || $i13_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

					if ('yes'== $i13_recapcha_no_conflict) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
									   wp_dequeue_script($handle);
									   wp_deregister_script($handle);
									   break;
								}
							}
						}
					}
					wp_enqueue_script('i13-woo-captcha-explicit');
				}
			}
			
		} else {
					
					
			$site_key = get_option('wc_settings_tab_recapcha_site_key_v3');
			wp_register_script('i13-woo-captcha-v3', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=' . esc_html($site_key), array('jquery'), '1.0');
			$is_enabled = get_option('i13_recapcha_enable_on_guestcheckout');
			$is_enabled_on_payment_page = get_option('i13_recapcha_enable_on_addpaymentmethod');
			$is_enabled_logincheckout = get_option('i13_recapcha_enable_on_logincheckout');
			$i13_recapcha_enable_on_payfororder = get_option('i13_recapcha_enable_on_payfororder');
			$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict_v3');


			if ('yes' == $is_enabled_on_payment_page && is_user_logged_in() && is_wc_endpoint_url(get_option('woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method')) ) {

				if ('yes'== $i13_recapcha_no_conflict) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
								wp_dequeue_script($handle);
								wp_deregister_script($handle);
								break;
							}
						}
					}
				}
				wp_enqueue_script('i13-woo-captcha-v3');
			}

			if ('yes' == $is_enabled && ( !is_user_logged_in() || $i13_recapcha_enable_on_payfororder ) && is_checkout() ) {

				if ('yes'== $i13_recapcha_no_conflict) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
								wp_dequeue_script($handle);
								wp_deregister_script($handle);
								break;
							}
						}
					}
				}
							wp_enqueue_script('i13-woo-captcha-v3');
			} else if ('yes' == ( $is_enabled_logincheckout || $i13_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

				if ('yes'== $i13_recapcha_no_conflict) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
								   wp_dequeue_script($handle);
								   wp_deregister_script($handle);
								   break;
							}
						}
					}
				}
				wp_enqueue_script('i13-woo-captcha-v3');
			}
					
					
		}
	}

	public function add_header_metadata_for_IE() {
		echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
	}

	public function google_recaptcha_defer_parsing_of_js( $url ) {					
		if (strpos($url, 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha')!==false ) {
			return str_replace(' src', ' defer src', $url);
		}
		return $url;    
	}

	public function isIEBrowser() {		
		if (!isset($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}
		$badBrowser = preg_match('~MSIE|Internet Explorer~i', sanitize_text_field($_SERVER['HTTP_USER_AGENT'])) || preg_match('~Trident/7.0(.*)?; rv:11.0~', sanitize_text_field($_SERVER['HTTP_USER_AGENT']));	
		return $badBrowser;
	}

	public function woo_load_lang_for_woo_recaptcha() {

		load_plugin_textdomain('recaptcha-for-woocommerce', false, basename(dirname(__FILE__)) . '/languages/');
		//add_filter('map_meta_cap', array($this, 'map_i13_woo_map_woo_product_slider_meta_caps'), 10, 4);
	}
}
