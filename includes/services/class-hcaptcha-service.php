<?php
/**
 * hCaptcha Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.1.0
 */

/**
 * hCaptcha implementation - Privacy-focused alternative to reCAPTCHA
 */
class WBC_HCaptcha_Service extends WBC_Captcha_Service_Base {

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id' => 'hcaptcha',
			'service_name' => __( 'hCaptcha', 'buddypress-recaptcha' ),
			'script_url' => 'https://js.hcaptcha.com/1/api.js',
			'verify_endpoint' => 'https://hcaptcha.com/siteverify',
			'response_field' => 'h-captcha-response',
		);
	}

	/**
	 * Get the service identifier
	 *
	 * @return string
	 */
	public function get_service_id() {
		return 'hcaptcha';
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
	 * Get the site key
	 *
	 * @return string
	 */
	public function get_site_key() {
		// Use hCaptcha plugin settings if available
		if ( function_exists( 'hcaptcha' ) && method_exists( hcaptcha(), 'settings' ) ) {
			return hcaptcha()->settings()->get_site_key();
		}

		return trim( get_option( 'wbc_hcaptcha_site_key' ) );
	}

	/**
	 * Get the secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		// Use hCaptcha plugin settings if available
		if ( function_exists( 'hcaptcha' ) && method_exists( hcaptcha(), 'settings' ) ) {
			return hcaptcha()->settings()->get_secret_key();
		}

		return trim( get_option( 'wbc_hcaptcha_secret_key' ) );
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		return 'wbc-hcaptcha-captcha';
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
	 * Render the captcha field
	 *
	 * @param string $context The context where captcha is rendered
	 * @param array  $args    Additional arguments
	 * @return void
	 */
	public function render( $context, $args = array() ) {
		$site_key = $this->get_site_key();
		if ( empty( $site_key ) ) {
			return;
		}

		// Get settings
		$theme = get_option( 'wbc_hcaptcha_theme', 'light' );
		$size = get_option( 'wbc_hcaptcha_size', 'normal' );

		// Generate unique ID
		$div_id = 'h-captcha-' . $context . '-wbc';

		// Get nonce
		$nonce_action = $this->get_nonce_action( $context );

		// Render HTML
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_html( wp_create_nonce( $nonce_action ) ); ?>" />
		<div class="wbc_captcha_field wbc_hcaptcha_field input" style="transform: scale(0.9);margin-left: -20px;">
			<div id="<?php echo esc_attr( $div_id ); ?>"
				 class="h-captcha"
				 data-sitekey="<?php echo esc_attr( $site_key ); ?>"
				 data-theme="<?php echo esc_attr( $theme ); ?>"
				 data-size="<?php echo esc_attr( $size ); ?>"></div>
		</div>
		<?php
	}

	/**
	 * Verify the captcha response
	 *
	 * @param string $response The captcha response
	 * @param array  $args     Additional arguments
	 * @return bool
	 */
	public function verify( $response, $args = array() ) {
		$secret_key = $this->get_secret_key();
		if ( empty( $secret_key ) ) {
			return true; // If not configured, don't block
		}

		if ( empty( $response ) ) {
			return false;
		}

		// Use hCaptcha plugin's API if available
		if ( class_exists( 'HCaptcha\Helpers\API' ) ) {
			$result = \HCaptcha\Helpers\API::verify_request( $response );
			$verified = ( null === $result ); // null means success
		} else {
			// Fallback to manual verification
			$params = array(
				'secret'   => $secret_key,
				'response' => $response,
				'remoteip' => $this->get_user_ip(),
			);

			$result = $this->make_verify_request( $this->get_verify_endpoint(), $params );

			if ( ! $result || ! is_array( $result ) ) {
				return false;
			}

			$verified = isset( $result['success'] ) && true === $result['success'];
		}

		return apply_filters( 'wbc_captcha_verified', $verified, $result ?? array(), $response, $this->get_service_id() );
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint() {
		return $this->config['verify_endpoint'];
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
