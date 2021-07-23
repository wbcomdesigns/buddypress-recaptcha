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
class WoocommerceLogin {
	public function woo_extra_login_fields() {

		$reCapcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $reCapcha_version ) {
			$reCapcha_version = 'v2';
		}
		if ( 'v2' == strtolower( $reCapcha_version ) ) {

			$disable_submit_btn            = get_option( 'wbc_recapcha_disable_submitbtn_woo_login' );
			$wbc_recapcha_hide_label_login = get_option( 'wbc_recapcha_hide_label_login' );
			$captcha_lable                 = get_option( 'wbc_recapcha_login_title' );
			$captcha_lable_                = $captcha_lable;

			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_login_theme' );
			$size                     = get_option( 'wbc_recapcha_login_size' );
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_login' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict' );

			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' == trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
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
	<p class="woo-login-captcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php
				if ( 'yes' != $wbc_recapcha_hide_label_login ) :
					?>
	<label for="login_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'recaptcha-for-woocommerce' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label>
					<?php
			endif;
				?>
	<div name="g-recaptcha-login-wbc" class="g-recaptcha" data-callback="verifyCallback_woo_login" data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>


	</p>



	<script type="text/javascript">

				<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

		clearInterval(<?php echo esc_html( $intval_signup ); ?>);

														 <?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
			jQuery('button[name$="login"]').attr("disabled", true);
																<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
				jQuery('button[name$="login"]').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'recaptcha-for-woocommerce' ) ); ?>");
			<?php else : ?>
				jQuery('button[name$="login"]').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
			<?php endif; ?>    

														 <?php endif; ?>        
		}    
	}, 100);    


									var verifyCallback_woo_login = function(response) {

										if(response.length!==0){ 
											<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
												jQuery('button[name$="login"]').removeAttr("title");
												jQuery('button[name$="login"]').attr("disabled", false);
											<?php endif; ?>    


												if (typeof woo_login_captcha_verified === "function") { 

													 woo_login_captcha_verified(response);
												 }

											}

									};  
									
									
								  
	</script>


				<?php

			}
		} else {

			$is_enabled                        = get_option( 'wbc_recapcha_enable_on_login' );
			$wbc_recapcha_no_conflict          = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_token_generation_v3_woo_login = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_login' );
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

				$site_key                     = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_login_action_v3 = get_option( 'wbc_recapcha_login_action_v3' );
				if ( '' == trim( $wbc_recapcha_login_action_v3 ) ) {

					$wbc_recapcha_login_action_v3 = 'login';
				}

				if ( '' == trim( $wbc_token_generation_v3_woo_login ) ) {

					$wbc_token_generation_v3_woo_login = 'no';
				}

				?>
	<input type="hidden" value="" name="wbc_recaptcha_login_token" id="wbc_recaptcha_login_token"/>
	<script type="text/javascript">

				<?php $intval_login = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_login ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_login ); ?>);

			grecaptcha.ready(function () {
				
				grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_login_action_v3 ); ?>' }).then(function (token) {
				
					var recaptchaResponse = document.getElementById('wbc_recaptcha_login_token');
					recaptchaResponse.value = token;
				}, function (reason) {
				  
				});
			});
			
			
	  
													   <?php if ( 'yes' == $wbc_token_generation_v3_woo_login ) : ?>
															setInterval(function() {
																	
																grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_login_action_v3 ); ?>' }).then(function (token) {

																	var recaptchaResponse = document.getElementById('wbc_recaptcha_login_token');
																	recaptchaResponse.value = token;
																});

															}, 40 * 1000); 
													   <?php else : ?>
														jQuery('.woocommerce-form-login').on('submit', function (e) {
																	 var frm = this;
																	 e.preventDefault();
																	 grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_login_action_v3 ); ?>' }).then(function (token) {

																	  submitval=jQuery(".woocommerce-form-login__submit").val();
																	  var recaptchaResponse = document.getElementById('wbc_recaptcha_login_token');
																	   recaptchaResponse.value = token;
																		jQuery('.woocommerce-form-login').prepend('<input type="hidden" name="login" value="' + submitval + '">');


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
