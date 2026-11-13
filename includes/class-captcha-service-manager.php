<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Legacy filename convention.
/**
 * Captcha Service Manager
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

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
		do_action( 'wbc_register_captcha_services', $this ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
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
	 * @return WBC_Captcha_Service_Interface|null
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
		if ( self::is_disabled_by_constant() ) {
			return;
		}

		try {
			// Check IP restriction.
			$recaptcha_system_ip = get_option( 'wbc_recaptcha_ip_to_skip_captcha' );
			if ( $recaptcha_system_ip && function_exists( 'wb_recaptcha_restriction_recaptcha_by_ip' ) && wb_recaptcha_restriction_recaptcha_by_ip() ) {
				$this->log_debug( 'Skipping captcha render due to IP whitelist', $context );
				return;
			}

			$service = $this->get_active_service();
			if ( ! $service ) {
				// "Not set up yet" is a normal state, not a genuine error. Do not write to
				// the error log (which spams debug.log on every wp_login in production);
				// emit only a debug-level note when WP_DEBUG logging is on, and surface the
				// admin-side configuration notice. log_error() is reserved for real failures.
				$this->log_debug( 'No active captcha service available', $context );
				$this->show_configuration_notice();
				return;
			}

			if ( ! $service->is_configured() ) {
				// "Not configured yet" is a normal state, not a genuine error. Same handling
				// as above: debug-level note only, plus the admin notice. This avoids the
				// repeated "[BuddyPress reCAPTCHA Error] [wp_login] Active captcha service is
				// not configured" log spam reported in production.
				$this->log_debug( 'Active captcha service is not configured', $context );
				$this->show_configuration_notice();
				return;
			}

			if ( ! $service->is_enabled_for_context( $context ) ) {
				$this->log_debug( 'Captcha not enabled for context: ' . $context );
				return;
			}

			// The render half of the pair every provider's verify() honours through
			// should_skip_verification(). Until 2.2.1 only reCAPTCHA v3 applied it, so on
			// the other four a filter that skipped the check still showed the widget.
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( ! apply_filters( 'wbc_should_render_captcha', true, $context, $service->get_service_id() ) ) {
				return;
			}

			// Enqueue scripts.
			if ( method_exists( $service, 'enqueue_scripts' ) ) {
				$service->enqueue_scripts( $context );
			}
			wp_enqueue_script( 'wbc-captcha-refresh' );

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
	 * Recovery switch: `define( 'WBC_CAPTCHA_DISABLE', true );` in wp-config.php
	 * turns every CAPTCHA off (nothing renders, nothing is checked) so an owner
	 * locked out by a wrong key or a provider outage can always log in.
	 *
	 * @return bool
	 */
	public static function is_disabled_by_constant() {
		return defined( 'WBC_CAPTCHA_DISABLE' ) && WBC_CAPTCHA_DISABLE;
	}

	/**
	 * Contexts whose CAPTCHA passed during this request.
	 *
	 * @var array<string,bool>
	 */
	private $verified_contexts = array();

	/**
	 * Whether any CAPTCHA was solved and verified during this request.
	 *
	 * Tokens are single-use, so a second check of the same submission (one form
	 * that triggers two verification hooks) must not ask the provider again.
	 *
	 * @return bool
	 */
	public function has_verified_this_request() {
		return ! empty( $this->verified_contexts );
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
		if ( self::is_disabled_by_constant() ) {
			return true;
		}

		$service = $this->get_active_service();
		if ( ! $service || ! $service->is_configured() ) {
			// maybe_render_misconfiguration_notice() tells the admin on every
			// admin screen while this stays true.
			$this->log_error( 'Captcha service not configured during verify', $context );

			// Optional fail-closed mode: block the request when configuration is
			// missing. Off by default for backwards compatibility.
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( $this->fail_closed( $context ) ) {
				return false;
			}
			return true; // Don't block if not configured (legacy behavior).
		}

		if ( ! $service->is_enabled_for_context( $context ) ) {
			return true; // Don't block if not enabled for this context.
		}

		// Allowlisted IPs skip verification for every provider, including ones
		// registered by other plugins (render() already hides their widget).
		if ( $this->is_ip_whitelisted() ) {
			return true;
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
			$reason = $service instanceof WBC_Captcha_Service_Base ? $service->pull_failure_reason() : '';

			// The provider could not be reached or answered garbage: an outage,
			// not a bad visitor. Let a submitted token through unless the owner
			// chose "Block when the provider is down". An empty token never passes.
			if ( ! $result && in_array( $reason, array( 'network-error', 'bad-response' ), true ) && '' !== (string) $response && ! $this->fail_closed( $context ) ) {
				$this->log_error( 'Captcha provider unreachable (' . $reason . '); submission allowed because fail-closed is off', $context );
				$this->verified_contexts[ $context ] = true;
				return true;
			}

			if ( ! $result ) {
				$this->log_info( 'Captcha verification failed for context: ' . $context );
				$this->record_rejection( $context, $service->get_service_id(), $reason ? $reason : ( '' === (string) $response ? 'missing-token' : 'rejected' ) );
			} else {
				$this->verified_contexts[ $context ] = true;
			}

			return $result;

		} catch ( Exception $e ) {
			$this->log_error( 'Exception during captcha verification: ' . $e->getMessage(), $context );

			// Optional fail-closed mode: block on unexpected exceptions when the
			// admin opts into stricter verification.
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( $this->fail_closed( $context ) ) {
				return false;
			}

			// Default: don't block on exceptions (legacy behavior).
			return true;
		}
	}

	/**
	 * Transient holding the most recent rejected submissions for the Overview tab.
	 */
	const REJECTIONS_TRANSIENT = 'wbc_captcha_rejections';

	/**
	 * Remember a rejected submission so the owner can see why visitors fail.
	 *
	 * Keeps the newest 20 for a week. Stores no token, secret or IP.
	 * ponytail: read-modify-write, two rejections in the same instant can drop one entry; fine for a diagnostic list.
	 *
	 * @param string $context Form context.
	 * @param string $service Service id.
	 * @param string $reason  Short reason code(s).
	 * @return void
	 */
	private function record_rejection( $context, $service, $reason ) {
		$log = self::get_recent_rejections();
		array_unshift(
			$log,
			array(
				'time'    => time(),
				'context' => sanitize_key( $context ),
				'service' => sanitize_key( $service ),
				'reason'  => substr( (string) $reason, 0, 120 ),
			)
		);
		set_transient( self::REJECTIONS_TRANSIENT, array_slice( $log, 0, 20 ), WEEK_IN_SECONDS );
	}

	/**
	 * The stored rejections from the last 7 days, newest first. Every write renews the
	 * transient's expiry, so each row's own time is checked here.
	 *
	 * @return array
	 */
	public static function get_recent_rejections() {
		$log    = get_transient( self::REJECTIONS_TRANSIENT );
		$cutoff = time() - WEEK_IN_SECONDS;
		return array_values(
			array_filter(
				is_array( $log ) ? $log : array(),
				static function ( $row ) use ( $cutoff ) {
					return is_array( $row ) && isset( $row['time'] ) && (int) $row['time'] >= $cutoff;
				}
			)
		);
	}

	/**
	 * Output a persistent admin notice when the active CAPTCHA service is
	 * missing required keys. Hooked to `admin_notices` from the main plugin
	 * loader so admins notice silent fail-open conditions even when no
	 * verification has fired this request.
	 */
	public function maybe_render_misconfiguration_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings_url = admin_url( 'admin.php?page=buddypress-recaptcha&tab=rfw-general' );

		if ( self::is_disabled_by_constant() ) {
			echo '<div class="notice notice-warning wbc-captcha-config-notice"><p>';
			echo esc_html(
				sprintf(
					/* translators: %s: plugin name */
					__( '%s is switched off by WBC_CAPTCHA_DISABLE in wp-config.php. No form is protected until it is removed.', 'buddypress-recaptcha' ),
					'Wbcom CAPTCHA Manager'
				)
			);
			echo '</p></div>';
			return;
		}

		$key_error = get_transient( 'wbc_captcha_key_error' );
		if ( is_array( $key_error ) && ! empty( $key_error['service'] ) ) {
			echo '<div class="notice notice-warning wbc-captcha-config-notice"><p>';
			echo wp_kses(
				sprintf(
					/* translators: 1: plugin name, 2: CAPTCHA provider name, 3: provider error codes, 4: opening anchor tag, 5: closing anchor tag */
					__( '%1$s: %2$s rejected this site\'s keys (%3$s). So nobody gets locked out, CAPTCHA checks are being let through until this is fixed. %4$sCheck the keys in Quick Setup%5$s.', 'buddypress-recaptcha' ),
					'Wbcom CAPTCHA Manager',
					esc_html( $key_error['service'] ),
					esc_html( implode( ', ', (array) $key_error['codes'] ) ),
					'<a href="' . esc_url( $settings_url ) . '">',
					'</a>'
				),
				array( 'a' => array( 'href' => array() ) )
			);
			echo '</p></div>';
			return;
		}

		// Live check, not a cached flag: the notice disappears the moment keys are saved.
		$service = $this->get_active_service();
		if ( $service && $service->is_configured() ) {
			return;
		}

		// Use `notice-warning` (not `notice-error`) so the message survives
		// over-broad third-party admin CSS rules like
		//   `#wpwrap .notice.notice-error { display:none }`
		// that ship in some sibling Wbcom plugins. The `wbc-captcha-config-notice`
		// class also gives admins a stable hook to style or scope the notice.
		echo '<div class="notice notice-warning wbc-captcha-config-notice"><p>';
		echo wp_kses(
			sprintf(
				/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
				__( 'Wbcom CAPTCHA Manager is enabled but the active service is not configured. Forms are currently unprotected — %1$sopen settings%2$s and add your API keys.', 'buddypress-recaptcha' ),
				'<a href="' . esc_url( $settings_url ) . '">',
				'</a>'
			),
			array( 'a' => array( 'href' => array() ) )
		);
		echo '</p></div>';
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
	 * Whether a provider outage should block the submission. The Advanced tab
	 * saves "yes"/"no", so the stored value is parsed, never cast ((bool) "no"
	 * is true).
	 *
	 * @param string $context Form context.
	 * @return bool
	 */
	private function fail_closed( $context ) {
		$stored = filter_var( get_option( 'wbc_captcha_fail_closed', false ), FILTER_VALIDATE_BOOLEAN );
		return (bool) apply_filters( 'wbc_captcha_fail_closed', $stored, $context );
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

		// One entry per line or comma-separated; both forms are accepted.
		$entries = preg_split( '/[\s,]+/', (string) $ip_list, -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $entries as $entry ) {
			if ( wb_recaptcha_ip_matches_entry( $user_ip, $entry ) ) {
				return true;
			}
		}
		return false;
	}
}

// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed -- Helper function must accompany manager class.
if ( ! function_exists( 'wbc_captcha_service_manager' ) ) {
	/**
	 * Helper function to get service manager instance.
	 *
	 * @return WBC_Captcha_Service_Manager
	 */
	//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wbc_captcha_service_manager() {
		return WBC_Captcha_Service_Manager::get_instance();
	}
}
