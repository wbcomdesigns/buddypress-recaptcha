<?php
/**
 * Cloudflare Turnstile Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

/**
 * Cloudflare Turnstile implementation
 */
class WBC_Turnstile_Service extends WBC_Captcha_Service_Base {
	
	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id' => 'turnstile',
			'service_name' => __( 'Cloudflare Turnstile', 'buddypress-recaptcha' ),
			'script_url' => 'https://challenges.cloudflare.com/turnstile/v0/api.js',
			'verify_endpoint' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			'response_field' => 'cf-turnstile-response',
		);
	}

	/**
	 * Get the service identifier
	 *
	 * @return string
	 */
	public function get_service_id() {
		return 'turnstile';
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
		return trim( get_option( 'wbc_turnstile_site_key' ) );
	}

	/**
	 * Get the secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		return trim( get_option( 'wbc_turnstile_secret_key' ) );
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		return 'wbc-turnstile-captcha';
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

		// Get context-specific settings
		$theme = $this->get_option( 'theme_' . $context, 'light' );
		$size = $this->get_option( 'size_' . $context, 'normal' );
		
		// Generate unique identifiers
		$callback = 'turnstileCallback_' . str_replace( '-', '_', $context );
		$div_id = 'cf-turnstile-' . $context . '-wbc';
		
		// Get nonce
		$nonce_action = $this->get_nonce_action( $context );
		
		// Render HTML
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_html( wp_create_nonce( $nonce_action ) ); ?>" />
		<p class="wbc_captcha_field wbc_turnstile_field">
			<div id="<?php echo esc_attr( $div_id ); ?>" 
				 class="cf-turnstile" 
				 data-sitekey="<?php echo esc_html( $site_key ); ?>"
				 data-theme="<?php echo esc_html( $theme ); ?>"
				 data-size="<?php echo esc_html( $size ); ?>"
				 data-callback="<?php echo esc_attr( $callback ); ?>"></div>
			<br/>
		</p>
		
		<script type="text/javascript">
		window.<?php echo esc_js( $callback ); ?> = function(token) {
			if(token){
				// Handle successful verification
				var submitBtn = jQuery('<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>');
				if(submitBtn.length) {
					submitBtn.removeAttr("disabled");
					submitBtn.removeAttr("title");
				}
				
				if (typeof woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified === "function") {
					woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified(token);
				}
			}
		};
		</script>
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
		
		return apply_filters( 'wbc_captcha_verified', $verified, $result, $response, $this->get_service_id() );
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
		return false; // Turnstile doesn't typically conflict with other scripts
	}

	/**
	 * Get service-specific attributes for the captcha container
	 *
	 * @param string $context The context
	 * @return array
	 */
	public function get_container_attributes( $context ) {
		return array(
			'class' => 'cf-turnstile',
			'data-sitekey' => $this->get_site_key(),
			'data-theme' => $this->get_option( 'theme_' . $context, 'light' ),
			'data-size' => $this->get_option( 'size_' . $context, 'normal' ),
			'data-callback' => 'turnstileCallback_' . str_replace( '-', '_', $context ),
		);
	}
}