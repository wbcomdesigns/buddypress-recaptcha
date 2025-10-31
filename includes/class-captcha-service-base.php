<?php
/**
 * Base Captcha Service Class
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

/**
 * Abstract base class for captcha services
 */
abstract class WBC_Captcha_Service_Base implements WBC_Captcha_Service_Interface {
	
	/**
	 * Service configuration
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Service ID
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Service name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_config();
	}

	/**
	 * Initialize service configuration
	 */
	abstract protected function init_config();

	/**
	 * Get script URL for the service
	 *
	 * @return string
	 */
	abstract public function get_script_url();

	/**
	 * Get script handle for the service
	 *
	 * @param string $context
	 * @return string
	 */
	abstract public function get_script_handle( $context = 'default' );

	/**
	 * Check if no-conflict mode is required
	 *
	 * @return bool
	 */
	public function requires_no_conflict() {
		return false;
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	abstract public function get_verify_endpoint();

	/**
	 * Get form field name for the response
	 *
	 * @return string
	 */
	abstract public function get_response_field_name();

	/**
	 * Get service-specific attributes for the captcha container
	 *
	 * @param string $context The context
	 * @return array
	 */
	public function get_container_attributes( $context ) {
		return array();
	}

	/**
	 * Get service ID
	 *
	 * @return string
	 */
	public function get_service_id() {
		return $this->id;
	}

	/**
	 * Get service name
	 *
	 * @return string
	 */
	public function get_service_name() {
		return $this->name;
	}

	/**
	 * Check if the service is properly configured
	 *
	 * @return bool
	 */
	public function is_configured() {
		$site_key = $this->get_site_key();
		$secret_key = $this->get_secret_key();
		return ! empty( $site_key ) && ! empty( $secret_key );
	}

	/**
	 * Get service-specific options
	 *
	 * @param string $option_name Option name
	 * @param mixed  $default     Default value
	 * @return mixed
	 */
	public function get_option( $option_name, $default = null ) {
		$full_option_name = 'wbc_' . $this->get_service_id() . '_' . $option_name;
		$value = get_option( $full_option_name, $default );
		return ( '' === $value ) ? $default : $value;
	}

	/**
	 * Check if enabled for context
	 *
	 * @param string $context
	 * @return bool
	 */
	public function is_enabled_for_context( $context ) {
		$option_map = $this->get_context_option_map();
		$option_name = isset( $option_map[ $context ] ) ? $option_map[ $context ] : '';
		
		if ( empty( $option_name ) ) {
			return false;
		}
		
		return 'yes' === get_option( $option_name );
	}

	/**
	 * Get context option map
	 *
	 * @return array
	 */
	protected function get_context_option_map() {
		return array(
			'wp_login' => 'wbc_recaptcha_enable_on_wplogin',
			'wp_register' => 'wbc_recaptcha_enable_on_wpregister',
			'wp_lostpassword' => 'wbc_recaptcha_enable_on_wplostpassword',
			'woo_login' => 'wbc_recaptcha_enable_on_login',
			'woo_register' => 'wbc_recaptcha_enable_on_signup',
			'woo_lostpassword' => 'wbc_recaptcha_enable_on_lostpassword',
			'bp_register' => 'wbc_recaptcha_enable_on_buddypress',  // Fixed: admin saves as 'buddypress' not 'signup_bp'
			'bbpress_topic' => 'wbc_recaptcha_enable_on_bbpress_topic',
			'bbpress_reply' => 'wbc_recaptcha_enable_on_bbpress_reply',
			'woo_checkout_guest' => 'wbc_recaptcha_enable_on_guestcheckout',
			'woo_checkout_login' => 'wbc_recaptcha_enable_on_logincheckout',
			'comment' => 'wbc_recaptcha_enable_on_comment',
			'cf7' => 'wbc_recaptcha_enable_on_cf7',
			'wpforms' => 'wbc_recaptcha_enable_on_wpforms',
			'gravityforms' => 'wbc_recaptcha_enable_on_gravityforms',
			'ninjaforms' => 'wbc_recaptcha_enable_on_ninjaforms',
			'forminator' => 'wbc_recaptcha_enable_on_forminator',
			'elementorpro' => 'wbc_recaptcha_enable_on_elementorpro',
		);
	}

	/**
	 * Enqueue necessary scripts and styles
	 *
	 * @param string $context The context where scripts are enqueued
	 * @return void
	 */
	public function enqueue_scripts( $context ) {
		if ( $this->requires_no_conflict() ) {
			$this->handle_no_conflict_mode();
		}

		wp_enqueue_script( 'jquery' );
		
		$handle = $this->get_script_handle( $context );
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			wp_register_script(
				$handle,
				$this->get_script_url(),
				array( 'jquery' ),
				null,
				true
			);
		}
		
		wp_enqueue_script( $handle );
	}

	/**
	 * Handle no-conflict mode
	 */
	protected function handle_no_conflict_mode() {
		$no_conflict = $this->get_option( 'no_conflict', 'no' );
		if ( 'yes' !== $no_conflict ) {
			return;
		}

		global $wp_scripts;
		$urls = $this->get_conflict_urls();
		$allowed_handles = $this->get_allowed_handles();
		
		foreach ( $wp_scripts->queue as $handle ) {
			foreach ( $urls as $url ) {
				if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ! in_array( $handle, $allowed_handles ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					break;
				}
			}
		}
	}

	/**
	 * Get URLs that might conflict
	 *
	 * @return array
	 */
	protected function get_conflict_urls() {
		return array();
	}

	/**
	 * Get allowed script handles
	 *
	 * @return array
	 */
	protected function get_allowed_handles() {
		return array();
	}

	/**
	 * Get nonce action for context
	 *
	 * @param string $context
	 * @return string
	 */
	protected function get_nonce_action( $context ) {
		$nonce_actions = array(
			'wp_login' => 'wp-login-nonce',
			'wp_register' => 'wp-register-nonce',
			'wp_lostpassword' => 'wp-lostpassword-nonce',
			'woo_login' => 'woo-login-nonce',
			'woo_register' => 'woo-register-nonce',
			'woo_lostpassword' => 'woo-lostpassword-nonce',
			'bp_register' => 'bp-register-nonce',
			'bbpress_topic' => 'bbpress-topic-nonce',
			'bbpress_reply' => 'bbpress-reply-nonce',
			'woo_checkout' => 'woo-checkout-nonce',
			'comment' => 'comment-nonce',
			'cf7' => 'cf7-nonce',
			'wpforms' => 'wpforms-nonce',
			'gravityforms' => 'gravityforms-nonce',
			'ninjaforms' => 'ninjaforms-nonce',
			'forminator' => 'forminator-nonce',
			'elementorpro' => 'elementorpro-nonce',
		);

		return isset( $nonce_actions[ $context ] ) ? $nonce_actions[ $context ] : $context . '-nonce';
	}

	/**
	 * Get form selector for context
	 *
	 * @param string $context
	 * @return string
	 */
	protected function get_form_selector( $context ) {
		$selectors = array(
			'wp_login' => '#loginform',
			'wp_register' => '#registerform',
			'wp_lostpassword' => '#lostpasswordform',
			'woo_login' => '.woocommerce-form-login',
			'woo_register' => '.woocommerce-form-register',
			'woo_lostpassword' => '.woocommerce-ResetPassword',
			'bp_register' => '#signup_form',
			'bbpress_topic' => '#new-post',
			'bbpress_reply' => '#new-post',
			'woo_checkout' => 'form.checkout',
			'comment' => '#commentform',
			'cf7' => '.wpcf7-form',
			'wpforms' => '.wpforms-form',
			'gravityforms' => '.gform_wrapper form',
			'ninjaforms' => '.nf-form-content',
			'forminator' => '.forminator-ui',
			'elementorpro' => '.elementor-form',
		);

		return isset( $selectors[ $context ] ) ? $selectors[ $context ] : '#' . $context . '-form';
	}

	/**
	 * Get submit button selector
	 *
	 * @param string $context
	 * @return string
	 */
	protected function get_submit_button_selector( $context ) {
		$selectors = array(
			'wp_login' => '#wp-submit',
			'wp_register' => '#wp-submit',
			'wp_lostpassword' => '#wp-submit',
			'woo_login' => '.woocommerce-form-login__submit',
			'woo_register' => '.woocommerce-form-register__submit',
			'woo_lostpassword' => '.woocommerce-Button',
			'bp_register' => '#submit, #signup_submit',
			'bbpress_topic' => '#bbp_topic_submit',
			'bbpress_reply' => '#bbp_reply_submit',
			'woo_checkout' => '#place_order',
			'comment' => '#submit',
			'cf7' => '.wpcf7-submit',
			'wpforms' => '.wpforms-submit',
			'gravityforms' => '.gform_button',
			'ninjaforms' => '.nf-element .submit-container input[type="button"]',
			'forminator' => '.forminator-button-submit',
			'elementorpro' => '.elementor-button',
		);

		return isset( $selectors[ $context ] ) ? $selectors[ $context ] : '#submit';
	}

	/**
	 * Make HTTP request to verify captcha
	 *
	 * @param string $url     Verification URL
	 * @param array  $params  Request parameters
	 * @return array|false
	 */
	protected function make_verify_request( $url, $params ) {
		$request = wp_remote_post(
			$url,
			array(
				'timeout' => 10,
				'body'    => $params,
			)
		);

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $request );
		if ( empty( $body ) ) {
			return false;
		}

		return json_decode( $body, true );
	}

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	protected function get_user_ip() {
		return wb_recaptcha_get_the_user_ip();
	}

	/**
	 * Check if captcha should be rendered for the context
	 *
	 * @param string $context
	 * @return bool
	 */
	protected function should_render( $context ) {
		// Check if enabled for context
		if ( ! $this->is_enabled_for_context( $context ) ) {
			return false;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return false;
		}

		// Allow filtering
		return apply_filters( 'wbc_should_render_captcha', true, $context, $this->get_service_id() );
	}

	/**
	 * Check if captcha should be verified for the context
	 *
	 * @param string $context
	 * @return bool
	 */
	protected function should_verify( $context ) {
		// Check if enabled for context
		if ( ! $this->is_enabled_for_context( $context ) ) {
			return false;
		}

		// Check IP restriction
		$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return false;
		}

		// Allow filtering
		return apply_filters( 'wbc_should_verify_captcha', true, $context, $this->get_service_id() );
	}
}