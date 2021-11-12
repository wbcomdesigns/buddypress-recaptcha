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
class Regisrtationbp {

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function woo_extra_bp_register_form() {

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}
		if ( 'v2' === strtolower( $re_capcha_version ) ) {
			$disable_submit_btn                 = get_option( 'wbc_recapcha_disable_submitbtn_woo_signup_bp' );
			$wbc_recapcha_hide_label_wpregister = get_option( 'wbc_recapcha_hide_label_signup_bp' );
			$captcha_lable                      = get_option( 'wbc_recapcha_signup_title_bp' );
			$captcha_lable_                     = $captcha_lable;
			$site_key                           = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                              = get_option( 'wbc_recapcha_signup_theme_bp' );
			$size                               = get_option( 'wbc_recapcha_signup_size_bp' );
			$is_enabled                         = get_option( 'wbc_recapcha_enable_on_signup_bp' );
			$wbc_recapcha_no_conflict           = get_option( 'wbc_recapcha_no_conflict' );
			$recapcha_error_msg_captcha_blank   = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' === trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' != $handle && 'wbc-woo-captcha-v3' != $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wbc-woo-captcha' );
				?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<p class="wp_register_captcha">
				<?php
				if ( 'yes' !== $wbc_recapcha_hide_label_wpregister ) :
					?>
<label for="g-recaptcha-wp-register-wbc"><?php echo esc_html( ( '' === trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;</label>
					<?php
				endif;
				?>
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
					jQuery('#submit').attr("disabled", true);
					console.log('close');
						<?php if ( '' === $recapcha_error_msg_captcha_blank ) : ?>
				jQuery('#submit').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
		<?php else : ?>
						jQuery('#submit').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
		<?php endif; ?>
					<?php endif; ?>

	}, 500);
});

var verifyCallback_wp_register = function(response) {
if(response.length!==0){
				<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
				jQuery('#submit').removeAttr("disabled");
				jQuery('#submit').removeAttr("title");
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

			$is_enabled                          = get_option( 'wbc_recapcha_enable_on_signup_bp' );
			$wbc_recapcha_no_conflict            = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_recapcha_wp_disable_wp_register = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_signup_bp' );
			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' != $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wbc-woo-captcha-v3' );

				$site_key                                  = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_wp_register_method_action_v3 = get_option( 'wbc_recapcha_wp_register_method_action_v3' );
				if ( '' === trim( $wbc_recapcha_wp_register_method_action_v3 ) ) {

					$wbc_recapcha_wp_register_method_action_v3 = 'wp_registration';
				}

				if ( '' === trim( $wbc_recapcha_wp_disable_wp_register ) ) {

					$wbc_recapcha_wp_disable_wp_register = 'no';
				}
				?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<input type="hidden" autocomplete="off" name="wbc_recaptcha_wp_register_token" value="" id="wbc_recaptcha_wp_register_token" />

<script type="text/javascript">

				<?php $intval_wp_register = uniqid( 'interval_' ); ?>

var <?php echo esc_html( $intval_wp_register ); ?> = setInterval(function() {

if(document.readyState === 'complete') {

clearInterval(<?php echo esc_html( $intval_wp_register ); ?>);


grecaptcha.ready(function () {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});
});

				<?php if ( 'yes' === $wbc_recapcha_wp_disable_wp_register ) : ?>

setInterval(function() {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});

}, 40 * 1000);

<?php else : ?>
jQuery('#registerform').on('submit', function (e) {

var frm = this;
e.preventDefault();

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

submitval=jQuery("#wp-submit").val();
var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
jQuery('#registerform').prepend('<input type="hidden" name="wp-submit" value="' + submitval + '">');

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
		do_action( 'bp_accept_tos_errors' );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function innovage_validate_user_registration() {
		global $bp;
		$disable_submit_btn               = get_option( 'wbc_recapcha_enable_on_signup_bp' );
		$re_capcha_version                = get_option( 'wbc_recapcha_version' );
		$wbc_recapcha_enable_on_signup_bp = get_option( 'wbc_recapcha_enable_on_signup_bp' );
		if ( 'yes' == $wbc_recapcha_enable_on_signup_bp ) {
			if ( 'v2' !== $re_capcha_version ) {
				$secret_key      = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
				$response        = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_wp_register_token'] ) );
				$verify_response = wp_remote_post(
					'https://www.google.com/recaptcha/api/siteverify',
					array(
						'method'  => 'POST',
						'timeout' => 45,
						'body'    => array(
							'secret'   => $secret_key,
							'response' => $response,
						),

					)
				);
				$response_data = json_decode( $verify_response['body'] );
				if ( ! $response_data->success ) {
					$bp->signup->errors['accept_tos'] = __( 'reCaptcha token is invalid', 'buddypress-recaptcha' );
				}
			} else {
				if ( 'yes' === $disable_submit_btn && empty( $_POST['g-recaptcha-response'] ) ) {
					$bp->signup->errors['accept_tos'] = __( 'reCaptcha token is invalid', 'buddypress-recaptcha' );
				}
			}
		}

		return;
	}

	/**
	 * Template Class Doc Comment
	 * Template Class.
	 */
	public function form_field() {
		$is_enabled = get_option( 'recapcha_enable_on_bbpress_topic' );
		if ( 'yes' === $is_enabled ) {
			$lable      = get_option( 'recapcha_bbpress_topic_title' );
			$hide_lable = get_option( 'recapcha_hide_label_bbpress_topic' );
			if ( ! empty( $lable ) && 'yes' !== $hide_lable ) {
				echo esc_html( $lable );
			}
			echo $this->form_field_return(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Template Class Doc Comment
	 * Template Class.
	 */
	public function form_field_replay() {
		$is_enabled = get_option( 'recapcha_enable_on_bbpress_replay' );
		if ( 'yes' === $is_enabled ) {
			$lable      = get_option( 'recapcha_bbpress_replay_title' );
			$hide_lable = get_option( 'recapcha_hide_label_bbpress_replay' );
			if ( ! empty( $lable ) && 'yes' !== $hide_lable ) {
				echo esc_html( $lable );
			}
			echo $this->form_field_return(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function form_field_bp() {
		echo $this->form_field_return(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$regisrtation_bp = new Regisrtationbp();
		$regisrtation_bp->v2_checkbox_script();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 *
	 * @param array $return The position of the current token.
	 */
	public function form_field_return( $return = '' ) {
		return $return . $this->captcha_form_field();
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function captcha_form_field() {
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
				self::anr_recaptcha_domain()
			);
		}
		return $field;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function anr_recaptcha_domain() {
		$domain = 'google.com';
		return apply_filters( 'anr_recaptcha_domain', $domain );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $response The position of the current token
	 * Template Class.
	 */
	public function verify( $response = false ) {
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
	public function bbp_new_verify( $forum_id ) {
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
	 * @param array $topic_id The position of the current token.
	 * @param array $forum_id The position of the current token.
	 * Template Class.
	 */
	public function bbp_reply_verify( $topic_id = '', $forum_id = '' ) {
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
	public function v2_checkbox_script() {
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
		$regisrtation_bp = new Regisrtationbp();
		$google_url      = apply_filters( 'anr_v2_checkbox_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, $regisrtation_bp->anr_recaptcha_domain() ), $lang );
		?>
		<script src="<?php echo esc_url( $google_url ); ?>"
			async defer>
		</script>
		<?php
	}
}
