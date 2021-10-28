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
class WoocommerceReviewOrder {

	/**
	 * This Function displays the woocommerce extra checkout field.
	 *
	 * @return void
	 */
	public function woo_extra_checkout_fields() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$disable_submit_btn                = get_option( 'wbc_recapcha_disable_submitbtn_guestcheckout' );
			$disable_submit_btn_login_checkout = get_option( 'wbc_recapcha_disable_submitbtn_logincheckout' );
			$wbc_recapcha_hide_label_checkout  = get_option( 'wbc_recapcha_hide_label_checkout' );
			$captcha_lable                     = get_option( 'wbc_recapcha_guestcheckout_title' );
			$captcha_lable_                    = get_option( 'wbc_recapcha_guestcheckout_title' );
			$refresh_lable                     = get_option( 'wbc_recapcha_guestcheckout_refresh' );
			if ( '' == esc_html( $refresh_lable ) ) {

				$refresh_lable = __( 'Refresh Captcha', 'buddypress-recaptcha' );
			}
			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_guestcheckout_theme' );
			$size                     = get_option( 'wbc_recapcha_guestcheckout_size' );
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
			$is_enabled_logincheckout = get_option( 'wbc_recapcha_enable_on_logincheckout' );
			$wbc_recapcha_guest_recpacha_refersh_on_error = get_option( 'wbc_recapcha_guest_recpacha_refersh_on_error' );
			$wbc_recapcha_login_recpacha_refersh_on_error = get_option( 'wbc_recapcha_login_recpacha_refersh_on_error' );

			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' == trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			if ( 'yes' == $is_enabled && ! is_user_logged_in() ) {

				wp_enqueue_script( 'jquery' );

				?>
	<p class="guest-checkout-recaptcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php
				if ( 'yes' != $wbc_recapcha_hide_label_checkout ) :
					?>
	<label for="reg_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label>
					<?php
			endif;
				?>
	<div id="g-recaptcha-checkout-wbc" name="g-recaptcha" class="g-recaptcha-" data-callback="verifyCallback_add_guestcheckout"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
	<div id='refresh_captcha' style="width:100%;padding-top:5px"> 
	<a href="javascript:grecaptcha.reset(myCaptcha);" style="clear:both"><?php echo esc_html( $refresh_lable ); ?></a>
	</div>    

	</p>
	<script type="text/javascript">
	var myCaptcha = null;
	var capchaChecked = false;
				<?php $intval_guest_checkout = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_guest_checkout ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_guest_checkout ); ?>);

				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery("#place_order").attr("disabled", true);
					<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>    
	<?php endif; ?>



	if (typeof (grecaptcha.render) !== 'undefined' && myCaptcha === null) {

				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	try{
	myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
	'sitekey': '<?php echo esc_html( $site_key ); ?>',
	'callback' : verifyCallback_add_guestcheckout
	});
	}catch(error){}
	<?php else : ?>

	try{
	myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
	'sitekey': '<?php echo esc_html( $site_key ); ?>',
												'callback' : verifyCallback_add_guestcheckout
	});
	}catch(error){}
	<?php endif; ?>

	}       

	jQuery(document).on('updated_checkout', function () {

	if (typeof (grecaptcha.render) !== 'undefined' && window.myCaptcha === null) {

	try{
	myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
	'sitekey': '<?php echo esc_html( $site_key ); ?>',
	'callback' : verifyCallback_add_guestcheckout
	});
	}catch(error){}

	}
	});



	}    
	}, 100); 




	var verifyCallback_add_guestcheckout = function(response) {

	if(response.length!==0){ 

				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
		jQuery("#place_order").removeAttr("title");
		jQuery("#place_order").attr("disabled", false);
																				capchaChecked=true;
	<?php endif; ?>   

	if (typeof woo_guest_checkout_recaptcha_verified === "function") { 

		woo_guest_checkout_recaptcha_verified(response);
	}
	}

	};
				<?php if ( 'yes' == $wbc_recapcha_guest_recpacha_refersh_on_error ) : ?>                                                                                                     
	jQuery('body').on('checkout_error', function(){
	grecaptcha.reset(window.myCaptcha); 
					<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery("#place_order").attr("disabled", true);
						<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>    
		<?php endif; ?>
		  
	});

	jQuery( document ).ajaxComplete(function() {

	if(jQuery(".woocommerce-error").is(":visible") || jQuery(".woocommerce_error").is(":visible")){
		grecaptcha.reset(window.myCaptcha); 
					<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery("#place_order").attr("disabled", true);
						<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>    
		<?php endif; ?>
	}

	});
	<?php endif; ?>     

				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>

		jQuery('#createaccount').on('click',function(){
			if(jQuery("#place_order").is(":disabled") || capchaChecked==false){
				setTimeout(function(){ jQuery("#place_order").attr("disabled", true); }, 100);

			}
		});  
		jQuery('#account_username').on('keyup',function(){
			if(jQuery("#place_order").is(":disabled") || capchaChecked==false){
				setTimeout(function(){ jQuery("#place_order").attr("disabled", true); }, 300);

			}
		});  
		jQuery('#account_password').on('keyup',function(){
			if(jQuery("#place_order").is(":disabled") || capchaChecked==false){
				setTimeout(function(){ jQuery("#place_order").attr("disabled", true); }, 300);
			}
		});  
		<?php endif; ?>


	</script>
				<?php

			} elseif ( 'yes' == $is_enabled_logincheckout && is_user_logged_in() ) {

				wp_enqueue_script( 'jquery' );

				?>
	<p class="login-checkout-captcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php
				if ( 'yes' != $wbc_recapcha_hide_label_checkout ) :
					?>
	<label for="reg_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label>
					<?php
			endif;
				?>
	<div id="g-recaptcha-checkout-wbc" name="g-recaptcha" class="g-recaptcha-" data-callback="verifyCallback_add_logincheckout"   data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
	<div id='refresh_captcha' style="width:100%;padding-top:5px"> <a href="javascript:grecaptcha.reset(myCaptcha);"><?php echo esc_html( $refresh_lable ); ?></a></div>

	</p>
	<script type="text/javascript">
	var myCaptcha = null;    
				<?php $intval_login_checkout = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_login_checkout ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_login_checkout ); ?>);


				<?php if ( 'yes' == trim( $disable_submit_btn_login_checkout ) ) : ?>
	jQuery("#place_order").attr("disabled", true);

					<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>    
	<?php endif; ?>



	if (typeof (grecaptcha.render) !== 'undefined' && myCaptcha === null) {

	try{
	myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
	'sitekey': '<?php echo esc_html( $site_key ); ?>',
	'callback' : verifyCallback_add_logincheckout
	});
	}catch(error){}

	}       

	jQuery(document).on('updated_checkout', function () {

	if (typeof (grecaptcha.render) !== 'undefined' && myCaptcha === null) {

	try{
	myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
	'sitekey': '<?php echo esc_html( $site_key ); ?>',
	'callback' : verifyCallback_add_logincheckout
	});
	}catch(error){}

	}
	});

	}    
	}, 100); 


	var verifyCallback_add_logincheckout = function(response) {

	if(response.length!==0){ 

				<?php if ( 'yes' == trim( $disable_submit_btn_login_checkout ) ) : ?>

	jQuery("#place_order").removeAttr("title");
	jQuery("#place_order").attr("disabled", false);

	<?php endif; ?>

	if (typeof woo_login_checkout_recaptcha_verified === "function") { 

		woo_login_checkout_recaptcha_verified(response);
	}
	}



	};

				<?php if ( 'yes' == $wbc_recapcha_login_recpacha_refersh_on_error ) : ?>                                                                                                     
			jQuery('body').on('checkout_error', function(){

	myCaptcha=grecaptcha.reset(myCaptcha);
						<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
								jQuery("#place_order").attr("disabled", true);
							<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
								jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
						<?php else : ?>
								jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
						<?php endif; ?>    
						<?php endif; ?>
					  

	});
				jQuery( document ).ajaxComplete(function() {
						if(jQuery(".woocommerce-error").is(":visible") || jQuery(".woocommerce_error").is(":visible")){
							grecaptcha.reset(window.myCaptcha); 
							<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
									jQuery("#place_order").attr("disabled", true);
									<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
									jQuery("#place_order").attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
							<?php else : ?>
									jQuery("#place_order").attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
							<?php endif; ?>    
							<?php endif; ?>
						}

					});
	<?php endif; ?>   

	</script>
				<?php

			}
		} else {

			$is_enabled               = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
			$is_enabled_logincheckout = get_option( 'wbc_recapcha_enable_on_logincheckout' );

			if ( ( 'yes' == $is_enabled && ! is_user_logged_in() ) || ( 'yes' == $is_enabled_logincheckout && is_user_logged_in() ) ) {

				wp_enqueue_script( 'jquery' );

				$site_key                                = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_checkout_action_v3         = get_option( 'wbc_recapcha_checkout_action_v3' );
				$wbc_recapcha_wp_disable_to_woo_checkout = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_checkout' );
				if ( '' == $wbc_recapcha_checkout_action_v3 ) {

					$wbc_recapcha_checkout_action_v3 = 'checkout';
				}

				if ( '' == $wbc_recapcha_wp_disable_to_woo_checkout ) {

					$wbc_recapcha_wp_disable_to_woo_checkout = 'no';
				}

				?>
	<input type="hidden" value="" name="wbc_checkout_token" id="wbc_checkout_token"/>
	<script type="text/javascript">

				<?php $intval_guest_checkout = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_guest_checkout ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_guest_checkout ); ?>);

	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;
	}, function (reason) {

	});
	});


					<?php if ( 'no' == $wbc_recapcha_wp_disable_to_woo_checkout ) : ?>  
				  
	var checkout_form = jQuery('form.checkout');

	/*checkout_form.on('checkout_place_order', function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;

	}, function (reason) {

	});
	});*/


	jQuery(document).on('updated_checkout', function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;

	}, function (reason) {

	});
	});
	jQuery(document).on('checkout_error', function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;

	}, function (reason) {

	});
	});
		jQuery( document ).ajaxComplete(function() {

										if(jQuery(".woocommerce-error").is(":visible") || jQuery(".woocommerce_error").is(":visible")){

												grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

													var recaptchaResponse = document.getElementById('wbc_checkout_token');
													recaptchaResponse.value = token;

												}, function (reason) {
												});
										}

									});
	jQuery(document).on('payment_method_selected', function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;

	}, function (reason) {

	});
	});
								  
						<?php else : ?>
						  
							setInterval(function() {

								grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

									var recaptchaResponse = document.getElementById('wbc_checkout_token');
									recaptchaResponse.value = token;
								});

								}, 40 * 1000); 

						<?php endif; ?>

	}    

	}, 100);   





	</script>
				<?php
			}
		}

	}
}
