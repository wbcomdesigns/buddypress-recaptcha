<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
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
class WoocommerceLostpassword {

	/**
	 * Function displays the woocommerce lost password captcha.
	 *
	 * @return void
	 */
	public function woo_extra_lostpassword_fields() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$disable_submit_btn       = get_option( 'wbc_recapcha_disable_submitbtn_woo_lostpassword' );
			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_lostpassword_theme' );
			$size                     = get_option( 'wbc_recapcha_lostpassword_size' );
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_lostpassword' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict' );

			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$captcha_lable_                   = 'Captcha';
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

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
	<p class="woo-lost-password-captcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
	<div name="g-recaptcha-lostpassword-wbc" class="g-recaptcha" data-callback="verifyCallback_woo_lostpassword"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>


	</p>



	<script type="text/javascript">

	var myCaptcha = null;    
				<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);
				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery('.woocommerce-Button').attr("disabled", true);
					<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
							jQuery('.woocommerce-Button').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
					<?php else : ?>
							jQuery('.woocommerce-Button').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
					<?php endif; ?>    

			<?php endif; ?> 
	}    
	}, 100);    


	var verifyCallback_woo_lostpassword = function(response) {

	if(response.length!==0){
				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
				jQuery('.woocommerce-Button').removeAttr("title");
				jQuery('.woocommerce-Button').attr("disabled", false);
			<?php endif; ?>  
				if (typeof woo_lostpassword_captcha_verified === "function") { 
					woo_lostpassword_captcha_verified(response);
				}    
			}
		};  



	</script>

				<?php

			}
		} else {

			$is_enabled                  = get_option( 'wbc_recapcha_enable_on_lostpassword' );
			$wbc_recapcha_no_conflict    = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_generation_v3_woo_fpass = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_fpass' );
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

				$site_key                            = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_lostpassword_action_v3 = get_option( 'wbc_recapcha_lostpassword_action_v3' );
				if ( empty( trim( $wbc_recapcha_lostpassword_action_v3 ) ) ) {

					$wbc_recapcha_lostpassword_action_v3 = 'forgot_password';
				}
				if ( empty( trim( $$wbc_generation_v3_woo_fpass ) ) ) {

					$wbc_generation_v3_woo_fpass = 'no';
				}
				?>
	<input type="hidden" value="" name="wbc_recaptcha_token" id="wbc_recaptcha_token"/>
	<script type="text/javascript">

				<?php $intval_lost_pass = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_lost_pass ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_lost_pass ); ?>);

	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_lostpassword_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
	recaptchaResponse.value = token;
	});
	});



						<?php if ( 'yes' == $wbc_generation_v3_woo_fpass ) : ?>
							setInterval(function() {
								grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_lostpassword_action_v3 ); ?>' }).then(function (token) {

									var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
									recaptchaResponse.value = token;
								});

							}, 40 * 1000); 
						<?php else : ?>
							jQuery('.woocommerce-ResetPassword').on('submit', function (e) {
										var frm = this;
										e.preventDefault();
										grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_lostpassword_action_v3 ); ?>' }).then(function (token) {

										var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
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
}
