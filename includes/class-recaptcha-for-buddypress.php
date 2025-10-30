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
		if ( defined( 'RFB_PLUGIN_VERSION' ) ) {
			$this->version = RFB_PLUGIN_VERSION;
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
		 * Service architecture classes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/captcha-service-interface.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-captcha-service-base.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-captcha-service-manager.php';

		/**
		 * Helper functions for consistent reCAPTCHA version handling.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/recaptcha-helper-functions.php';

		/**
		 * Option name compatibility for handling typos in option names.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/option-name-compatibility.php';

		/**
		 * Captcha verification helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/captcha-verification-helper.php';

		/**
		 * Settings integration for service architecture.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-settings-integration.php';

		/**
		 * Settings migration for simplified settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-settings-migration.php';

		/**
		 * Option name migration for standardized naming.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-option-migration.php';

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lrl-classes/Registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lrl-classes/Lostpassword.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/Woocommerce_Register.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/Woocommerce_Login.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-lrl-classes/Woocommerce_Lostpassword.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_Review_Order.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_Register_Post.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/LostpasswordPost.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_Process_Login_Errors.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_After_Checkout_Validation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_Filter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/woocommerce-extra/Woocommerce_Order.php';

		// Buddy Press.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bp-classes/Registrationbp.php';

		// bbPress.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bbPress/class-wbc-bbpress-reply-recaptcha.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bbPress/class-wbc-bbpress-topic-recaptcha.php';

		$this->loader = new Recaptcha_For_BuddyPress_Loader();

		// Check and run settings migration if needed
		if ( class_exists( 'WBC_Settings_Migration' ) && WBC_Settings_Migration::is_migration_needed() ) {
			add_action( 'admin_init', array( 'WBC_Settings_Migration', 'migrate' ), 5 );
		}

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

		// Run option migration
		$this->loader->add_action( 'plugins_loaded', $this, 'run_option_migration' );

	}

	/**
	 * Run option name migration
	 *
	 * @since 2.0.0
	 */
	public function run_option_migration() {
		WBC_Option_Migration::migrate();
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
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wbcom_hide_all_admin_notices_from_setting_page' );
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
		if ( ! class_exists( 'Login' ) ) {
			error_log( 'BuddyPress reCAPTCHA: Login class not found' );
			return;
		}
		if ( ! class_exists( 'Registration' ) ) {
			error_log( 'BuddyPress reCAPTCHA: Registration class not found' );
			return;
		}
		if ( ! class_exists( 'Lostpassword' ) ) {
			error_log( 'BuddyPress reCAPTCHA: Lostpassword class not found' );
			return;
		}
		
		$login        = new Login();
		$registration = new Registration();
		$lostpassword = new Lostpassword();
		add_action( 'login_form', array( $login, 'woo_extra_wp_login_form' ) );
		add_action( 'reign_recaptcha_after_login_form', array( $login, 'woo_extra_wp_login_form' ) );
		add_action( 'buddyxpro_recaptcha_after_login_form', array( $login, 'woo_extra_wp_login_form' ) );
		add_action( 'register_form', array( $registration, 'woo_extra_wp_register_form' ) );
		add_action( 'reign_recaptcha_after_register_form', array( $registration, 'woo_extra_wp_register_form' ) );
		add_action( 'buddyxpro_recaptcha_after_register_form', array( $registration, 'woo_extra_wp_register_form' ) );
		add_filter( 'registration_errors', array( $registration, 'woo_extra_validate_extra_register_fields' ), 10, 3 );
		add_action( 'lostpassword_form', array( $lostpassword, 'woo_extra_wp_lostpassword_form' ) );

		$is_wp_login_recaptcha_enabled = get_option( 'wbc_recaptcha_enable_on_wplogin' );
		if ( 'yes' === $is_wp_login_recaptcha_enabled ) {
			add_action( 'bppcp_after_login_form', array( $registration, 'woo_extra_wp_register_form' ) );
			add_action( 'bppcp_after_register_form', array( $registration, 'woo_extra_wp_register_form' ) );
			add_action( 'bp_lock_after_login_form', array( $registration, 'woo_extra_wp_register_form' ) );
			add_action( 'bp_lock_after_register_form', array( $registration, 'woo_extra_wp_register_form' ) );
		}

		// Buddypress - only load if BuddyPress is active.
		// Hook into bp_init to ensure BuddyPress is fully loaded before registering hooks
		if ( class_exists( 'BuddyPress' ) ) {
			add_action( 'bp_init', array( $this, 'register_buddypress_hooks' ), 10 );
		}

		// bbPress - only load if bbPress is active.
		if ( class_exists( 'bbPress' ) ) {
			$bbpress_topic_class = new Recaptcha_bbPress_Topic();
			// Priority 99 ensures reCAPTCHA appears after other form fields
			add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $bbpress_topic_class, 'wbr_bbpress_topic_form_field' ), 99 );
			add_action( 'bbp_new_topic_pre_extras', array( $bbpress_topic_class, 'wbr_bbpress_topic_recaptcha_verify' ) );
			// Remove non-existent method call: wbr_bbpress_topic_v2_checkbox_script

			$bbpress_reply_class = new Recaptcha_bbPress_Reply();
			// Priority 99 ensures reCAPTCHA appears after other form fields
			add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $bbpress_reply_class, 'wbr_bbpress_reply_form_field_reply' ), 99 );
			add_action( 'bbp_new_reply_pre_extras', array( $bbpress_reply_class, 'wbr_bbpress_reply_recaptcha_verify' ) );
			// Remove non-existent method call: wbr_bbpress_reply_v2_checkbox_script
		}
		// Woocommerce - only load if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			// Woocommerce Login registration and lost form.
			$woocommerce_register      = new Woocommerce_Register();
			$woocommerce_login         = new Woocommerce_Login();
			$woocommerce_lost_password = new Woocommerce_Lostpassword();
			add_action( 'woocommerce_register_form', array( $woocommerce_register, 'woo_extra_register_fields' ) );
			add_action( 'woocommerce_login_form', array( $woocommerce_login, 'woo_extra_login_fields' ) );
			add_action( 'woocommerce_lostpassword_form', array( $woocommerce_lost_password, 'woo_extra_lostpassword_fields' ) );

			// Woocommerce extra.
			$woocommerce_review_order              = new Woocommerce_Review_Order();
			$woocommerce_register_post             = new Woocommerce_Register_Post();
			$lost_password_post                    = new LostpasswordPost();
			$woocommerce_process_login_errors      = new Woocommerce_Process_Login_Errors();
			$woocommerce_after_checkout_validation = new Woocommerce_After_Checkout_Validation();
			add_action( 'woocommerce_review_order_before_submit', array( $woocommerce_review_order, 'woo_extra_checkout_fields' ) );
			add_action( 'woocommerce_register_post', array( $woocommerce_register_post, 'woocomm_validate_signup_captcha' ), 10, 3 );
			add_action( 'lostpassword_post', array( $lost_password_post, 'woocomm_validate_lostpassword_captcha' ), 10, 1 );
			add_action( 'woocommerce_process_login_errors', array( $woocommerce_process_login_errors, 'woocomm_validate_login_captcha' ), 10, 3 );
			add_action( 'woocommerce_after_checkout_validation', array( $woocommerce_after_checkout_validation, 'woocomm_validate_checkout_captcha' ), 10, 2 );
		}

		// Woocommerce Filter.
		$woocommerce_filter = new Woocommerce_Filter();
		add_filter( 'wp_authenticate_user', array( $woocommerce_filter, 'woo_wp_verify_login_captcha' ), 10, 2 );
		add_filter( 'register_post', array( $woocommerce_filter, 'woo_verify_wp_register_captcha' ), 10, 3 );
		// Priority 20 to run after WooCommerce's handler at priority 10
		add_action( 'lostpassword_post', array( $woocommerce_filter, 'woo_verify_wp_lostpassword_captcha' ), 20, 1 );
		add_filter( 'wpforms_frontend_recaptcha_noconflict', array( $woocommerce_filter, 'woo_remove_no_conflict' ) );

		// Comment form display and validation
		$woocommerce_order = new Woocommerce_Order();
		add_filter( 'comment_form_fields', array( $woocommerce_order, 'woo_comment_form_captcha_field' ), 10 );
		add_filter( 'preprocess_comment', array( $woocommerce_filter, 'woo_verify_comment_captcha' ), 10 );

		// Woocommerce Order - only load if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'woocommerce_pay_order_before_submit', array( $woocommerce_order, 'woo_extra_checkout_fields_pay_order' ) );
			add_action( 'woocommerce_before_pay_action', array( $woocommerce_order, 'woo_verify_pay_order_captcha' ) );
			add_action( 'woocommerce_payment_complete', array( $woocommerce_order, 'woo_payment_complete' ) );
			add_action( 'woocommerce_before_add_to_cart_quantity', array( $woocommerce_order, 'woocommerce_payment_request_btn_captcha' ) );
			// Priority 999 ensures reCAPTCHA is added to checkout blocks last
			add_filter( 'render_block_woocommerce/checkout-payment-block', array( $woocommerce_order, 'woo_recaptcha_alter_checkout_payment_block' ), 999, 1 );
			// Add CAPTCHA to WooCommerce product review forms
			add_filter( 'woocommerce_product_review_comment_form_args', array( $woocommerce_order, 'woo_product_review_captcha_field' ), 10 );
		}

		// FluentCart - only load if FluentCart is active.
		if ( class_exists( 'FluentCart\App\App' ) || defined( 'FLUENT_CART_VERSION' ) ) {
			// Load FluentCart integration classes
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/fluentcart-extra/Fluent_Cart_Registration.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/fluentcart-extra/Fluent_Cart_Login.php';

			// FluentCart Registration
			$fluentcart_registration = new Fluent_Cart_Registration();
			add_action( 'fluent_cart/views/checkout_page_registration_form', array( $fluentcart_registration, 'render_registration_captcha' ), 10, 1 );
			add_filter( 'register_post', array( $fluentcart_registration, 'validate_wp_registration_captcha' ), 10, 3 );

			// FluentCart Login
			$fluentcart_login = new Fluent_Cart_Login();
			add_action( 'fluent_cart/views/checkout_page_login_form', array( $fluentcart_login, 'render_login_captcha' ), 10, 1 );
			add_filter( 'authenticate', array( $fluentcart_login, 'validate_login_captcha' ), 20, 3 );
		}

		if ( $plugin_public->woo_recaptcha_check_is_ie_browser() ) {
			add_action( 'wp_head', array( $plugin_public, 'woo_recaptcha_add_header_metadata_for_ie' ) );
			add_action( 'login_head', array( $plugin_public, 'woo_recaptcha_add_header_metadata_for_ie' ) );
			add_filter( 'script_loader_tag', array( $plugin_public, 'google_recaptcha_defer_parsing_of_js' ), 10 );
		}

		// Custom login form integration - only if WooCommerce is active
		if ( class_exists( 'WooCommerce' ) ) {
			// Check if custom login form is enabled (works for all service types)
			$custom_login_enabled = get_option( 'wbc_recaptcha_custom_wp_login_form_login' );
			if ( 'yes' !== $custom_login_enabled ) {
				// Check v3 specific option for backward compatibility
				$custom_login_enabled = get_option( 'wbc_recaptcha_v3_custom_wp_login_form_login' );
			}
			
			if ( 'yes' === $custom_login_enabled ) {
				$woocommerce_login = new Woocommerce_Login();
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

	/**
	 * Register BuddyPress hooks after bp_init
	 *
	 * @since     2.1.0
	 */
	public function register_buddypress_hooks() {
		$registration_bp = new Registrationbp();
		add_action( 'bp_before_registration_submit_buttons', array( $registration_bp, 'woo_extra_bp_register_form' ), 36 );
		add_action( 'bp_signup_validate', array( $registration_bp, 'innovage_validate_user_registration' ) );
	}

}
