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
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
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
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_BuddyPress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Recaptcha_For_BuddyPress_Loader    $loader    Maintains and registers all hooks for the plugin.
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
			$this->version = RECAPTCHA_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-recaptcha';

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
	 * - Recaptcha_For_BuddyPress_Loader. Orchestrates the hooks of the plugin.
	 * - Recaptcha_For_BuddyPress_I18n. Defines internationalization functionality.
	 * - Recaptcha_For_BuddyPress_Admin. Defines all hooks for the admin area.
	 * - Recaptcha_For_BuddyPress_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recaptcha-for-buddypress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recaptcha-for-buddypress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-recaptcha-for-buddypress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-recaptcha-for-buddypress-public.php';

		/* Enqueue wbcom plugin settings file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';
		// LRL Class Files login, registration and lost password.
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

		// Buddy Press.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bp-classes/Regisrtationbp.php';

		$this->loader = new Recaptcha_For_BuddyPress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Recaptcha_For_BuddyPress_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Recaptcha_For_BuddyPress_I18n();

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

		$plugin_admin = new Recaptcha_For_BuddyPress_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Load setting in woocommerce setting tab.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'rfw_admin_menu', 100 );
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'woocomm_load_custom_settings_tab' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'rfw_add_admin_register_setting' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Recaptcha_For_BuddyPress_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Load style, js language.
		add_action( 'plugins_loaded', array( $plugin_public, 'woo_load_lang_for_woo_recaptcha' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'woo_recaptcha_load_styles_and_js' ), 9999 );
		add_action( 'login_enqueue_scripts', array( $plugin_public, 'woo_recaptcha_load_styles_and_js' ), 9999 );

		// Login, registration lost password.
		$login        = new Login();
		$regisrtation = new Regisrtation();
		$lostpassword = new Lostpassword();
		add_action( 'login_form', array( $login, 'woo_extra_wp_login_form' ) );
		add_action( 'register_form', array( $regisrtation, 'woo_extra_wp_register_form' ) );
		add_action( 'lostpassword_form', array( $lostpassword, 'woo_extra_wp_lostpassword_form' ) );

		// Buddypress.
		$regisrtation_bp = new Regisrtationbp();
		add_action( 'bp_before_registration_submit_buttons', array( $regisrtation_bp, 'woo_extra_bp_register_form' ), 36 );
		add_action( 'bp_signup_validate', array( $regisrtation_bp, 'innovage_validate_user_registration' ) );
		// add_action( 'bp_activity_entry_comments', array( $regisrtation_bp, 'form_field_bp' ) );
		// add_action( 'bp_activity_post_form_options', array( $regisrtation_bp, 'form_field_bp' ) );

		$regisrtation_bp = new Regisrtationbp();
		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $regisrtation_bp, 'form_field' ), 99 );
		add_action( 'bbp_new_topic_pre_extras', array( $regisrtation_bp, 'bbp_new_verify' ) );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $regisrtation_bp, 'form_field_replay' ), 99 );
		add_action( 'bbp_new_reply_pre_extras', array( $regisrtation_bp, 'bbp_reply_verify' ) );
		add_action( 'wp_enqueue_scripts', array( $regisrtation_bp, 'v2_checkbox_script' ) );

		// Woocommerce Login registration and lost form.
		$woocommerce_register      = new WoocommerceRegister();
		$woocommerce_login         = new WoocommerceLogin();
		$woocommerce_lost_password = new WoocommerceLostpassword();
		add_action( 'woocommerce_register_form', array( $woocommerce_register, 'woo_extra_register_fields' ) );
		add_action( 'woocommerce_login_form', array( $woocommerce_login, 'woo_extra_login_fields' ) );
		add_action( 'woocommerce_lostpassword_form', array( $woocommerce_lost_password, 'woo_extra_lostpassword_fields' ) );

		// Woocommerce extra.
		$woocommerce_review_order              = new WoocommerceReviewOrder();
		$woocommerce_register_post             = new WoocommerceRegisterPost();
		$lost_password_post                    = new LostpasswordPost();
		$woocommerce_process_login_errors      = new WoocommerceProcessLoginErrors();
		$woocommerce_after_checkout_validation = new WoocommerceAfterCheckoutValidation();
		add_action( 'woocommerce_review_order_before_submit', array( $woocommerce_review_order, 'woo_extra_checkout_fields' ) );
		add_action( 'woocommerce_register_post', array( $woocommerce_register_post, 'woocomm_validate_signup_captcha' ), 10, 3 );
		add_action( 'lostpassword_post', array( $lost_password_post, 'woocomm_validate_lostpassword_captcha' ), 10, 1 );
		add_action( 'woocommerce_process_login_errors', array( $woocommerce_process_login_errors, 'woocomm_validate_login_captcha' ), 10, 3 );
		add_action( 'woocommerce_after_checkout_validation', array( $woocommerce_after_checkout_validation, 'woocomm_validate_checkout_captcha' ), 10, 2 );

		// Woocommerce Filter.
		$woocommerce_filter = new WoocommerceFilter();
		add_filter( 'wp_authenticate_user', array( $woocommerce_filter, 'woo_wp_verify_login_captcha' ), 10, 2 );
		add_filter( 'register_post', array( $woocommerce_filter, 'woo_verify_wp_register_captcha' ), 10, 3 );
		add_filter( 'lostpassword_post', array( $woocommerce_filter, 'woo_verify_wp_lostpassword_captcha' ), 10, 1 );
		add_filter( 'wpforms_frontend_recaptcha_noconflict', array( $woocommerce_filter, 'woo_remove_no_conflict' ) );

		add_filter( 'preprocess_comment', array( $woocommerce_filter, 'woo_check_review_captcha' ) );
		add_filter( 'preprocess_comment', array( $woocommerce_filter, 'woo_check_comment_captcha' ) );

		// Woocommerce Order.
		$woocommerce_order = new WoocommerceOrder();
		add_action( 'woocommerce_pay_order_before_submit', array( $woocommerce_order, 'woo_extra_checkout_fields_pay_order' ) );
		add_action( 'woocommerce_before_pay_action', array( $woocommerce_order, 'woo_verify_pay_order_captcha' ) );
		add_action( 'woocommerce_payment_complete', array( $woocommerce_order, 'woo_payment_complete' ) );
		add_action( 'woocommerce_before_add_to_cart_quantity', array( $woocommerce_order, 'woocommerce_payment_request_btn_captcha' ) );
		add_action( 'comment_form', array( $woocommerce_order, 'woo_add_comment_form_captcha' ) );

		// Extra actions.
		if ( $plugin_public->woo_recaptcha_check_is_ie_browser() ) {
			add_action( 'wp_head', array( $plugin_public, 'woo_recaptcha_add_header_metadata_for_ie' ) );
			add_action( 'login_head', array( $plugin_public, 'woo_recaptcha_add_header_metadata_for_ie' ) );
			add_filter( 'script_loader_tag', array( $plugin_public, 'google_recaptcha_defer_parsing_of_js' ), 10 );
		}

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $re_capcha_version ) ) {
			$wbc_recapcha_custom_wp_login_form_login = get_option( 'wbc_recapcha_custom_wp_login_form_login' );
			if ( 'yes' === $wbc_recapcha_custom_wp_login_form_login ) {
				add_filter( 'login_form_middle', array( $woocommerce_login, 'woo_extra_login_fields' ), 10, 2 );
			}
		} else {
			$wbc_recapcha__v3_custom_wp_login_form_login = get_option( 'wbc_recapcha__v3_custom_wp_login_form_login' );
			if ( 'yes' === $wbc_recapcha__v3_custom_wp_login_form_login ) {
				add_filter( 'login_form_middle', array( $woocommerce_login, 'woo_extra_login_fields' ), 10, 2 );
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
	 * @return    Recaptcha_For_BuddyPress_Loader    Orchestrates the hooks of the plugin.
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
