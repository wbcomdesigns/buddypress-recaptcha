<?php
/**
 * ALTCHA Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

// Load ALTCHA library if not already loaded
if ( ! class_exists( 'AltchaPlugin' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/altcha/helpers.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/altcha/class-altcha-lib.php';
}

/**
 * ALTCHA implementation - Privacy-first, self-hosted proof-of-work captcha
 */
class WBC_Altcha_Service extends WBC_Captcha_Service_Base {

	/**
	 * ALTCHA library instance
	 *
	 * @var AltchaPlugin
	 */
	private $altcha_lib;

	/**
	 * Constructor - Initialize library
	 */
	public function __construct() {
		parent::__construct();

		// Initialize ALTCHA library
		if ( class_exists( 'AltchaPlugin' ) && isset( AltchaPlugin::$instance ) ) {
			$this->altcha_lib = AltchaPlugin::$instance;
		}
	}

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id' => 'altcha',
			'service_name' => __( 'ALTCHA (Self-Hosted)', 'buddypress-recaptcha' ),
			'script_url' => plugins_url( 'public/js/altcha.min.js', dirname( dirname( __FILE__ ) ) ),
			'verify_endpoint' => '', // Server-side verification handled differently
			'response_field' => 'altcha',
		);
	}

	/**
	 * Get the service identifier
	 *
	 * @return string
	 */
	public function get_service_id() {
		return 'altcha';
	}

	/**
	 * Get the service display name
	 *
	 * @return string
	 */
	public function get_service_name() {
		return $this->config['service_name'];
	}

	/**
	 * Get the site key (HMAC key for ALTCHA)
	 *
	 * @return string
	 */
	public function get_site_key() {
		return trim( get_option( 'wbc_altcha_hmac_key' ) );
	}

	/**
	 * Get the secret key (same as HMAC key for ALTCHA)
	 *
	 * @return string
	 */
	public function get_secret_key() {
		return trim( get_option( 'wbc_altcha_hmac_key' ) );
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		return 'wbc-altcha-captcha';
	}

	/**
	 * Get the script URL for this service
	 *
	 * @return string
	 */
	public function get_script_url() {
		return $this->config['script_url'];
	}

	/**
	 * Enqueue scripts for this service
	 *
	 * @param string $context The context where the script is used
	 * @return void
	 */
	public function enqueue_scripts( $context = 'default' ) {
		$hmac_key = $this->get_site_key();
		if ( empty( $hmac_key ) ) {
			return;
		}

		wp_enqueue_script(
			$this->get_script_handle( $context ),
			$this->get_script_url(),
			array(),
			'2.0.0',
			array(
				'strategy' => 'defer',
				'in_footer' => true,
			)
		);

		// Add module type attribute
		add_filter( 'script_loader_tag', array( $this, 'add_module_type_attribute' ), 10, 2 );
	}

	/**
	 * Add type="module" to ALTCHA script tag
	 *
	 * @param string $tag    Script tag HTML
	 * @param string $handle Script handle
	 * @return string
	 */
	public function add_module_type_attribute( $tag, $handle ) {
		if ( $this->get_script_handle() === $handle ) {
			$tag = str_replace( ' src=', ' type="module" src=', $tag );
		}
		return $tag;
	}

	/**
	 * Generate ALTCHA challenge
	 *
	 * @return array|false Challenge data or false on failure
	 */
	private function generate_challenge() {
		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return false;
		}

		// Use ALTCHA library if available
		if ( $this->altcha_lib ) {
			$complexity = intval( get_option( 'wbc_altcha_max_number', 100000 ) );
			$expires = intval( get_option( 'wbc_altcha_expires', 3600 ) );
			return $this->altcha_lib->generate_challenge( $hmac_key, $complexity, $expires );
		}

		// Fallback to manual generation
		$salt = bin2hex( random_bytes( 16 ) );
		$max_number = intval( get_option( 'wbc_altcha_max_number', 100000 ) );
		$number = rand( 1, $max_number );
		$challenge = hash( 'sha256', $salt . $number );
		$signature = hash_hmac( 'sha256', $challenge, $hmac_key );

		return array(
			'algorithm' => 'SHA-256',
			'challenge' => $challenge,
			'maxnumber' => $max_number,
			'salt' => $salt,
			'signature' => $signature,
		);
	}

	/**
	 * Render the captcha field
	 *
	 * @param string $context The context where captcha is rendered
	 * @param array  $args    Additional arguments
	 * @return void
	 */
	public function render( $context, $args = array() ) {
		$hmac_key = $this->get_site_key();
		if ( empty( $hmac_key ) ) {
			return;
		}

		// Generate challenge
		$challenge = $this->generate_challenge();
		if ( ! $challenge ) {
			return;
		}

		// Get settings
		$auto_verify = get_option( 'wbc_altcha_auto_verify', 'off' );
		$hide_logo = get_option( 'wbc_altcha_hide_logo', 'no' ) === 'yes';

		// Generate unique ID
		$widget_id = 'altcha-' . $context . '-wbc';

		// Get nonce
		$nonce_action = $this->get_nonce_action( $context );

		// Render HTML
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_html( wp_create_nonce( $nonce_action ) ); ?>" />
		<div class="wbc_captcha_field wbc_altcha_field">
			<altcha-widget
				id="<?php echo esc_attr( $widget_id ); ?>"
				challengejson='<?php echo wp_json_encode( $challenge ); ?>'
				<?php if ( $auto_verify !== 'off' ) : ?>
				auto="<?php echo esc_attr( $auto_verify ); ?>"
				<?php endif; ?>
				<?php if ( $hide_logo ) : ?>
				hidelogo
				<?php endif; ?>
				name="altcha"
			></altcha-widget>
		</div>
		<?php
	}

	/**
	 * Verify the captcha response
	 *
	 * @param string $response The captcha response (base64 encoded payload)
	 * @param array  $args     Additional arguments
	 * @return bool
	 */
	public function verify( $response, $args = array() ) {
		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return false; // ALTCHA requires HMAC key
		}

		if ( empty( $response ) ) {
			return false;
		}

		// Use ALTCHA library for verification (required for self-hosted)
		if ( $this->altcha_lib ) {
			$verified = $this->altcha_lib->verify( $response, $hmac_key );
		} else {
			// Fallback to manual verification
			$verified = $this->verify_solution( $response, $hmac_key );
		}

		return apply_filters( 'wbc_captcha_verified', $verified, array(), $response, $this->get_service_id() );
	}

	/**
	 * Verify solution manually (fallback if ALTCHA plugin not available)
	 *
	 * @param string $payload  Base64 encoded payload
	 * @param string $hmac_key HMAC key
	 * @return bool
	 */
	private function verify_solution( $payload, $hmac_key ) {
		$data = json_decode( base64_decode( $payload ) );

		if ( ! $data ) {
			return false;
		}

		// Check expiration if present
		$salt_url = wp_parse_url( $data->salt );
		if ( isset( $salt_url['query'] ) && ! empty( $salt_url['query'] ) ) {
			parse_str( $salt_url['query'], $salt_params );
			if ( ! empty( $salt_params['expires'] ) ) {
				$expires = intval( $salt_params['expires'], 10 );
				if ( $expires > 0 && $expires < time() ) {
					return false;
				}
			}
		}

		// Verify algorithm
		$alg_ok = ( $data->algorithm === 'SHA-256' );

		// Verify challenge
		$calculated_challenge = hash( 'sha256', $data->salt . $data->number );
		$challenge_ok = ( $data->challenge === $calculated_challenge );

		// Verify signature
		$calculated_signature = hash_hmac( 'sha256', $data->challenge, $hmac_key );
		$signature_ok = ( $data->signature === $calculated_signature );

		return $alg_ok && $challenge_ok && $signature_ok;
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint() {
		return ''; // ALTCHA verifies locally, no external endpoint
	}

	/**
	 * Get form field name for the response
	 *
	 * @return string
	 */
	public function get_response_field_name() {
		return $this->config['response_field'];
	}

	/**
	 * Check if this service requires no-conflict mode
	 *
	 * @return bool
	 */
	public function requires_no_conflict() {
		return false;
	}
}
