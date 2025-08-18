<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/
 * @since 1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/bp-classes
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
class Recaptcha_bbPress_Reply {

	/**
	 * Template Class Doc Comment
	 * Template Class.
	 */
	public function wbr_bbpress_reply_form_field_reply() {
		$recpatcha_system_ip = get_option( 'wbc_recapcha_ip_to_skip_captcha' );
		if ( $recpatcha_system_ip && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return false;
		}
		$version = get_option( 'wbc_recapcha_version' );
		if ( 'v2' === $version ) {
			$is_enabled                       = get_option( 'recapcha_enable_on_bbpress_reply' );
			$disable_submit_btn               = get_option( 'wbc_recapcha_disable_submitbtn_bbpress_reply' );
			$site_key                         = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                            = get_option( 'recapcha_theme_bbpress_reply' );
			$size                             = get_option( 'recapcha_size_bbpress_reply' );
			$wbc_recapcha_no_conflict         = get_option( 'wbc_recapcha_no_conflict' );
			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$captcha_lable_                   = 'Captcha';
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-bbpress-reply-captcha' != $handle && 'wbc-bbpress-reply-captcha-v3' != $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wbc-bbpress-reply-captcha' );
				?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<p class="wp_register_captcha">
<div name="g-recaptcha-wp-register-wbc" class="g-recaptcha" data-callback="verifyCallback_wp_register"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
<br/>


</p>

<script type="text/javascript">

jQuery(document).ready(function(){
	var myCaptcha = null;
				<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);
					<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
					jQuery('#bbp_reply_submit').attr("disabled", true);
					console.log('close');
						<?php if ( '' === $recapcha_error_msg_captcha_blank ) : ?>
				jQuery('#bbp_reply_submit').attr("title", "<?php echo esc_html( __( 'Please complete the security check to submit your reply.', 'buddypress-recaptcha' ) ); ?>");
		<?php else : ?>
						jQuery('#bbp_reply_submit').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
		<?php endif; ?>
					<?php endif; ?>

	}, 500);
});

var verifyCallback_wp_register = function(response) {
if(response.length!==0){
				<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
				jQuery('#bbp_reply_submit').removeAttr("disabled");
				jQuery('#bbp_reply_submit').removeAttr("title");
				<?php endif; ?>

if (typeof woo_wp_register_captcha_verified === "function") {

woo_wp_register_captcha_verified(response);
}
}


};



</script>
				<?php if ( 'compact' !== $size ) : ?>
<style type="text/css">
[name="g-recaptcha-wp-register-wbc"]{
transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
}
</style>
				<?php endif; ?>
				<?php

			}
		} else {
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_bbpress_reply' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict_v3' );
			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-bbpress-reply' !== $handle && 'wbc-bbpress-reply-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wbc-bbpress-reply-v3' );

				$site_key                                 = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_bbpress_reply_action_v3     = get_option( 'wbc_recapcha_bbpress_reply_action_v3' );
				$wbc_recapcha_bbpress_reply_generation_v3 = get_option( 'wbc_recapcha_bbpress_reply_submit_token_generation_v3' );
				if ( '' === trim( $wbc_recapcha_bbpress_reply_action_v3 ) ) {

					$wbc_recapcha_bbpress_reply_action_v3 = 'bbPress_reply';
				}
				if ( '' === $wbc_recapcha_bbpress_reply_generation_v3 ) {

					$wbc_recapcha_bbpress_reply_generation_v3 = 'no';
				}

				?>
				<input type="hidden" autocomplete="off" name="bbpress-reply-nonce" value="<?php echo esc_html( wp_create_nonce( 'bbpress-reply-nonce' ) ); ?>" />
				<input type="hidden" autocomplete="off" name="wbc_recaptcha_bbpress_reply_token" value="" id="wbc_recaptcha_bbpress_reply_token" />
				<script type="text/javascript">

				<?php $intval_bbpress_reply = uniqid( 'interval_' ); ?>

var <?php echo esc_html( $intval_bbpress_reply ); ?> = setInterval(function() {

if(document.readyState === 'complete') {

clearInterval(<?php echo esc_html( $intval_bbpress_reply ); ?>);


grecaptcha.ready(function () {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_bbpress_reply_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_bbpress_reply_token');
recaptchaResponse.value = token;
});
});

				<?php if ( 'yes' === $wbc_recapcha_bbpress_reply_generation_v3 ) : ?>

setInterval(function() {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_bbpress_reply_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_bbpress_reply_token');
recaptchaResponse.value = token;
});

}, 40 * 1000);

<?php else : ?>
jQuery('#new-post').on('submit', function (e) {

var frm = this;
e.preventDefault();

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_bbpress_reply_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_bbpress_reply_token');
recaptchaResponse.value = token;
frm.submit();

}, function (reason) {

});

});
<?php endif; ?>




}

}, 100);





</script>
				<?php
			}
		}

	}


	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_form_field_reply() {
		echo wp_kses_post( $this->wbr_bbpress_reply_form_field_return() );
		$bbpress_reply_recaptcha_class = new Recaptcha_bbPress_Reply();
		$bbpress_reply_recaptcha_class->wbr_bbpress_reply_v2_checkbox_script();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 *
	 * @param array $return The position of the current token.
	 */
	public function wbr_bbpress_reply_form_field_return( $return = '' ) {
		return $return . $this->wbr_bbpress_reply_captcha_form_field();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_reply_captcha_form_field() {
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
							  <textarea id="g-recaptcha-response-' . $number . '" data-callback="verifyCallback_bbpress_reply" name="g-recaptcha-response"
										   class="g-recaptcha-response"
										   style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
												  margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						  </div>
						</noscript>',
				self::wbr_bbpress_reply_anr_recaptcha_domain()
			);
		}
		return $field;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_reply_anr_recaptcha_domain() {
		$domain = 'google.com';
		return apply_filters( 'anr_recaptcha_domain', $domain );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $response The position of the current token
	 * Template Class.
	 */
	public function wbr_bbpress_reply_recaptcha_verify( $response = false ) {
		static $last_verify = null;
		$nonce              = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $nonce, 'bbp-new-reply' ) ) {
			return false;
		}
		if ( is_user_logged_in() ) {
			return true;
		}
		$version = get_option( 'wbc_recapcha_version' );
		if ( 'v2' === $version ) {
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

			$url = apply_filters( 'anr_google_verify_url', sprintf( 'https://www.%s/recaptcha/api/siteverify', wbr_bbpress_reply_anr_recaptcha_domain() ) );

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
		} else {
			$secre_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key_v3' ) );
			$remoteip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$verify    = false;

			if ( false === $response ) {
				$response = isset( $_POST['wbc_recaptcha_bbpress_reply_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_bbpress_reply_token'] ) ) : '';
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

			$url = apply_filters( 'anr_google_verify_url', sprintf( 'https://www.%s/recaptcha/api/siteverify', wbr_bbpress_reply_anr_recaptcha_domain() ) );

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

		}
		return $verify;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $topic_id The position of the current token.
	 * @param array $forum_id The position of the current token.
	 * Template Class.
	 */
	public function wbr_bbpress_reply_verify( $topic_id = '', $forum_id = '' ) {
		$version = get_option( 'wbc_recapcha_version' );
		$nonce   = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $nonce, 'bbp-new-reply' ) ) {
			return false;
		}
		if ( 'v2' === $version ) {
			$is_enabled = get_option( 'recapcha_enable_on_bbpress_reply' );
			if ( ! wb_recaptcha_restriction_recaptcha_by_ip() ) {
				if ( 'yes' === $is_enabled && empty( $_POST['g-recaptcha-response'] ) ) {
					bbp_add_error( 'anr_error', __( 'Please complete the security check to continue.', 'buddypress-recaptcha' ) );
				}
			}
			if ( ! $this->wbr_bbpress_reply_recaptcha_verify() ) {
				bbp_add_error( 'anr_error', __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ) );
			}
		} else {
			$is_enabled = get_option( 'wbc_recapcha_enable_on_bbpress_reply' );
			if ( 'yes' === $is_enabled && empty( $_POST['wbc_recaptcha_bbpress_reply_token'] ) ) {
				bbp_add_error( 'anr_error', 'reCaptcha is required' );
			}
			if ( ! $this->wbr_bbpress_reply_recaptcha_verify() ) {
				bbp_add_error( 'anr_error', __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ) );
			}
		}

	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wbr_bbpress_reply_v2_checkbox_script() {
		$version            = get_option( 'wbc_recapcha_version' );
		$disable_submit_btn = get_option( 'wbc_recapcha_disable_submitbtn_bbpress_reply' );
		if ( 'v2' === $version ) {
			if( class_exists( 'BuddyPress' ) || class_exists( 'bbPress' ) ){
				if ( is_singular( 'topic' ) || ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' )) ) {
					?>
				<script type="text/javascript" async defers>
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
									'size'  : '<?php echo esc_js( get_option( 'recapcha_size_bbpress_reply' ) ); ?>',
									'theme' : '<?php echo esc_js( get_option( 'recapcha_theme_bbpress_reply' ) ); ?>'
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
					$bbpress_reply_recaptcha_class = new Recaptcha_bbPress_Reply();
					$google_url                    = apply_filters( 'anr_v2_checkbox_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, $bbpress_reply_recaptcha_class->wbr_bbpress_reply_anr_recaptcha_domain() ), $lang );
					?>
		<script src="<?php echo esc_url( $google_url ); ?>"
			async defer>
		</script>
					<?php
				}
			}
		}
	}
}
