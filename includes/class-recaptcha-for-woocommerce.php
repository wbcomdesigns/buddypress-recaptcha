<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Recaptcha_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( ' RECAPTCHA_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version =  RECAPTCHA_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = ' recaptcha-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Recaptcha_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Recaptcha_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Recaptcha_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Recaptcha_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recaptcha-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recaptcha-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-recaptcha-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-recaptcha-for-woocommerce-public.php';

		/* Enqueue wbcom plugin settings file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';
		// LRL Class Files login, registration and lost password
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lrl-classes/Login.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lrl-classes/Regisrtation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lrl-classes/Lostpassword.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/WoocommerceRegister.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/WoocommerceLogin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/WoocommerceLostpassword.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/WoocommerceReviewOrder.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/WoocommerceRegisterPost.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/LostpasswordPost.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/WoocommerceProcessLoginErrors.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/WoocommerceAfterCheckoutValidation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/WoocommerceFilter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-order/WoocommerceOrder.php';

		$this->loader = new Recaptcha_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Recaptcha_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Recaptcha_For_Woocommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Recaptcha_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//Load setting in woocommerce setting tab
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wpc_admin_menu', 100 );
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'woocomm_load_custom_settings_tab' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wpc_add_admin_register_setting' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Recaptcha_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//Load style, js language
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'woo_load_lang_for_woo_recaptcha' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'woo_recaptcha_load_styles_and_js' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'woo_recaptcha_load_styles_and_js' );
		
		//Login, registration lost password
		$Login = new Login();
		$Regisrtation = new Regisrtation();
		$Lostpassword = new Lostpassword();
		$this->loader->add_action( 'login_form', $Login, 'woo_extra_wp_login_form' );
		$this->loader->add_action( 'register_form', $Regisrtation, 'woo_extra_wp_register_form' );
		$this->loader->add_action( 'lostpassword_form', $Lostpassword, 'woo_extra_wp_lostpassword_form' );

		// Woocommerce Login registration and lost form
		$WoocommerceRegister = new WoocommerceRegister();
		$WoocommerceLogin = new WoocommerceLogin();
		$WoocommerceLostpassword = new WoocommerceLostpassword();
		$this->loader->add_action( 'woocommerce_register_form', $WoocommerceRegister, 'woo_extra_register_fields' );
		$this->loader->add_action( 'woocommerce_login_form', $WoocommerceLogin, 'woo_extra_login_fields' );
		$this->loader->add_action( 'woocommerce_lostpassword_form', $WoocommerceLostpassword, 'woo_extra_lostpassword_fields' );
		
		//Woocommerce extra
		$WoocommerceReviewOrder =  new WoocommerceReviewOrder();
		$WoocommerceRegisterPost = new WoocommerceRegisterPost();
		$LostpasswordPost = new LostpasswordPost();
		$WoocommerceProcessLoginErrors = new WoocommerceProcessLoginErrors();
		$WoocommerceAfterCheckoutValidation = new WoocommerceAfterCheckoutValidation();
		$this->loader->add_action( 'woocommerce_review_order_before_submit', $WoocommerceReviewOrder, 'woo_extra_checkout_fields' );
		$this->loader->add_action( 'woocommerce_register_post', array($WoocommerceRegisterPost, 'woocomm_validate_signup_captcha'), 10, 3 );
		$this->loader->add_action( 'lostpassword_post', array($LostpasswordPost, 'woocomm_validate_lostpassword_captcha'), 10, 1 );
		$this->loader->add_action( 'woocommerce_process_login_errors', array($WoocommerceProcessLoginErrors, 'woocomm_validate_login_captcha'), 10, 3 );
		$this->loader->add_action( 'woocommerce_after_checkout_validation', array($WoocommerceAfterCheckoutValidation, 'woocomm_validate_checkout_captcha'), 10, 2 );

		//Woocommerce Filter
		$WoocommerceFilter = new WoocommerceFilter();
		$this->loader->add_filter( 'wp_authenticate_user', array($WoocommerceFilter, 'woo_wp_verify_login_captcha'), 10, 2 );
		$this->loader->add_filter( 'register_post', array($WoocommerceFilter, 'woo_verify_wp_register_captcha'), 10, 3 );
		$this->loader->add_filter( 'lostpassword_post', array($WoocommerceFilter, 'woo_verify_wp_lostpassword_captcha'), 10, 1 );
		$this->loader->add_filter( 'wpforms_frontend_recaptcha_noconflict', $WoocommerceFilter, 'woo_remove_no_conflict');
		$this->loader->add_filter( 'preprocess_comment', $WoocommerceFilter, 'woo_check_review_captcha');
		$this->loader->add_filter( 'preprocess_comment', $WoocommerceFilter, 'woo_check_comment_captcha');

		//Woocommerce Order
		$WoocommerceOrder = new WoocommerceOrder();
		$this->loader->add_action( 'woocommerce_pay_order_before_submit', $WoocommerceOrder, 'woo_extra_checkout_fields_pay_order' );
		$this->loader->add_action( 'woocommerce_before_pay_action', $WoocommerceOrder, 'woo_verify_pay_order_captcha' );
		$this->loader->add_action( 'woocommerce_payment_complete', $WoocommerceOrder, 'woo_payment_complete' );
		$this->loader->add_action( 'wp', $WoocommerceOrder, 'woo_verify_add_payment_method' );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_quantity', $WoocommerceOrder, 'woocommerce_payment_request_btn_captcha' );

		//Extra actions
		if ($plugin_public->isIEBrowser()) {
			$this->loader->add_action('wp_head', $plugin_public, 'add_header_metadata_for_IE');
			$this->loader->add_action('login_head', $plugin_public, 'add_header_metadata_for_IE');
			$this->loader->add_filter('script_loader_tag', array($plugin_public,'google_recaptcha_defer_parsing_of_js'), 10);
		}

		$reCapcha_version = get_option('i13_recapcha_version'); 
		if (''==$reCapcha_version) {
			$reCapcha_version='v2';
		}

		if ('v2'== strtolower($reCapcha_version)) {					
			$i13_recapcha_custom_wp_login_form_login=get_option('i13_recapcha_custom_wp_login_form_login');
			if ('yes'==$i13_recapcha_custom_wp_login_form_login) {
				$this->loader->add_filter('login_form_middle', array($WoocommerceLogin, 'woo_extra_login_fields'), 10, 2);
			}
		} else {
			$i13_recapcha__v3_custom_wp_login_form_login=get_option('i13_recapcha__v3_custom_wp_login_form_login');
			if ('yes'==$i13_recapcha__v3_custom_wp_login_form_login) {
				$this->loader->add_filter('login_form_middle', array($WoocommerceLogin, 'woo_extra_login_fields'), 10, 2);
			}					
		}

	}	

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Recaptcha_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
