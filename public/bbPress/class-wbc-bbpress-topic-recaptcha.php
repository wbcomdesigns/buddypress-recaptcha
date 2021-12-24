<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/
 * @since 1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/bbPress
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_bbPress_Topic {

	/**
	 * Template Class Doc Comment
	 * Template Class.
	 */
	public function wbr_bbpress_topic_form_field() {
		$is_enabled = get_option( 'recapcha_enable_on_bbpress_topic' );
		if ( 'yes' === $is_enabled ) {
			$lable      = get_option( 'recapcha_bbpress_topic_title' );
			$hide_lable = get_option( 'recapcha_hide_label_bbpress_topic' );
			if ( ! empty( $lable ) && 'yes' === $hide_lable ) {
				echo esc_html( $lable );
			}
			echo $this->wbr_bbpress_topic_form_field_return(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_form_field_topic() {
		echo $this->wbr_bbpress_topic_form_field_return(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$bbpress_topic_recaptcha_class = new Recaptcha_bbPress_Topic();
		$bbpress_topic_recaptcha_class->wbr_bbpress_topic_v2_checkbox_script();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 *
	 * @param array $return The position of the current token.
	 */
	public function wbr_bbpress_topic_form_field_return( $return = '' ) {
		return $return . $this->wbr_bbpress_topic_captcha_form_field();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_topic_captcha_form_field() {
		$version  = get_option( 'wbc_recapcha_version' );
		$site_key = get_option( 'wc_settings_tab_recapcha_site_key' );
		$number   = 0;

		$field = '<div class="anr_captcha_field"><div id="anr_captcha_field_' . $number . '" class="anr_captcha_field_div">';

		if ( 'v3' === $version ) {
			$field .= '<input type="hidden" name="g-recaptcha-response" value="" />';
		}

		$field .= '</div></div>';

		if ( 'v2' === $version ) {

			$field .= sprintf(
				'<noscript>
						  <div>
							<div style="width: 302px; height: 422px; position: relative;">
							  <div style="width: 302px; height: 422px; position: absolute;">
								<iframe src="https://www.%s/recaptcha/api/fallback?k=' . $site_key . '"
										frameborder="0" scrolling="no"
										style="width: 302px; height:422px; border-style: none;">
								</iframe>
							  </div>
							</div>
							<div style="width: 300px; height: 60px; border-style: none;
										   bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
										   background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
							  <textarea id="g-recaptcha-response-' . $number . '" name="g-recaptcha-response"
										   class="g-recaptcha-response"
										   style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
												  margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						  </div>
						</noscript>',
				self::wbr_bbpress_topic_recaptcha_domain()
			);
		}
		return $field;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_topic_recaptcha_domain() {
		$domain = 'google.com';
		return apply_filters( 'anr_recaptcha_domain', $domain );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $response The position of the current token
	 * Template Class.
	 */
	public function wbr_bbpress_topic_verify_recaptcha( $response = false ) {
		static $last_verify = null;

		if ( is_user_logged_in() ) {
			return true;
		}

		$secre_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key' ) );
		$remoteip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$verify    = false;

		if ( false === $response ) {
			$response = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
		}

		if ( ! $secre_key ) { // if $secre_key is not set.
			return true;
		}

		if ( ! $response || ! $remoteip ) {
			return $verify;
		}

		if ( null !== $last_verify ) {
			return $last_verify;
		}

		$url = apply_filters( 'anr_google_verify_url', sprintf( 'https://www.%s/recaptcha/api/siteverify', anr_recaptcha_domain() ) );

		// make a POST request to the Google reCAPTCHA Server.
		$request = wp_remote_post(
			$url,
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $secre_key,
					'response' => $response,
					'remoteip' => $remoteip,
				),
			)
		);

		// get the request response body.
		$request_body = wp_remote_retrieve_body( $request );
		if ( ! $request_body ) {
			return $verify;
		}

		$result = json_decode( $request_body, true );
		if ( isset( $result['success'] ) && true === $result['success'] ) {
			if ( 'v3' === get_option( 'wbc_recapcha_version' ) ) {
				$score  = isset( $result['score'] ) ? $result['score'] : 0;
				$action = isset( $result['action'] ) ? $result['action'] : '';
				$verify = anr_get_option( 'score', '0.5' ) <= $score && 'advanced_nocaptcha_recaptcha' === $action;
			} else {
				$verify = true;
			}
		}
		$verify      = apply_filters( 'anr_verify_captcha', $verify, $result, $response );
		$last_verify = $verify;

		return $verify;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $forum_id The position of the current token
	 * Template Class.
	 */
	public function wbr_bbpress_topic_new_verify( $forum_id ) {
		$is_enabled = get_option( 'recapcha_enable_on_bbpress_topic' );
		if ( 'yes' === $is_enabled && empty( $_POST['g-recaptcha-response'] ) ) {
			bbp_add_error( 'anr_error', 'reCaptcha is required' );
		}
		if ( ! $this->verify() ) {
			bbp_add_error( 'anr_error', $this->add_error_to_mgs() );
		}
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_topic_v2_checkbox_script() {
		if ( is_singular( 'forum' ) ) {
			?>
			<script type="text/javascript" async defer>
				var anr_onloadCallback = function() {
					for ( var i = 0; i < document.forms.length; i++ ) {
						var form = document.forms[i];
						var captcha_div = form.querySelector( '.anr_captcha_field_div' );

						if ( null === captcha_div )
							continue;
						captcha_div.innerHTML = '';
						( function( form ) {
							var anr_captcha = grecaptcha.render( captcha_div,{
								'sitekey' : '<?php echo esc_js( trim( get_option( 'wc_settings_tab_recapcha_site_key' ) ) ); ?>',
								'size'  : '<?php echo esc_js( get_option( 'recapcha_size_bbpress_topic' ) ); ?>',
								'theme' : '<?php echo esc_js( get_option( 'recapcha_theme_bbpress_topic' ) ); ?>'
							});
							if ( typeof jQuery !== 'undefined' ) {
								jQuery( document.body ).on( 'checkout_error', function(){
									grecaptcha.reset(anr_captcha);
								});
							}
							if ( typeof wpcf7 !== 'undefined' ) {
								document.addEventListener( 'wpcf7submit', function() {
									grecaptcha.reset(anr_captcha);
								}, false );
							}
						})(form);
					}
				};
			</script>
			<?php
			$language = trim( get_option( 'language' ) );

			$lang = 'eng';
			if ( $language ) {
				$lang = '&hl=' . $language;
			}
			$bbpress_topic_recaptcha_class = new Recaptcha_bbPress_Topic();
			$google_url                    = apply_filters( 'anr_v2_checkbox_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, $bbpress_topic_recaptcha_class->wbr_bbpress_topic_recaptcha_domain() ), $lang );
			?>
	<script src="<?php echo esc_url( $google_url ); ?>"
		async defer>
	</script>
			<?php
		}
	}
}
