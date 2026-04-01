<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Service class uses simplified naming convention.
/**
 * ReCAPTCHA v3 Service Implementation
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * ReCAPTCHA v3 service class.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_Recaptcha_V3_Service extends WBC_Captcha_Service_Base {

	/**
	 * Service ID
	 *
	 * @var string
	 */
	protected $id = 'recaptcha-v3';

	/**
	 * Service name
	 *
	 * @var string
	 */
	protected $name = 'reCAPTCHA v3';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->init_hooks();
	}

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		// Initialize any v3-specific configuration here.
		$this->config = array(
			'version'    => 'v3',
			'script_url' => 'https://www.google.com/recaptcha/api.js',
		);
	}

	/**
	 * Get script URL for the service
	 *
	 * @return string
	 */
	public function get_script_url() {
		$site_key = $this->get_site_key();
		return 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $site_key );
	}

	/**
	 * Get script handle for the service
	 *
	 * @param string $context The context identifier.
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		return 'wbc-recaptcha-v3';
	}

	/**
	 * Check if no-conflict mode is required
	 *
	 * @return bool
	 */
	public function requires_no_conflict() {
		return 'yes' === get_option( 'wbc_recapcha_no_conflict_v3' );
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint() {
		return 'https://www.google.com/recaptcha/api/siteverify';
	}

	/**
	 * Get form field name for the response
	 *
	 * @return string
	 */
	public function get_response_field_name() {
		return 'g-recaptcha-response';
	}

	/**
	 * Get URLs that might conflict
	 *
	 * @return array
	 */
	protected function get_conflict_urls() {
		return array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );
	}

	/**
	 * Get allowed script handles
	 *
	 * @return array
	 */
	protected function get_allowed_handles() {
		return array( 'wbc-recaptcha-v3', 'wbc-woo-captcha-v3' );
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Enqueue scripts when needed.
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'maybe_enqueue_scripts' ) );
	}

	/**
	 * Get site key
	 *
	 * @return string
	 */
	public function get_site_key() {
		// Try standard format first (with underscore).
		$site_key = get_option( 'wbc_recaptcha_v3_site_key', '' );
		if ( empty( $site_key ) ) {
			// Fallback to hyphen format for backward compatibility.
			$site_key = get_option( 'wbc_recaptcha-v3_site_key', '' );
		}
		return $site_key;
	}

	/**
	 * Get secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		// Try standard format first (with underscore).
		$secret_key = get_option( 'wbc_recaptcha_v3_secret_key', '' );
		if ( empty( $secret_key ) ) {
			// Fallback to hyphen format for backward compatibility.
			$secret_key = get_option( 'wbc_recaptcha-v3_secret_key', '' );
		}
		return $secret_key;
	}

	/**
	 * Check if service is properly configured
	 *
	 * @return bool
	 */
	public function is_configured() {
		$site_key   = $this->get_site_key();
		$secret_key = $this->get_secret_key();

		return ! empty( $site_key ) && ! empty( $secret_key );
	}

	/**
	 * Render captcha
	 *
	 * @param string $context The context where captcha is being rendered.
	 * @param array  $args    Additional arguments.
	 * @return void
	 */
	public function render( $context = '', $args = array() ) {
		if ( ! $this->should_render( $context ) ) {
			return;
		}

		if ( ! $this->is_configured() ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p class="wbc-captcha-error">' . esc_html__( 'reCAPTCHA v3 is not properly configured. Please check your settings.', 'buddypress-recaptcha' ) . '</p>';
			}
			return;
		}

		$site_key       = $this->get_site_key();
		$action         = $this->get_action_for_context( $context );
		$token_field_id = 'wbc_recaptcha_' . $context . '_token';

		// Add hidden field for token.
		echo '<input type="hidden" name="' . esc_attr( $token_field_id ) . '" id="' . esc_attr( $token_field_id ) . '" value="" />';

		// Add nonce for security.
		wp_nonce_field( 'wbc_captcha_' . $context, 'wbc_captcha_nonce_' . $context );

		// Enqueue script if not already enqueued.
		$this->enqueue_script();

		// Add inline script for this specific form.
		$this->add_inline_script( $context, $site_key, $action, $token_field_id );
	}

	/**
	 * Verify captcha response
	 *
	 * @param string $response The captcha response (token).
	 * @param array  $args     Additional arguments including context.
	 * @return bool
	 */
	public function verify( $response, $args = array() ) {
		// Extract context from args for backward compatibility.
		$context = isset( $args['context'] ) ? $args['context'] : '';
		if ( ! $this->should_verify( $context ) ) {
			return true;
		}

		// Skip nonce verification if not in POST context or nonce not set.
		// Nonce is optional as many forms don't use our custom nonce.
		$nonce_field  = 'wbc_captcha_nonce_' . $context;
		$nonce_action = 'wbc_captcha_' . $context;

		if ( isset( $_POST[ $nonce_field ] ) ) {
			// Only verify if nonce is present.
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) ), $nonce_action ) ) {
				return false;
			}
		}

		// Use provided response or get token from POST data.
		$token = '';

		if ( ! empty( $response ) ) {
			$token = $response;
		} else {
			// Get token from POST data.
			$token_field_id = 'wbc_recaptcha_' . $context . '_token';
			$token          = isset( $_POST[ $token_field_id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $token_field_id ] ) ) : '';

			if ( empty( $token ) ) {
				// Try legacy field names for backward compatibility.
				$legacy_fields = array(
					'wbc_recaptcha_wp_login_token',
					'wbc_recaptcha_wp_register_token',
					'wbc_recaptcha_wp_lostpassword_token',
					'wbc_login_token',
					'wbc_signup_token',
					'wbc_lostpassword_token',
					'wbc_checkout_token',
					'wbc_recaptcha_bbpress_topic_token',
					'wbc_recaptcha_bbpress_reply_token',
				);

				foreach ( $legacy_fields as $field ) {
					if ( isset( $_POST[ $field ] ) && ! empty( $_POST[ $field ] ) ) {
						$token = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
						break;
					}
				}
			}
		}

		if ( empty( $token ) ) {
			return false;
		}

		// Verify with Google.
		$secret_key = $this->get_secret_key();
		$remote_ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $secret_key,
					'response' => $token,
					'remoteip' => $remote_ip,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		if ( empty( $response_body ) ) {
			return false;
		}

		$result = json_decode( $response_body, true );

		if ( ! isset( $result['success'] ) || ! $result['success'] ) {
			return false;
		}

		// Check score threshold.
		$score_threshold = $this->get_score_threshold( $context );
		$score           = isset( $result['score'] ) ? floatval( $result['score'] ) : 0;

		if ( $score < $score_threshold ) {
			return false;
		}

		// Check action if configured.
		$expected_action = $this->get_action_for_context( $context );
		if ( ! empty( $expected_action ) && isset( $result['action'] ) && $result['action'] !== $expected_action ) {
			return false;
		}

		// Allow filtering of result.
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return apply_filters( 'wbc_recaptcha_v3_verify', true, $result, $context );
	}

	/**
	 * Get action name for context
	 *
	 * @param string $context The context identifier.
	 * @return string
	 */
	private function get_action_for_context( $context ) {
		$action_map = array(
			'wp_login'           => get_option( 'wbc_recapcha_wp_login_method_action_v3', 'wp_login' ),
			'wp_register'        => get_option( 'wbc_recapcha_wp_register_method_action_v3', 'wp_registration' ),
			'wp_lostpassword'    => get_option( 'wbc_recapcha_wp_lostpassword_method_action_v3', 'wp_lostpassword' ),
			'woo_login'          => get_option( 'wbc_recapcha_login_action_v3', 'login' ),
			'woo_register'       => get_option( 'wbc_recapcha_signup_action_v3', 'signup' ),
			'woo_lostpassword'   => get_option( 'wbc_recapcha_lostpassword_action_v3', 'lostpassword' ),
			'woo_checkout_guest' => get_option( 'wbc_recapcha_checkout_action_v3', 'checkout' ),
			'woo_checkout_login' => get_option( 'wbc_recapcha_checkout_action_v3', 'checkout' ),
			'bp_register'        => get_option( 'wbc_recapcha_signup_action_v3_bp', 'signup' ),
			'bbpress_topic'      => get_option( 'wbc_recapcha_bbpress_topic_action_v3', 'bbPress_topic' ),
			'bbpress_reply'      => get_option( 'wbc_recapcha_bbpress_reply_action_v3', 'bbPress_reply' ),
			'comment_form'       => 'comment',
			'order_tracking'     => 'order_tracking',
		);

		return isset( $action_map[ $context ] ) ? $action_map[ $context ] : $context;
	}

	/**
	 * Get score threshold for context
	 *
	 * @param string $context The context identifier.
	 * @return float
	 */
	private function get_score_threshold( $context ) {
		$threshold_map = array(
			'wp_login'           => get_option( 'wbc_recapcha_login_score_threshold_v3', '0.5' ),
			'wp_register'        => get_option( 'wbc_recapcha_wp_register_score_threshold_v3', '0.5' ),
			'wp_lostpassword'    => get_option( 'wbc_recapcha_wp_lostpassword_score_threshold_v3', '0.5' ),
			'woo_login'          => get_option( 'wbc_recapcha_login_score_threshold_v3', '0.5' ),
			'woo_register'       => get_option( 'wbc_recapcha_signup_score_threshold_v3', '0.5' ),
			'woo_lostpassword'   => get_option( 'wbc_recapcha_lostpassword_score_threshold_v3', '0.5' ),
			'woo_checkout_guest' => get_option( 'wbc_recapcha_checkout_score_threshold_v3', '0.5' ),
			'woo_checkout_login' => get_option( 'wbc_recapcha_checkout_score_threshold_v3', '0.5' ),
			'bp_register'        => get_option( 'wbc_recapcha_signup_score_threshold_v3_bp', '0.5' ),
			'bbpress_topic'      => get_option( 'wbc_recapcha_bbpress_topic_score_threshold_v3', '0.5' ),
			'bbpress_reply'      => get_option( 'wbc_recapcha_bbpress_reply_score_threshold_v3', '0.5' ),
			'comment_form'       => '0.5',
			'order_tracking'     => '0.5',
		);

		$threshold = isset( $threshold_map[ $context ] ) ? $threshold_map[ $context ] : '0.5';
		return floatval( $threshold );
	}

	/**
	 * Enqueue scripts for a specific context
	 *
	 * Required by WBC_Captcha_Service_Interface. Called by service manager.
	 *
	 * @param string $context The context where scripts are needed.
	 * @return void
	 */
	public function enqueue_scripts( $context = '' ) {
		if ( ! $this->is_configured() ) {
			return;
		}

		$this->enqueue_script();
	}

	/**
	 * Check if scripts should be enqueued
	 *
	 * @return void
	 */
	public function maybe_enqueue_scripts() {
		if ( ! $this->is_configured() ) {
			return;
		}

		// Check if we're on a page that might need captcha.
		if ( $this->is_captcha_page() ) {
			$this->enqueue_script();
		}
	}

	/**
	 * Check if current page might need captcha
	 *
	 * @return bool
	 */
	private function is_captcha_page() {
		// Login pages.
		if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true ) ) {
			return true;
		}

		// WooCommerce pages.
		if ( class_exists( 'WooCommerce' ) ) {
			if ( is_account_page() || is_checkout() ) {
				return true;
			}
		}

		// BuddyPress pages.
		if ( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) {
			return true;
		}

		// bbPress pages.
		if ( class_exists( 'bbPress' ) ) {
			if ( is_singular( array( 'forum', 'topic' ) ) ) {
				return true;
			}
		}

		// Comments.
		if ( is_singular() && comments_open() ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueue reCAPTCHA v3 script
	 *
	 * @return void
	 */
	private function enqueue_script() {
		if ( wp_script_is( 'wbc-recaptcha-v3', 'enqueued' ) ) {
			return;
		}

		$site_key = $this->get_site_key();

		// Check for no-conflict mode.
		if ( 'yes' === get_option( 'wbc_recapcha_no_conflict_v3' ) ) {
			$this->dequeue_conflicting_scripts();
		}

		wp_enqueue_script(
			'wbc-recaptcha-v3',
			'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $site_key ),
			array(),
			'3.0',
			true
		);
	}

	/**
	 * Add inline script for specific form
	 *
	 * @param string $context The context identifier.
	 * @param string $site_key        The reCAPTCHA site key.
	 * @param string $action           The reCAPTCHA action name.
	 * @param string $token_field_id   The token field ID.
	 * @return void
	 */
	private function add_inline_script( $context, $site_key, $action, $token_field_id ) {
		// Generate token function with error handling.
		$script = "
		(function() {
			var siteKey = '" . esc_js( $site_key ) . "';
			var action = '" . esc_js( $action ) . "';
			var tokenFieldId = '" . esc_js( $token_field_id ) . "';

			// Function to generate and set token.
			function generateToken() {
				grecaptcha.execute(siteKey, {action: action})
					.then(function(token) {
						var tokenField = document.getElementById(tokenFieldId);
						if (tokenField) {
							tokenField.value = token;
						}
					})
					.catch(function(error) {
						console.error('reCAPTCHA v3 error:', error);
						// Still allow form submission even if reCAPTCHA fails.
						// Server-side will handle missing token appropriately.
					});
			}

			// Generate initial token on page load.
			grecaptcha.ready(function() {
				generateToken();

				// Regenerate token every 110 seconds (token expires in 120 seconds).
				// This ensures we always have a fresh token.
				setInterval(function() {
					generateToken();
				}, 110000);
			});

			// Also regenerate token on form submission to ensure freshness.
			document.addEventListener('DOMContentLoaded', function() {
				var tokenField = document.getElementById(tokenFieldId);
				if (tokenField && tokenField.form) {
					tokenField.form.addEventListener('submit', function(e) {
						// Regenerate token on submit for maximum freshness.
						generateToken();
					}, false);
				}
			});
		})();
		";

		wp_add_inline_script( 'wbc-recaptcha-v3', $script );
	}

	/**
	 * Dequeue conflicting reCAPTCHA scripts
	 *
	 * @return void
	 */
	private function dequeue_conflicting_scripts() {
		global $wp_scripts;

		if ( ! isset( $wp_scripts->queue ) ) {
			return;
		}

		$urls            = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );
		$allowed_handles = array( 'wbc-recaptcha-v3', 'wbc-woo-captcha-v3' );

		foreach ( $wp_scripts->queue as $handle ) {
			if ( in_array( $handle, $allowed_handles, true ) ) {
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
}
