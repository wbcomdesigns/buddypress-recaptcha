<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WoocommerceRegister {

	/**
	 * Function displays the woocommerce registration captcha.
	 *
	 * @return void
	 */
	public function woo_extra_register_fields() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}
		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$disable_submit_btn               = get_option( 'wbc_recapcha_disable_submitbtn_woo_signup' );
			$wbc_recapcha_hide_label_signup   = get_option( 'wbc_recapcha_hide_label_signup' );
			$captcha_lable                    = trim( get_option( 'wbc_recapcha_signup_title' ) );
			$captcha_lable_                   = $captcha_lable;
			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' == trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_signup_theme' );
			$size                     = get_option( 'wbc_recapcha_signup_size' );
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_signup' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict' );

			if ( 'yes' == $is_enabled ) {

				if ( 'yes' == $wbc_recapcha_no_conflict ) {
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
	<p id="woo_reg_recaptcha" class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php
				if ( 'yes' != $wbc_recapcha_hide_label_signup ) :
					?>
	<label for="reg_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? esc_html( __( 'Captcha', 'recaptcha-for-woocommerce' ) ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label>
					<?php
			endif;
				?>
	<div name="g-recaptcha" class="g-recaptcha" data-callback="verifyCallback_woo_signup" data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
	</p>
	<script type="text/javascript">
				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>



	var myCaptcha = null;    
					<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);

	jQuery('button[name$="register"]').attr("disabled", true);
					<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery('button[name$="register"]').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'recaptcha-for-woocommerce' ) ); ?>");
	<?php else : ?>
	jQuery('button[name$="register"]').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>    


	}    
	}, 100);    

	<?php endif; ?>

				var verifyCallback_woo_signup = function(response) {
					if(response.length!==0){ 

						<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
										jQuery('button[name$="register"]').removeAttr("title");
										jQuery('button[name$="register"]').attr("disabled", false);
						<?php endif; ?>  

										if (typeof woo_register_recaptcha_verified === "function") { 

												woo_register_recaptcha_verified(response);
										}
				}

				};  


	</script>

				<?php

			}
		} else {

			$is_enabled               = get_option( 'wbc_recapcha_enable_on_signup' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict_v3' );
			if ( 'yes' == $is_enabled ) {

				if ( 'yes' == $wbc_recapcha_no_conflict ) {

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
				wp_enqueue_script( 'wbc-woo-captcha-v3' );

				$site_key                      = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_signup_action_v3 = get_option( 'wbc_recapcha_signup_action_v3' );
				if ( '' == trim( $wbc_recapcha_signup_action_v3 ) ) {

					$wbc_recapcha_signup_action_v3 = 'signup';
				}
				$wbc_recapcha_disable_v3_woo_signup = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_signup' );
				if ( '' == trim( $wbc_recapcha_disable_v3_woo_signup ) ) {

					$wbc_recapcha_disable_v3_woo_signup = 'no';
				}
				?>
	<input type="hidden" value="" name="wbc_recaptcha_register_token" id="wbc_recaptcha_register_token"/>
	<script type="text/javascript">

				<?php $intval_register = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_register ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_register ); ?>);

	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_signup_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_register_token');
	recaptchaResponse.value = token;
	}, function (reason) {

	});
	});



				<?php if ( 'yes' == $wbc_recapcha_disable_v3_woo_signup ) : ?>     

	setInterval(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_signup_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_register_token');
	recaptchaResponse.value = token;
	});

	}, 40 * 1000); 

	<?php else : ?>
	jQuery('.woocommerce-form-register').on('submit', function (e) {
	var frm = this;
	e.preventDefault();
	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_signup_action_v3 ); ?>' }).then(function (token) {

	submitval=jQuery(".woocommerce-form-register__submit").val();
	var recaptchaResponse = document.getElementById('wbc_recaptcha_register_token');
	recaptchaResponse.value = token;
	jQuery('.woocommerce-form-register').prepend('<input type="hidden" name="register" value="' + submitval + '">');


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
}
