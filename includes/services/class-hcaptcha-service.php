<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Service class uses simplified naming convention.
/**
 * HCaptcha Service
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.1.0
 */

/**
 * HCaptcha implementation - Privacy-focused alternative to reCAPTCHA.
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_HCaptcha_Service extends WBC_Captcha_Service_Base {

	/**
	 * Initialize service configuration
	 */
	protected function init_config() {
		$this->config = array(
			'service_id'      => 'hcaptcha',
			'service_name'    => __( 'hCaptcha', 'buddypress-recaptcha' ),
			'script_url'      => 'https://js.hcaptcha.com/1/api.js',
			'verify_endpoint' => 'https://hcaptcha.com/siteverify',
			'response_field'  => 'h-captcha-response',
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
		// Use hCaptcha plugin settings if available.
		if ( function_exists( 'hcaptcha' ) && method_exists( hcaptcha(), 'settings' ) ) {
			return hcaptcha()->settings()->get_site_key();
		}

		return trim( get_option( 'wbc_hcaptcha_site_key', '' ) );
	}

	/**
	 * Get the secret key
	 *
	 * @return string
	 */
	public function get_secret_key() {
		// Use hCaptcha plugin settings if available.
		if ( function_exists( 'hcaptcha' ) && method_exists( hcaptcha(), 'settings' ) ) {
			return hcaptcha()->settings()->get_secret_key();
		}

		return trim( get_option( 'wbc_hcaptcha_secret_key', '' ) );
	}

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used.
	 * @return string
	 */
	public function get_script_handle( $context = 'default' ) {
		return 'wbc-hcaptcha-captcha';
	}

	/**
	 * Get the script URL for this service
	 *
	 * Appends the configured language as ?hl= so hCaptcha respects the
	 * admin-selected locale, matching the reCAPTCHA v2 service behavior.
	 *
	 * @return string
	 */
	public function get_script_url() {
		$url      = $this->config['script_url'];
		$language = trim( get_option( 'language', '' ) );
		if ( '' !== $language ) {
			$url = add_query_arg( 'hl', rawurlencode( $language ), $url );
		}
		return $url;
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

		// Get settings - use the global reCAPTCHA theme/size options.
		// (admin panel provides shared theme/size controls for reCAPTCHA v2 and hCaptcha).
		$theme          = get_option( 'wbc_recaptcha_theme', 'light' );
		$size           = get_option( 'wbc_recaptcha_size', 'normal' );
		$disable_submit = $this->should_disable_submit( $context );

		// Generate unique identifiers.
		$div_id   = 'h-captcha-' . $context . '-wbc';
		$callback = 'hcaptchaCallback_' . str_replace( '-', '_', $context );

		// Get nonce.
		$nonce_action = $this->get_nonce_action( $context );

		// Render HTML.
		?>
		<input type="hidden" autocomplete="off" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>" />
		<div class="wbc_captcha_field wbc_hcaptcha_field input">
			<div id="<?php echo esc_attr( $div_id ); ?>"
				class="h-captcha"
				data-sitekey="<?php echo esc_attr( $site_key ); ?>"
				data-theme="<?php echo esc_attr( $theme ); ?>"
				data-size="<?php echo esc_attr( $size ); ?>"
				data-callback="<?php echo esc_attr( $callback ); ?>"></div>
		</div>
		<?php
		// Per-widget styling.
		//  - `text-align:center` on the wrapper visually balances the widget
		//    inside the form (the iframe is inline-block at every size).
		//  - Scaling is applied ONLY at the default "normal" size — applying
		//    it to "compact" / "invisible" produces a misaligned widget. The
		//    `transform-origin: 0 0` mirrors the reCAPTCHA v2 service so the
		//    widget stays aligned with surrounding fields without needing a
		//    negative-margin hack.
		?>
		<style type="text/css">
		.wbc_hcaptcha_field{ text-align:center; }
		<?php if ( 'compact' !== $size && 'invisible' !== $size ) : ?>
		#<?php echo esc_attr( $div_id ); ?>{
			display:inline-block;
			transform:scale(0.89);-webkit-transform:scale(0.89);
			transform-origin:0 0;-webkit-transform-origin:0 0;
		}
		<?php endif; ?>
		</style>
		<script type="text/javascript">
		<?php if ( $disable_submit ) : ?>
			jQuery( document ).ready( function() {
				<?php $interval_var = uniqid( 'wbc_hcap_int_' ); ?>
				var <?php echo esc_html( $interval_var ); ?> = setInterval( function() {
					clearInterval( <?php echo esc_html( $interval_var ); ?> );
					jQuery( '<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>' ).attr( 'disabled', true );
					jQuery( '<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>' ).attr( 'title', '<?php echo esc_html( $this->get_error_message( $context ) ); ?>' );
				}, 500 );
			} );
		<?php endif; ?>
		window.<?php echo esc_js( $callback ); ?> = function( response ) {
			if ( response && response.length !== 0 ) {
				<?php if ( $disable_submit ) : ?>
				jQuery( '<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>' ).removeAttr( 'disabled' );
				jQuery( '<?php echo esc_js( $this->get_submit_button_selector( $context ) ); ?>' ).removeAttr( 'title' );
				<?php endif; ?>

				if ( typeof woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified === 'function' ) {
					woo_<?php echo esc_js( str_replace( '-', '_', $context ) ); ?>_captcha_verified( response );
				}
			}
		};
		</script>
		<?php
	}

	/**
	 * Check if the submit button should be disabled until the captcha is solved.
	 *
	 * Mirrors WBC_Recaptcha_V2_Service so the "disable submit until verified"
	 * setting works consistently when hCaptcha is the active provider.
	 *
	 * @param string $context Captcha render context.
	 * @return bool
	 */
	protected function should_disable_submit( $context ) {
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
	 * Resolve the user-facing error message for a context.
	 *
	 * @param string $context Captcha render context.
	 * @return string
	 */
	protected function get_error_message( $context ) {
		$error_msg = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
		$error_msg = is_string( $error_msg ) ? str_replace( '[recaptcha]', 'hCaptcha', $error_msg ) : '';

		if ( empty( $error_msg ) ) {
			$error_msg = __( 'Please complete the security check.', 'buddypress-recaptcha' );
		}

		return $error_msg;
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

		// Verify nonce. In strict mode the nonce is mandatory for forms the
		// plugin renders itself (WP / BuddyPress / bbPress) — opt in via the
		// `wbc_captcha_strict_nonce` option (or the same-named filter).
		$context = isset( $args['context'] ) ? $args['context'] : '';
		if ( ! empty( $context ) ) {
			$nonce_action = $this->get_nonce_action( $context );
			//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$strict_nonce = (bool) apply_filters( 'wbc_captcha_strict_nonce', (bool) get_option( 'wbc_captcha_strict_nonce', false ), $context, $this->get_service_id() );
			if ( $strict_nonce ) {
				if ( ! isset( $_POST[ $nonce_action ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_action ] ) ), $nonce_action ) ) {
					return false;
				}
			} elseif ( isset( $_POST[ $nonce_action ] ) ) {
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_action ] ) ), $nonce_action ) ) {
					return false;
				}
			}
		}

		if ( empty( $response ) ) {
			return false;
		}

		// Use hCaptcha plugin's API if available.
		if ( class_exists( 'HCaptcha\Helpers\API' ) ) {
			$result   = \HCaptcha\Helpers\API::verify_request( $response );
			$verified = ( null === $result ); // Null means success.
		} else {
			// Fallback to manual verification.
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
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
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
