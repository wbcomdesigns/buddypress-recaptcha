<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Service class uses simplified naming convention.
/**
 * Google reCAPTCHA v2 Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

/**
 * ReCAPTCHA v2 implementation.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_Recaptcha_V2_Service extends WBC_Captcha_Service_Base {

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id'      => 'recaptcha-v2',
			'service_name'    => __( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ),
			'script_url'      => 'https://www.google.com/recaptcha/api.js',
			'verify_endpoint' => 'https://www.google.com/recaptcha/api/siteverify',
			'response_field'  => 'g-recaptcha-response',
		);
	}

	/**
	 * Get the service identifier
	 *
	 * @return string
	 */
	public function get_service_id() {
		return 'recaptcha-v2';
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
		// Try standard format first (with underscore).
		$site_key = trim( get_option( 'wbc_recaptcha_v2_site_key', '' ) );
		if ( empty( $site_key ) ) {
			// Fallback to hyphen format for backward compatibility.
			$site_key = trim( get_option( 'wbc_recaptcha-v2_site_key', '' ) );
		}
		return $site_key;
	}

	/**
	 * Get the secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		// Try standard format first (with underscore).
		$secret_key = trim( get_option( 'wbc_recaptcha_v2_secret_key', '' ) );
		if ( empty( $secret_key ) ) {
			// Fallback to hyphen format for backward compatibility.
			$secret_key = trim( get_option( 'wbc_recaptcha-v2_secret_key', '' ) );
		}
		return $secret_key;
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used.
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		$handles = array(
			'default'       => 'wbc-woo-captcha',
			'bbpress_topic' => 'wbc-bbpress-topic-captcha',
			'bbpress_reply' => 'wbc-bbpress-reply-captcha',
		);

		return isset( $handles[ $context ] ) ? $handles[ $context ] : $handles['default'];
	}

	/**
	 * Get the script URL for this service
	 *
	 * @return string
	 */
	public function get_script_url() {
		$language = trim( get_option( 'language', '' ) );
		$lang     = '';
		if ( $language ) {
			$lang = '?hl=' . $language;
		}

		$domain = apply_filters( 'anr_recaptcha_domain', 'google.com' ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound	
		return sprintf( 'https://www.%s/recaptcha/api.js%s', $domain, $lang );
	}

	/**
	 * Render the captcha field
	 *
	 * @param string $context The context where captcha is rendered.
	 * @param array  $args    Additional arguments.
	 * @return void
	 */
	public function render( $context, $args = array() ) {
		$site_key = $this->get_site_key();
		if ( empty( $site_key ) ) {
			return;
		}

		// Get context-specific settings.
		$theme          = $this->get_theme_for_context( $context );
		$size           = $this->get_size_for_context( $context );
		$disable_submit = $this->should_disable_submit( $context );

		// Generate unique identifiers.
		$callback = 'verifyCallback_' . str_replace( '-', '_', $context );
		$div_name = 'g-recaptcha-' . $context . '-wbc';

		// Get nonce.
		$nonce_action = $this->get_nonce_action( $context );

		// Render HTML.
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>" />
		<p class="wbc_recaptcha_field">
			<div name="<?php echo esc_attr( $div_name ); ?>" class="g-recaptcha" data-callback="<?php echo esc_attr( $callback ); ?>" data-sitekey="<?php echo esc_attr( $site_key ); ?>" data-theme="<?php echo esc_attr( $theme ); ?>" data-size="<?php echo esc_attr( $size ); ?>"></div>
			<br/>
		</p>
		
		<script type="text/javascript">
		<?php if ( $disable_submit ) : ?>
			jQuery(document).ready(function(){
				<?php $interval_var = uniqid( 'interval_' ); ?>
				var <?php echo esc_html( $interval_var ); ?> = setInterval(function() {
					clearInterval(<?php echo esc_html( $interval_var ); ?>);
					jQuery('<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>').attr("disabled", true);
					jQuery('<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>').attr("title", "<?php echo esc_html( $this->get_error_message( $context ) ); ?>");
				}, 500);
			});
		<?php endif; ?>
		
		var <?php echo esc_js( $callback ); ?> = function(response) {
			if(response.length!==0){
				<?php if ( $disable_submit ) : ?>
				jQuery('<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>').removeAttr("disabled");
				jQuery('<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>').removeAttr("title");
				<?php endif; ?>
				
				if (typeof woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified === "function") {
					woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified(response);
				}
			}
		};
		</script>
		
		<?php if ( 'compact' !== $size ) : ?>
		<style type="text/css">
		[name="<?php echo esc_attr( $div_name ); ?>"]{
			transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
		}
		</style>
			<?php
		endif;
	}

	/**
	 * Verify the captcha response
	 *
	 * @param string $response The captcha response.
	 * @param array  $args     Additional arguments.
	 * @return bool
	 */
	public function verify( $response, $args = array() ) {
		$secret_key = $this->get_secret_key();
		if ( empty( $secret_key ) ) {
			return true; // If not configured, don't block.
		}

		// Verify nonce if present (CSRF protection).
		$context = isset( $args['context'] ) ? $args['context'] : '';
		if ( ! empty( $context ) ) {
			$nonce_action = $this->get_nonce_action( $context );
			if ( isset( $_POST[ $nonce_action ] ) ) {
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_action ] ) ), $nonce_action ) ) {
					return false;
				}
			}
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
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound	
		return apply_filters( 'wbc_captcha_verified', $verified, $result, $response, $this->get_service_id() );
	}

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint() {
		$domain = apply_filters( 'anr_recaptcha_domain', 'google.com' ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return sprintf( 'https://www.%s/recaptcha/api/siteverify', $domain );
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
	 * Check if this service requires no-conflict mode
	 *
	 * @return bool
	 */
	public function requires_no_conflict() {
		return true;
	}

	/**
	 * Get service-specific attributes for the captcha container
	 *
	 * @param string $context The context.
	 * @return array
	 */
	public function get_container_attributes( $context ) {
		return array(
			'class'         => 'g-recaptcha',
			'data-sitekey'  => $this->get_site_key(),
			'data-theme'    => $this->get_theme_for_context( $context ),
			'data-size'     => $this->get_size_for_context( $context ),
			'data-callback' => 'verifyCallback_' . str_replace( '-', '_', $context ),
		);
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
		return array(
			'wbc-woo-captcha',
			'wbc-bbpress-topic-captcha',
			'wbc-bbpress-reply-captcha',
		);
	}

	/**
	 * Get theme for context
	 *
	 * @param string $context The context identifier.
	 * @return string
	 */
	private function get_theme_for_context( $context ) {
		// Use the global theme setting configured in admin.
		return get_option( 'wbc_recaptcha_theme', 'light' );
	}

	/**
	 * Get size for context
	 *
	 * @param string $context The context identifier.
	 * @return string
	 */
	private function get_size_for_context( $context ) {
		// Use the global size setting configured in admin.
		return get_option( 'wbc_recaptcha_size', 'normal' );
	}

	/**
	 * Check if submit button should be disabled
	 *
	 * @param string $context The context identifier.
	 * @return bool
	 */
	private function should_disable_submit( $context ) {
		$option_map = array(
			'wp_login'           => 'wbc_recapcha_disable_submitbtn_wp_login',
			'wp_register'        => 'wbc_recapcha_disable_submitbtn_wp_register',
			'wp_lostpassword'    => 'wbc_recapcha_disable_submitbtn_wp_lost_password',
			'woo_login'          => 'wbc_recapcha_disable_submitbtn_woo_login',
			'woo_register'       => 'wbc_recapcha_disable_submitbtn_woo_signup',
			'woo_lostpassword'   => 'wbc_recapcha_disable_submitbtn_woo_lostpassword',
			'bp_register'        => 'wbc_recapcha_disable_submitbtn_woo_signup_bp',
			'bbpress_topic'      => 'wbc_recapcha_disable_submitbtn_bbpress_topic',
			'bbpress_reply'      => 'wbc_recapcha_disable_submitbtn_bbpress_reply',
			'woo_checkout_guest' => 'wbc_recapcha_disable_submitbtn_guestcheckout',
			'woo_checkout_login' => 'wbc_recapcha_disable_submitbtn_logincheckout',
		);

		$option_name = isset( $option_map[ $context ] ) ? $option_map[ $context ] : '';
		return $option_name ? ( 'yes' === get_option( $option_name ) ) : false;
	}

	/**
	 * Get error message
	 *
	 * @param string $context The context identifier.
	 * @return string
	 */
	public function get_error_message( $context ) {
		$error_msg = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
		$error_msg = str_replace( '[recaptcha]', 'reCAPTCHA', $error_msg );

		if ( empty( $error_msg ) ) {
			$default_messages = array(
				'wp_login'         => __( 'Please complete the security check to log in.', 'buddypress-recaptcha' ),
				'wp_register'      => __( 'Please complete the security check to register.', 'buddypress-recaptcha' ),
				'wp_lostpassword'  => __( 'Please complete the security check to reset your password.', 'buddypress-recaptcha' ),
				'woo_login'        => __( 'Please complete the security check to log in.', 'buddypress-recaptcha' ),
				'woo_register'     => __( 'Please complete the security check to register.', 'buddypress-recaptcha' ),
				'woo_lostpassword' => __( 'Please complete the security check to reset your password.', 'buddypress-recaptcha' ),
				'bp_register'      => __( 'Please complete the security check to register.', 'buddypress-recaptcha' ),
				'bbpress_topic'    => __( 'Please complete the security check to submit your topic.', 'buddypress-recaptcha' ),
				'bbpress_reply'    => __( 'Please complete the security check to submit your reply.', 'buddypress-recaptcha' ),
				'woo_checkout'     => __( 'Please complete the security check to place your order.', 'buddypress-recaptcha' ),
			);

			$error_msg = isset( $default_messages[ $context ] ) ? $default_messages[ $context ] : __( 'Please complete the security check.', 'buddypress-recaptcha' );
		}

		return $error_msg;
	}
}