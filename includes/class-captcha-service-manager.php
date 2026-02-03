<?php
/**
 * Captcha Service Manager
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

/**
 * Manages all captcha services
 */
class WBC_Captcha_Service_Manager {

	/**
	 * Singleton instance
	 *
	 * @var WBC_Captcha_Service_Manager
	 */
	private static $instance = null;

	/**
	 * Registered services
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Active service
	 *
	 * @var WBC_Captcha_Service_Interface
	 */
	private $active_service = null;

	/**
	 * Private constructor
	 */
	private function __construct() {
		$this->register_default_services();
		$this->init_active_service();
	}

	/**
	 * Get singleton instance
	 *
	 * @return WBC_Captcha_Service_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register default services
	 */
	private function register_default_services() {
		$services_dir = plugin_dir_path( __DIR__ ) . 'includes/services/';

		$default_services = array(
			'recaptcha-v2' => array(
				'file'  => 'class-recaptcha-v2-service.php',
				'class' => 'WBC_Recaptcha_V2_Service',
			),
			'recaptcha-v3' => array(
				'file'  => 'class-recaptcha-v3-service.php',
				'class' => 'WBC_Recaptcha_V3_Service',
			),
			'turnstile'    => array(
				'file'  => 'class-turnstile-service.php',
				'class' => 'WBC_Turnstile_Service',
			),
			'altcha'       => array(
				'file'  => 'class-altcha-service.php',
				'class' => 'WBC_Altcha_Service',
			),
			'hcaptcha'     => array(
				'file'  => 'class-hcaptcha-service.php',
				'class' => 'WBC_HCaptcha_Service',
			),
		);

		foreach ( $default_services as $service_key => $service_config ) {
			$file_path = $services_dir . $service_config['file'];
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				if ( class_exists( $service_config['class'] ) ) {
					$this->register_service( new $service_config['class']() );
				}
			}
		}

		// Allow plugins to register additional services.
		do_action( 'wbc_register_captcha_services', $this );
	}

	/**
	 * Initialize active service based on settings
	 */
	private function init_active_service() {
		$active_service_id = $this->get_active_service_id();

		if ( isset( $this->services[ $active_service_id ] ) ) {
			$this->active_service = $this->services[ $active_service_id ];
		} else {
			// Fallback to first available service.
			$this->active_service = reset( $this->services );
		}
	}

	/**
	 * Get active service ID from settings
	 *
	 * @return string
	 */
	private function get_active_service_id() {
		// Check new service selection option first.
		$service_id = get_option( 'wbc_captcha_service' );
		if ( ! empty( $service_id ) ) {
			return $service_id;
		}

		// Fallback: Try to determine from configured keys.
		// Check Turnstile.
		$turnstile_site = get_option( 'wbc_turnstile_site_key' );
		if ( ! empty( $turnstile_site ) ) {
			return 'turnstile';
		}

		// Check reCAPTCHA v3 (try both formats for backward compatibility).
		$v3_site = get_option( 'wbc_recaptcha_v3_site_key' );
		if ( empty( $v3_site ) ) {
			$v3_site = get_option( 'wbc_recaptcha-v3_site_key' );
		}
		if ( ! empty( $v3_site ) ) {
			return 'recaptcha-v3';
		}

		// Check reCAPTCHA v2 (try both formats for backward compatibility).
		$v2_site = get_option( 'wbc_recaptcha_v2_site_key' );
		if ( empty( $v2_site ) ) {
			$v2_site = get_option( 'wbc_recaptcha-v2_site_key' );
		}
		if ( ! empty( $v2_site ) ) {
			return 'recaptcha-v2';
		}

		// Default to reCAPTCHA v2.
		return 'recaptcha-v2';
	}

	/**
	 * Register a captcha service
	 *
	 * @param WBC_Captcha_Service_Interface $service The service instance.
	 */
	public function register_service( WBC_Captcha_Service_Interface $service ) {
		$this->services[ $service->get_service_id() ] = $service;
	}

	/**
	 * Get all registered services
	 *
	 * @return array
	 */
	public function get_services() {
		return $this->services;
	}

	/**
	 * Get service by ID
	 *
	 * @param string $service_id The service identifier.
	 * @return WBC_Captcha_Service_Interface|null
	 */
	public function get_service( $service_id ) {
		return isset( $this->services[ $service_id ] ) ? $this->services[ $service_id ] : null;
	}

	/**
	 * Get active service
	 *
	 * @return WBC_Captcha_Service_Interface
	 */
	public function get_active_service() {
		return $this->active_service;
	}

	/**
	 * Set active service
	 *
	 * @param string $service_id The service identifier.
	 * @return bool
	 */
	public function set_active_service( $service_id ) {
		if ( isset( $this->services[ $service_id ] ) ) {
			$this->active_service = $this->services[ $service_id ];
			update_option( 'wbc_captcha_service', $service_id );
			return true;
		}
		return false;
	}

	/**
	 * Render captcha for context
	 *
	 * @param string $context The context identifier.
	 * @param array  $args    Additional arguments.
	 */
	public function render( $context, $args = array() ) {
		try {
			// Check IP restriction.
			$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
			if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
				$this->log_debug( 'Skipping captcha render due to IP whitelist', $context );
				return;
			}

			$service = $this->get_active_service();
			if ( ! $service ) {
				$this->log_error( 'No active captcha service available', $context );
				$this->show_configuration_notice();
				return;
			}

			if ( ! $service->is_configured() ) {
				$this->log_error( 'Active captcha service is not configured', $context );
				$this->show_configuration_notice();
				return;
			}

			if ( ! $service->is_enabled_for_context( $context ) ) {
				$this->log_debug( 'Captcha not enabled for context: ' . $context );
				return;
			}

			// Enqueue scripts.
			if ( method_exists( $service, 'enqueue_scripts' ) ) {
				$service->enqueue_scripts( $context );
			}

			// Render.
			$service->render( $context, $args );

		} catch ( Exception $e ) {
			$this->log_error( 'Exception during captcha render: ' . $e->getMessage(), $context );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				echo '<!-- Captcha render error: ' . esc_html( $e->getMessage() ) . ' -->';
			}
		}
	}

	/**
	 * Verify captcha response
	 *
	 * @param string $context  The context identifier.
	 * @param string $response The captcha response.
	 * @param array  $args     Additional arguments.
	 * @return bool
	 */
	public function verify( $context, $response = null, $args = array() ) {
		$service = $this->get_active_service();
		if ( ! $service || ! $service->is_configured() ) {
			return true; // Don't block if not configured.
		}

		if ( ! $service->is_enabled_for_context( $context ) ) {
			return true; // Don't block if not enabled for this context.
		}

		// Get response from POST if not provided.
		if ( null === $response ) {
			$field_name = $service->get_response_field_name();
			$response   = isset( $_POST[ $field_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in individual service verify() methods.
		}

		// Add context to args.
		if ( ! isset( $args['context'] ) ) {
			$args['context'] = $context;
		}

		// Wrap in try-catch for error handling.
		try {
			$result = $service->verify( $response, $args );

			if ( ! $result ) {
				$this->log_info( 'Captcha verification failed for context: ' . $context );
			}

			return $result;

		} catch ( Exception $e ) {
			$this->log_error( 'Exception during captcha verification: ' . $e->getMessage(), $context );
			// Don't block on exceptions.
			return true;
		}
	}

	/**
	 * Log debug message
	 *
	 * @param string $message The log message.
	 * @param string $context The context identifier.
	 */
	private function log_debug( $message, $context = '' ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( sprintf( '[BuddyPress reCAPTCHA Debug] [%s] %s', $context, $message ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Log info message
	 *
	 * @param string $message The log message.
	 * @param string $context The context identifier.
	 */
	private function log_info( $message, $context = '' ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( sprintf( '[BuddyPress reCAPTCHA Info] [%s] %s', $context, $message ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Log error message
	 *
	 * @param string $message The log message.
	 * @param string $context The context identifier.
	 */
	private function log_error( $message, $context = '' ) {
		error_log( sprintf( '[BuddyPress reCAPTCHA Error] [%s] %s', $context, $message ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

		// Store error for admin notice.
		$errors = get_transient( 'wbc_captcha_errors' );
		if ( ! is_array( $errors ) ) {
			$errors = array();
		}

		$errors[] = array(
			'message' => $message,
			'context' => $context,
			'time'    => time(),
		);

		// Keep only last 10 errors.
		$errors = array_slice( $errors, -10 );

		set_transient( 'wbc_captcha_errors', $errors, HOUR_IN_SECONDS );
	}

	/**
	 * Show configuration notice to admins
	 */
	private function show_configuration_notice() {
		if ( current_user_can( 'manage_options' ) ) {
			$settings_url = admin_url( 'admin.php?page=buddypress-recaptcha&tab=rfw-general' );
			echo '<div class="wbc-captcha-notice">';
			echo '<p>' . sprintf(
				/* translators: %s: Settings page URL */
				esc_html__( 'Captcha service is not properly configured. Please %1$sconfigure your captcha settings%2$s.', 'buddypress-recaptcha' ),
				'<a href="' . esc_url( $settings_url ) . '">',
				'</a>'
			) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Check if captcha is enabled for context
	 *
	 * @param string $context The context identifier.
	 * @return bool
	 */
	public function is_enabled_for_context( $context ) {
		$service = $this->get_active_service();
		return $service ? $service->is_enabled_for_context( $context ) : false;
	}

	/**
	 * Get active service site key
	 *
	 * @return string
	 */
	public function get_site_key() {
		$service = $this->get_active_service();
		return $service ? $service->get_site_key() : '';
	}

	/**
	 * Get active service secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		$service = $this->get_active_service();
		return $service ? $service->get_secret_key() : '';
	}

	/**
	 * Check if active service is configured
	 *
	 * @return bool
	 */
	public function is_configured() {
		$service = $this->get_active_service();
		return $service ? $service->is_configured() : false;
	}

	/**
	 * Check if current user IP is whitelisted
	 *
	 * @return bool
	 */
	public function is_ip_whitelisted() {
		// Try both option names for backward compatibility.
		$ip_list = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
		if ( empty( $ip_list ) ) {
			$ip_list = get_option( 'wbc_recapcha_ip_to_skip_captcha' ); // Typo version for backward compatibility.
		}

		if ( empty( $ip_list ) ) {
			return false;
		}

		$user_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( empty( $user_ip ) ) {
			return false;
		}

		$ip_array = array_map( 'trim', explode( ',', $ip_list ) );
		return in_array( $user_ip, $ip_array, true );
	}
}

// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed -- Helper function must accompany manager class.
if ( ! function_exists( 'wbc_captcha_service_manager' ) ) {
	/**
	 * Helper function to get service manager instance.
	 *
	 * @return WBC_Captcha_Service_Manager
	 */
	function wbc_captcha_service_manager() {
		return WBC_Captcha_Service_Manager::get_instance();
	}
}
