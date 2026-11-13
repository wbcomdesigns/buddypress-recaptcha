<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Service class uses simplified naming convention.
/**
 * ALTCHA Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * ALTCHA implementation - Privacy-first, self-hosted proof-of-work captcha
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */
class WBC_Altcha_Service extends WBC_Captcha_Service_Base {

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id'      => 'altcha',
			'service_name'    => 'ALTCHA (Self-Hosted)',
			'script_url'      => plugins_url( 'public/js/altcha.min.js', dirname( __DIR__ ) ),
			'verify_endpoint' => '', // Server-side verification handled differently.
			'response_field'  => 'altcha',
		);
		add_action( 'rest_api_init', array( $this, 'register_challenge_route' ) );
	}

	/**
	 * REST namespace and route the widget loads a fresh challenge from.
	 */
	const REST_NAMESPACE = 'wbc-captcha/v1';
	const REST_ROUTE     = '/altcha-challenge';

	/**
	 * Serve challenges over REST instead of embedding one in the page.
	 *
	 * Each solution is single-use, so an embedded challenge breaks twice: a
	 * retry after a failed AJAX submit re-solves the same challenge (rejected as
	 * a replay), and a page cache hands one challenge to every visitor. The
	 * widget fetches a new one each time it starts verifying.
	 */
	public function register_challenge_route() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_challenge' ),
				'permission_callback' => '__return_true', // Public: visitors solve it before they log in.
			)
		);
	}

	/**
	 * REST callback: one fresh signed challenge, never cached.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function rest_challenge() {
		$challenge = $this->generate_challenge();
		if ( ! $challenge ) {
			return new WP_Error( 'wbc_altcha_not_configured', __( 'ALTCHA is not configured.', 'buddypress-recaptcha' ), array( 'status' => 503 ) );
		}
		$response = new WP_REST_Response( $challenge );
		$response->header( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
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
		// Translated here, not in init_config(): services are constructed while the
		// plugin file loads, long before init, and calling __() there triggered WP 6.7's
		// _load_textdomain_just_in_time notice on every page load.
		return __( 'ALTCHA (Self-Hosted)', 'buddypress-recaptcha' );
	}

	/**
	 * ALTCHA has no public site key: its only key is the HMAC secret, which must
	 * never reach the browser. Returning it here once printed it on every page.
	 *
	 * @return string Always empty.
	 */
	public function get_site_key() {
		return '';
	}

	/**
	 * ALTCHA is configured when its HMAC secret is set.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->get_secret_key();
	}

	/**
	 * Get the HMAC secret used to sign and verify challenges (server side only)
	 *
	 * @return string
	 */
	public function get_secret_key() {
		return trim( get_option( 'wbc_altcha_hmac_key', '' ) );
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used.
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
	 * @param string $context The context where the script is used.
	 * @return void
	 */
	public function enqueue_scripts( $context = 'default' ) {
		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return;
		}

		$this->handle_no_conflict_mode();

		wp_enqueue_script(
			$this->get_script_handle( $context ),
			$this->get_script_url(),
			array(),
			'2.0.0',
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		// Add module type attribute.
		add_filter( 'script_loader_tag', array( $this, 'add_module_type_attribute' ), 10, 2 );
	}

	/**
	 * Add type="module" to ALTCHA script tag
	 *
	 * @param string $tag    Script tag HTML.
	 * @param string $handle Script handle.
	 * @return string
	 */
	public function add_module_type_attribute( $tag, $handle ) {
		if ( $this->get_script_handle() === $handle ) {
			$tag = str_replace( ' src=', ' type="module" src=', $tag );
		}
		return $tag;
	}

	/**
	 * Default and bounds for the challenge difficulty (the largest number the
	 * visitor's browser may have to try). The admin picks it as "Complexity Level".
	 */
	const DEFAULT_MAX_NUMBER = 100000;
	const MIN_MAX_NUMBER     = 1000;
	const MAX_MAX_NUMBER     = 1000000;

	/**
	 * How long a challenge stays solvable, in seconds. Every challenge expires,
	 * so a solved token can never be used later than this.
	 */
	const DEFAULT_EXPIRES = 3600;
	const MAX_EXPIRES     = DAY_IN_SECONDS;

	/**
	 * Challenge difficulty: the saved "Complexity Level", else the pre-2.2.3 key.
	 *
	 * @return int
	 */
	private function get_max_number() {
		$value = get_option( 'wbc_altcha_complexity', '' );
		if ( '' === $value || false === $value ) {
			$value = get_option( 'wbc_altcha_max_number', self::DEFAULT_MAX_NUMBER );
		}
		$value = absint( $value );
		if ( ! $value ) {
			$value = self::DEFAULT_MAX_NUMBER;
		}
		return min( max( $value, self::MIN_MAX_NUMBER ), self::MAX_MAX_NUMBER );
	}

	/**
	 * Challenge lifetime in seconds, always greater than zero.
	 *
	 * @return int
	 */
	private function get_expires() {
		$value = absint( get_option( 'wbc_altcha_expires', self::DEFAULT_EXPIRES ) );
		if ( ! $value ) {
			$value = self::DEFAULT_EXPIRES;
		}
		return min( $value, self::MAX_EXPIRES );
	}

	/**
	 * Generate an ALTCHA proof-of-work challenge signed with the site's HMAC key.
	 *
	 * The key never leaves the server: the browser gets only the challenge and
	 * its signature.
	 *
	 * @return array|false Challenge data or false on failure
	 */
	private function generate_challenge() {
		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return false;
		}

		$max_number = $this->get_max_number();
		$salt       = bin2hex( random_bytes( 12 ) ) . '?expires=' . ( time() + $this->get_expires() );
		$number     = wp_rand( 1, $max_number );
		$challenge  = hash( 'sha256', $salt . $number );

		return array(
			'algorithm' => 'SHA-256',
			'challenge' => $challenge,
			'maxnumber' => $max_number,
			'salt'      => $salt,
			'signature' => hash_hmac( 'sha256', $challenge, $hmac_key ),
		);
	}

	/**
	 * Whether the visitor's browser can run ALTCHA.
	 *
	 * The widget needs the Web Crypto API, which browsers expose only in a secure
	 * context: HTTPS, or a loopback host (localhost, 127.0.0.1, ::1, *.localhost).
	 * A plain-HTTP .local / .test / staging host is NOT secure to the browser,
	 * whatever WP_ENVIRONMENT_TYPE says, so the widget could never be solved there.
	 *
	 * @return bool
	 */
	private function is_secure_context() {
		if ( is_ssl() ) {
			return true;
		}

		$host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : '';
		$host = preg_replace( '/:\d+$/', '', trim( $host, '[]' ) );
		$host = trim( $host, '[]' );

		return in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) || (bool) preg_match( '/\.localhost$/', $host );
	}

	/**
	 * Render the captcha field
	 *
	 * @param string $context The context where captcha is rendered.
	 * @param array  $args    Additional arguments.
	 * @return void
	 */
	public function render( $context, $args = array() ) {
		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return;
		}

		// Check for secure context (HTTPS required for Web Crypto API).
		if ( ! $this->is_secure_context() ) {
			$current_host = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_SPECIAL_CHARS );
			$current_host = ! empty( $current_host ) ? $current_host : 'unknown';
			?>
			<div class="wbc_captcha_field wbc_altcha_field">
				<div class="wbc-altcha-warning">
					<strong><?php esc_html_e( 'ALTCHA requires HTTPS or localhost:', 'buddypress-recaptcha' ); ?></strong>
					<p><?php esc_html_e( 'ALTCHA uses Web Crypto API which requires a secure context (HTTPS). For local development, use localhost or .local/.test domains.', 'buddypress-recaptcha' ); ?></p>
					<p><strong><?php esc_html_e( 'Your current host:', 'buddypress-recaptcha' ); ?></strong> <?php echo esc_html( $current_host ); ?></p>
					<p><?php esc_html_e( 'For production: Enable HTTPS. For local testing: Use localhost, 127.0.0.1, or .local domains.', 'buddypress-recaptcha' ); ?></p>
				</div>
			</div>
			<?php
			return;
		}


		// Get settings.
		$auto_verify = get_option( 'wbc_altcha_auto_verify', 'off' );
		$hide_logo   = get_option( 'wbc_altcha_hide_logo', 'no' ) === 'yes';

		// Generate unique ID.
		$widget_id = 'altcha-' . $context . '-wbc';

		// Get nonce.
		$nonce_action = $this->get_nonce_action( $context );

		// Render HTML.
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>" />
		<div class="wbc_captcha_field wbc_altcha_field input">
			<altcha-widget
				id="<?php echo esc_attr( $widget_id ); ?>"
				challengeurl="<?php echo esc_url( rest_url( self::REST_NAMESPACE . self::REST_ROUTE ) ); ?>"
				<?php if ( 'off' !== $auto_verify ) : ?>
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
	 * @param string $response The captcha response (base64 encoded payload).
	 * @param array  $args     Additional arguments.
	 * @return bool
	 */
	public function verify( $response, $args = array() ) {
		// Skip only when render() would also have been skipped (IP allowlist / filter).
		// Checked before the key guard: unlike the other providers ALTCHA fails closed
		// when unconfigured, so an allowlisted visitor would otherwise be blocked by a
		// missing key on a form that never showed a widget.
		if ( $this->should_skip_verification( isset( $args['context'] ) ? $args['context'] : '' ) ) {
			return true;
		}

		$hmac_key = $this->get_secret_key();
		if ( empty( $hmac_key ) ) {
			return false; // ALTCHA requires HMAC key.
		}

		if ( ! $this->nonce_check_passes( isset( $args['context'] ) ? $args['context'] : '' ) ) {
			return false;
		}

		if ( empty( $response ) ) {
			return false;
		}

		$verified = $this->verify_solution( $response, $hmac_key );
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return apply_filters( 'wbc_captcha_verified', $verified, array(), $response, $this->get_service_id() );
	}

	/**
	 * Verify an ALTCHA solution payload.
	 *
	 * Accepts a payload only when it was signed with this site's key, solves its
	 * challenge, has not expired, and has not been used before. Anything
	 * malformed is rejected without touching undefined properties.
	 *
	 * @param string $payload  Base64 encoded payload.
	 * @param string $hmac_key HMAC key.
	 * @return bool
	 */
	private function verify_solution( $payload, $hmac_key ) {
		if ( ! is_string( $payload ) || strlen( $payload ) > 2048 || ! preg_match( '/^[A-Za-z0-9+\/=]+$/', $payload ) ) {
			return false;
		}

		$decoded = base64_decode( $payload, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- ALTCHA payloads are base64 JSON by spec.
		$data    = false === $decoded ? null : json_decode( $decoded, true );
		if ( ! is_array( $data ) ) {
			return false;
		}

		foreach ( array( 'algorithm', 'challenge', 'salt', 'signature' ) as $key ) {
			if ( ! isset( $data[ $key ] ) || ! is_string( $data[ $key ] ) ) {
				return false;
			}
		}
		if ( ! isset( $data['number'] ) || ! is_int( $data['number'] ) || $data['number'] < 0 ) {
			return false;
		}
		if ( 'SHA-256' !== $data['algorithm'] ) {
			return false;
		}

		// The signature proves this server issued the challenge (the salt and its
		// expiry are covered by the challenge hash below).
		if ( ! hash_equals( hash_hmac( 'sha256', $data['challenge'], $hmac_key ), $data['signature'] ) ) {
			return false;
		}
		if ( ! hash_equals( hash( 'sha256', $data['salt'] . $data['number'] ), $data['challenge'] ) ) {
			return false;
		}

		// Every challenge this plugin issues carries an expiry; one without it was
		// not issued by us (or predates 2.2.3) and is refused.
		$query = (string) wp_parse_url( $data['salt'], PHP_URL_QUERY );
		parse_str( $query, $params );
		$expires = isset( $params['expires'] ) ? absint( $params['expires'] ) : 0;
		if ( ! $expires || $expires < time() ) {
			$this->failure_reason = 'expired';
			return false;
		}

		// Single use: claim the signature until the challenge expires. The claim must be
		// atomic, or parallel requests replaying one solution all pass a read-then-write
		// check. With a persistent object cache, wp_cache_add() is an atomic "add if absent".
		// Without one, set_transient() stores the value with add_option(), whose
		// INSERT ... ON DUPLICATE KEY UPDATE affects 0 rows (returns false) for every
		// request after the first, because the value written is always the same.
		$used_key = 'wbc_altcha_used_' . substr( $data['signature'], 0, 40 );
		$ttl      = max( 60, $expires - time() );
		$claimed  = wp_using_ext_object_cache()
			? wp_cache_add( $used_key, 1, 'wbc_altcha_used', $ttl )
			: set_transient( $used_key, 1, $ttl );
		if ( ! $claimed ) {
			$this->failure_reason = 'already-used';
			return false;
		}

		return true;
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint() {
		return ''; // ALTCHA verifies locally, no external endpoint.
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
