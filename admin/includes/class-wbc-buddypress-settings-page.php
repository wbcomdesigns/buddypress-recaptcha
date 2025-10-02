<?php
/**
 * Simplified Settings Page for BuddyPress reCAPTCHA
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wbc_WooCommerce_Settings_Page_Simplified' ) ) :

	/**
	 * Simplified Settings Page Class
	 */
	class Wbc_WooCommerce_Settings_Page_Simplified {
	
		/**
		 * Settings page ID
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Settings page label
		 *
		 * @var string
		 */
		public $label;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->id    = 'wbc_woo_recaptcha';
			$this->label = 'reCaptcha';

			include 'class-settings-renderer.php';
			
			// Initialize settings
			add_action( 'admin_init', array( $this, 'init_settings' ) );
		}

		/**
		 * Initialize settings
		 */
		public function init_settings() {
			// Initialize settings integration if needed
			if ( class_exists( 'WBC_Settings_Integration' ) ) {
				WBC_Settings_Integration::init();
			}
		}

		/**
		 * Get available captcha services dynamically
		 *
		 * @return array Service ID => Service Name
		 */
		private function get_available_services() {
			$services = array();

			if ( function_exists( 'wbc_captcha_service_manager' ) ) {
				$service_manager = wbc_captcha_service_manager();
				$registered_services = $service_manager->get_services();

				foreach ( $registered_services as $service_id => $service ) {
					$services[ $service_id ] = $service->get_service_name();
				}
			}

			// Fallback if service manager is not available
			if ( empty( $services ) ) {
				$services = array(
					'recaptcha_v2' => __( 'Google reCAPTCHA v2 (Checkbox)', 'buddypress-recaptcha' ),
					'recaptcha_v3' => __( 'Google reCAPTCHA v3 (Invisible)', 'buddypress-recaptcha' ),
					'turnstile'    => __( 'Cloudflare Turnstile', 'buddypress-recaptcha' ),
				);
			}

			return $services;
		}

		/**
		 * Get sections
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = array(
				''                => __( 'Service Configuration', 'buddypress-recaptcha' ),
			);
			
			// Add plugin-specific integration sections
			$sections['wordpress'] = __( 'WordPress', 'buddypress-recaptcha' );
			
			if ( class_exists( 'WooCommerce' ) ) {
				$sections['woocommerce'] = __( 'WooCommerce', 'buddypress-recaptcha' );
			}
			
			if ( class_exists( 'BuddyPress' ) ) {
				$sections['buddypress'] = __( 'BuddyPress', 'buddypress-recaptcha' );
			}
			
			if ( class_exists( 'bbPress' ) ) {
				$sections['bbpress'] = __( 'bbPress', 'buddypress-recaptcha' );
			}
			
			$sections['appearance'] = __( 'Appearance', 'buddypress-recaptcha' );
			$sections['advanced'] = __( 'Advanced', 'buddypress-recaptcha' );

			return apply_filters( 'wbc_recaptcha_settings_sections', $sections );
		}

		/**
		 * Output sections
		 */
		public function output_sections() {
			global $current_section;

			$sections = $this->get_sections();

			if ( empty( $sections ) || 1 === count( $sections ) ) {
				return;
			}

			echo '<ul class="subsubsub">';

			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				echo '<li>';
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=wbc-recaptcha-page&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) ) . '" 
					class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a>';
				echo ( end( $array_keys ) === $id ? '' : ' | ' );
				echo '</li>';
			}

			echo '</ul><br class="clear" />';
		}

		/**
		 * Get settings for the default section (Service Configuration)
		 *
		 * @return array
		 */
		public function get_service_settings() {
			// Get active service for dynamic settings
			$active_service = '';
			if ( function_exists( 'wbc_captcha_service_manager' ) ) {
				$service = wbc_captcha_service_manager()->get_active_service();
				if ( $service ) {
					$active_service = $service->get_service_id();
				}
			}

			$settings = array(
				array(
					'name' => __( 'Captcha Service Selection', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_captcha_service_selection',
				),

				array(
					'name'    => __( 'Active Captcha Service', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_captcha_service',
					'options' => $this->get_available_services(),
					'default' => 'recaptcha_v2',
					'desc'    => __( 'Select which captcha service to use across your site', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_captcha_service_selection',
				),

				// reCAPTCHA v2 Settings
				array(
					'name'  => __( 'Google reCAPTCHA v2 Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_recaptcha_v2_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha_v2',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recaptcha_v2_site_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v2 site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha_v2',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_recaptcha_v2_secret_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v2 secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha_v2',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_recaptcha_v2_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha_v2',
				),

				// reCAPTCHA v3 Settings
				array(
					'name'  => __( 'Google reCAPTCHA v3 Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_recaptcha_v3_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha_v3',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recaptcha_v3_site_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v3 site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha_v3',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_recaptcha_v3_secret_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v3 secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha_v3',
				),

				array(
					'name'    => __( 'Score Threshold', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_recaptcha_v3_score_threshold',
					'desc'    => __( 'Set the minimum score (0.0 to 1.0) to pass verification. Higher scores mean stricter validation.', 'buddypress-recaptcha' ),
					'default' => '0.5',
					'custom_attributes' => array(
						'min'  => '0',
						'max'  => '1',
						'step' => '0.1',
					),
					'class'   => 'wbc-service-field wbc-service-recaptcha_v3',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_recaptcha_v3_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha_v3',
				),

				// Turnstile Settings
				array(
					'name'  => __( 'Cloudflare Turnstile Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_turnstile_settings',
					'class' => 'wbc-service-settings wbc-service-turnstile',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_turnstile_site_key',
					'desc'        => __( 'Enter your Cloudflare Turnstile site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-turnstile',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_turnstile_secret_key',
					'desc'        => __( 'Enter your Cloudflare Turnstile secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-turnstile',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_turnstile_settings',
					'class' => 'wbc-service-settings wbc-service-turnstile',
				),

				// ALTCHA Settings
				array(
					'name'  => __( 'ALTCHA Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_altcha_settings',
					'desc'  => __( 'Configure ALTCHA - Privacy-first, self-hosted captcha. No external API required.', 'buddypress-recaptcha' ),
					'class' => 'wbc-service-settings wbc-service-altcha',
				),

				array(
					'name'        => __( 'HMAC Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_altcha_hmac_key',
					'desc'        => __( 'Enter a secret HMAC key for challenge signing. Generate a random string (32+ characters recommended).', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your HMAC secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Complexity (Max Number)', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_altcha_max_number',
					'desc'    => __( 'Maximum number for proof-of-work challenge (higher = harder, recommended: 50000-100000)', 'buddypress-recaptcha' ),
					'default' => '100000',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Challenge Expiration', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_altcha_expires',
					'desc'    => __( 'Time in seconds before challenge expires (recommended: 3600)', 'buddypress-recaptcha' ),
					'default' => '3600',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Auto Verify', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_altcha_auto_verify',
					'desc'    => __( 'When to automatically start verification', 'buddypress-recaptcha' ),
					'options' => array(
						'off'      => __( 'Manual (User clicks checkbox)', 'buddypress-recaptcha' ),
						'onload'   => __( 'On page load', 'buddypress-recaptcha' ),
						'onfocus'  => __( 'On form focus', 'buddypress-recaptcha' ),
						'onsubmit' => __( 'On form submit', 'buddypress-recaptcha' ),
					),
					'default' => 'off',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Hide Logo', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_altcha_hide_logo',
					'desc'    => __( 'Hide the ALTCHA logo from the widget', 'buddypress-recaptcha' ),
					'options' => array(
						'no'  => __( 'No', 'buddypress-recaptcha' ),
						'yes' => __( 'Yes', 'buddypress-recaptcha' ),
					),
					'default' => 'no',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_altcha_settings',
					'class' => 'wbc-service-settings wbc-service-altcha',
				),
			);

			return apply_filters( 'wbc_recaptcha_service_settings', $settings );
		}

		/**
		 * Get settings for WordPress integration
		 *
		 * @return array
		 */
		public function get_wordpress_settings() {
			$settings = array(
				array(
					'name' => __( 'WordPress Core Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_wp_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for WordPress native forms', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Login Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wplogin',
					'desc'    => __( 'Protect WordPress login form from brute force attacks', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Registration Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wpregister',
					'desc'    => __( 'Prevent spam registrations on your site', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Lost Password Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wplostpassword',
					'desc'    => __( 'Secure password reset requests', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Comment Forms', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_comment',
					'desc'    => __( 'Stop spam comments on posts and pages', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_wp_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_wordpress_settings', $settings );
		}

		/**
		 * Get settings for WooCommerce integration
		 *
		 * @return array
		 */
		public function get_woocommerce_settings() {
			$settings = array(
				array(
					'name' => __( 'WooCommerce Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_woo_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for WooCommerce forms', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Login Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_login',
					'desc'    => __( 'Protect customer login from unauthorized access', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Registration Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_signup',
					'desc'    => __( 'Prevent fake customer account creation', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Lost Password Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_lostpassword',
					'desc'    => __( 'Secure password reset for customers', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Guest Checkout', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_guestcheckout',
					'desc'    => __( 'Protect checkout from automated bots', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Logged-in User Checkout', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_logincheckout',
					'desc'    => __( 'Add extra security for logged-in user checkouts', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Pay for Order', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_payfororder',
					'desc'    => __( 'Secure payment forms for existing orders', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Order Tracking', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_order_tracking',
					'desc'    => __( 'Protect order tracking from automated queries', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_woo_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_woocommerce_settings', $settings );
		}

		/**
		 * Get settings for BuddyPress integration
		 *
		 * @return array
		 */
		public function get_buddypress_settings() {
			$settings = array(
				array(
					'name' => __( 'BuddyPress Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_bp_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for BuddyPress community features', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Member Registration', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_buddypress',
					'desc'    => __( 'Protect community registration from spam accounts', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_bp_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_buddypress_settings', $settings );
		}

		/**
		 * Get settings for bbPress integration
		 *
		 * @return array
		 */
		public function get_bbpress_settings() {
			$settings = array(
				array(
					'name' => __( 'bbPress Forum Protection', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_bbpress_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for forum activities', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'New Topic Creation', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_bbpress_topic',
					'desc'    => __( 'Prevent spam topics in your forums', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Topic Replies', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_bbpress_reply',
					'desc'    => __( 'Stop automated spam replies', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_bbpress_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_bbpress_settings', $settings );
		}

		/**
		 * Get appearance settings
		 *
		 * @return array
		 */
		public function get_appearance_settings() {
			$settings = array(
				array(
					'name' => __( 'Global Appearance Settings', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_appearance_settings',
					'desc' => __( 'These settings apply to all enabled forms', 'buddypress-recaptcha' ),
				),

				// For reCAPTCHA v2
				array(
					'name'    => __( 'Theme (reCAPTCHA v2)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_theme',
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
					),
					'default' => 'light',
					'desc'    => __( 'Color theme for reCAPTCHA v2 widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha_v2',
				),

				array(
					'name'    => __( 'Size (reCAPTCHA v2)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_size',
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'desc'    => __( 'Size of the reCAPTCHA v2 widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha_v2',
				),

				array(
					'name'    => __( 'Badge Position (reCAPTCHA v3)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_v3_badge',
					'options' => array(
						'bottomright' => __( 'Bottom Right', 'buddypress-recaptcha' ),
						'bottomleft'  => __( 'Bottom Left', 'buddypress-recaptcha' ),
						'inline'      => __( 'Inline', 'buddypress-recaptcha' ),
					),
					'default' => 'bottomright',
					'desc'    => __( 'Position of the reCAPTCHA v3 badge', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha_v3',
				),

				// For Turnstile
				array(
					'name'    => __( 'Theme (Turnstile)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_turnstile_theme',
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
						'auto'  => __( 'Auto', 'buddypress-recaptcha' ),
					),
					'default' => 'auto',
					'desc'    => __( 'Color theme for Turnstile widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-turnstile',
				),

				array(
					'name'    => __( 'Size (Turnstile)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_turnstile_size',
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'desc'    => __( 'Size of the Turnstile widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-turnstile',
				),

				array(
					'name'    => __( 'Language', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_language',
					'options' => $this->get_language_options(),
					'default' => '',
					'desc'    => __( 'Language for captcha widget (leave empty for auto-detect)', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_appearance_settings',
				),
			);

			return apply_filters( 'wbc_recaptcha_appearance_settings', $settings );
		}

		/**
		 * Get advanced settings
		 *
		 * @return array
		 */
		public function get_advanced_settings() {
			$settings = array(
				array(
					'name' => __( 'Advanced Settings', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_advanced_settings',
				),

				array(
					'name'    => __( 'IP Whitelist', 'buddypress-recaptcha' ),
					'type'    => 'textarea',
					'id'      => 'wbc_recapcha_ip_to_skip_captcha',
					'desc'    => __( 'Enter IP addresses to skip captcha verification (comma-separated)', 'buddypress-recaptcha' ),
					'placeholder' => __( '192.168.1.1, 10.0.0.1', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'No Conflict Mode', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_no_conflict',
					'desc'    => __( 'Prevent conflicts with other captcha plugins', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Disable Submit Button', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_disable_submitbtn',
					'desc'    => __( 'Disable submit button until captcha is completed (reCAPTCHA v2 only)', 'buddypress-recaptcha' ),
					'default' => 'no',
					'class'   => 'wbc-service-specific wbc-service-recaptcha_v2',
				),

				array(
					'name'    => __( 'Checkout Timeout (minutes)', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_recapcha_checkout_timeout',
					'desc'    => __( 'Time before checkout captcha revalidation is required (0 to disable)', 'buddypress-recaptcha' ),
					'default' => '3',
					'custom_attributes' => array(
						'min' => '0',
						'max' => '60',
					),
				),

				array(
					'name' => __( 'Error Messages', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_error_messages',
				),

				array(
					'name'        => __( 'Blank Captcha Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_blank',
					'desc'        => __( 'Error message when captcha is not completed', 'buddypress-recaptcha' ),
					'default'     => __( 'Please complete the security check.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Please complete the security check.', 'buddypress-recaptcha' ),
				),

				array(
					'name'        => __( 'Invalid Captcha Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_invalid',
					'desc'        => __( 'Error message when captcha verification fails', 'buddypress-recaptcha' ),
					'default'     => __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ),
				),

				array(
					'name'        => __( 'No Response Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_no_response',
					'desc'        => __( 'Error message when captcha server doesn\'t respond', 'buddypress-recaptcha' ),
					'default'     => __( 'Could not verify security check. Please refresh and try again.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Could not verify security check. Please refresh and try again.', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_error_messages',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_advanced_settings',
				),
			);

			return apply_filters( 'wbc_recaptcha_advanced_settings', $settings );
		}

		/**
		 * Get language options
		 *
		 * @return array
		 */
		private function get_language_options() {
			return array(
				''      => __( 'Auto-detect', 'buddypress-recaptcha' ),
				'ar'    => __( 'Arabic', 'buddypress-recaptcha' ),
				'bg'    => __( 'Bulgarian', 'buddypress-recaptcha' ),
				'ca'    => __( 'Catalan', 'buddypress-recaptcha' ),
				'zh-CN' => __( 'Chinese (Simplified)', 'buddypress-recaptcha' ),
				'zh-TW' => __( 'Chinese (Traditional)', 'buddypress-recaptcha' ),
				'hr'    => __( 'Croatian', 'buddypress-recaptcha' ),
				'cs'    => __( 'Czech', 'buddypress-recaptcha' ),
				'da'    => __( 'Danish', 'buddypress-recaptcha' ),
				'nl'    => __( 'Dutch', 'buddypress-recaptcha' ),
				'en'    => __( 'English', 'buddypress-recaptcha' ),
				'et'    => __( 'Estonian', 'buddypress-recaptcha' ),
				'fil'   => __( 'Filipino', 'buddypress-recaptcha' ),
				'fi'    => __( 'Finnish', 'buddypress-recaptcha' ),
				'fr'    => __( 'French', 'buddypress-recaptcha' ),
				'de'    => __( 'German', 'buddypress-recaptcha' ),
				'el'    => __( 'Greek', 'buddypress-recaptcha' ),
				'iw'    => __( 'Hebrew', 'buddypress-recaptcha' ),
				'hi'    => __( 'Hindi', 'buddypress-recaptcha' ),
				'hu'    => __( 'Hungarian', 'buddypress-recaptcha' ),
				'id'    => __( 'Indonesian', 'buddypress-recaptcha' ),
				'it'    => __( 'Italian', 'buddypress-recaptcha' ),
				'ja'    => __( 'Japanese', 'buddypress-recaptcha' ),
				'ko'    => __( 'Korean', 'buddypress-recaptcha' ),
				'lv'    => __( 'Latvian', 'buddypress-recaptcha' ),
				'lt'    => __( 'Lithuanian', 'buddypress-recaptcha' ),
				'no'    => __( 'Norwegian', 'buddypress-recaptcha' ),
				'fa'    => __( 'Persian', 'buddypress-recaptcha' ),
				'pl'    => __( 'Polish', 'buddypress-recaptcha' ),
				'pt'    => __( 'Portuguese', 'buddypress-recaptcha' ),
				'pt-BR' => __( 'Portuguese (Brazil)', 'buddypress-recaptcha' ),
				'ro'    => __( 'Romanian', 'buddypress-recaptcha' ),
				'ru'    => __( 'Russian', 'buddypress-recaptcha' ),
				'sr'    => __( 'Serbian', 'buddypress-recaptcha' ),
				'sk'    => __( 'Slovak', 'buddypress-recaptcha' ),
				'sl'    => __( 'Slovenian', 'buddypress-recaptcha' ),
				'es'    => __( 'Spanish', 'buddypress-recaptcha' ),
				'sv'    => __( 'Swedish', 'buddypress-recaptcha' ),
				'th'    => __( 'Thai', 'buddypress-recaptcha' ),
				'tr'    => __( 'Turkish', 'buddypress-recaptcha' ),
				'uk'    => __( 'Ukrainian', 'buddypress-recaptcha' ),
				'vi'    => __( 'Vietnamese', 'buddypress-recaptcha' ),
			);
		}

		/**
		 * Output the settings
		 * 
		 * @param string $current Current tab/section
		 */
		public function output( $current = '' ) {
			// Use the passed parameter or fall back to global
			global $current_section;
			$section = ! empty( $current ) ? $current : $current_section;

			// Get the appropriate settings based on section
			switch ( $section ) {
				case 'wordpress':
					$settings = $this->get_wordpress_settings();
					break;
				case 'woocommerce':
					$settings = $this->get_woocommerce_settings();
					break;
				case 'buddypress':
					$settings = $this->get_buddypress_settings();
					break;
				case 'bbpress':
					$settings = $this->get_bbpress_settings();
					break;
				case 'appearance':
					$settings = $this->get_appearance_settings();
					break;
				case 'advanced':
					$settings = $this->get_advanced_settings();
					break;
				default:
					$settings = $this->get_service_settings();
					break;
			}

			// Output the settings
			WBC_Settings_Renderer::output_fields( $settings );
		}

		/**
		 * Save settings
		 * 
		 * @param string $current Current tab/section
		 */
		public function save( $current = '' ) {
			// Use the passed parameter or fall back to global
			global $current_section;
			$section = ! empty( $current ) ? $current : $current_section;

			// Get the appropriate settings based on section
			switch ( $section ) {
				case 'wordpress':
					$settings = $this->get_wordpress_settings();
					break;
				case 'woocommerce':
					$settings = $this->get_woocommerce_settings();
					break;
				case 'buddypress':
					$settings = $this->get_buddypress_settings();
					break;
				case 'bbpress':
					$settings = $this->get_bbpress_settings();
					break;
				case 'appearance':
					$settings = $this->get_appearance_settings();
					break;
				case 'advanced':
					$settings = $this->get_advanced_settings();
					break;
				default:
					$settings = $this->get_service_settings();
					break;
			}

			// Save the settings
			WBC_Settings_Renderer::save_fields( $settings );

			// Trigger settings saved action
			do_action( 'wbc_recaptcha_settings_saved', $section );
		}
	}

endif;