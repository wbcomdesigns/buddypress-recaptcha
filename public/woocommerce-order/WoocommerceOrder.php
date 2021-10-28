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
class WoocommerceOrder {

	/**
	 * Function displays the woocommerce checkout pay order captcha.
	 *
	 * @return void
	 */
	public function woo_extra_checkout_fields_pay_order() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$disable_submit_btn               = get_option( 'wbc_recapcha_disable_submitbtn_payfororder' );
			$wbc_recapcha_hide_label_checkout = get_option( 'wbc_recapcha_hide_label_checkout' );
			$captcha_lable                    = get_option( 'wbc_recapcha_guestcheckout_title' );
			$captcha_lable_                   = get_option( 'wbc_recapcha_guestcheckout_title' );
			$refresh_lable                    = get_option( 'wbc_recapcha_guestcheckout_refresh' );
			if ( '' == esc_html( $refresh_lable ) ) {

				$refresh_lable = __( 'Refresh Captcha', 'buddypress-recaptcha' );
			}
			$site_key   = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme      = get_option( 'wbc_recapcha_guestcheckout_theme' );
			$size       = get_option( 'wbc_recapcha_guestcheckout_size' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_payfororder' );

			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' == trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			if ( 'yes' == $is_enabled ) {

				wp_enqueue_script( 'jquery' );

				?>
	<p class="payorder-checkout-recaptcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
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
	<?php endif; ?>

	if (typeof woo_guest_checkout_recaptcha_verified === "function") {

	woo_guest_checkout_recaptcha_verified(response);
	}
	}

	};



	</script>
				<?php

			}
		} else {

			$is_enabled                              = get_option( 'wbc_recapcha_enable_on_payfororder' );
			$wbc_recapcha_wp_disable_to_woo_checkout = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_checkout' );
			if ( 'yes' == $is_enabled ) {

				wp_enqueue_script( 'jquery' );

				$site_key                        = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_checkout_action_v3 = get_option( 'wbc_recapcha_checkout_action_v3' );
				if ( '' == $wbc_recapcha_checkout_action_v3 ) {

					$wbc_recapcha_checkout_action_v3 = 'checkout';
				}
				if ( '' == $wbc_recapcha_wp_disable_to_woo_checkout ) {

					$wbc_recapcha_wp_disable_to_woo_checkout = 'no';
				}

				?>
	<input type="hidden" value="" name="wbc_checkout_token" id="wbc_checkout_token"/>
	<script type="text/javascript">

				<?php $intval_payorder_checkout = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_payorder_checkout ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_payorder_checkout ); ?>);

	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;
	}, function (reason) {

	});
	});



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

				<?php if ( 'yes' == $wbc_recapcha_wp_disable_to_woo_checkout ) : ?>

	setInterval(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
	recaptchaResponse.value = token;
	});

	}, 40 * 1000);

	<?php else : ?>

	jQuery('#order_review').on('submit', function (e) {
	var frm = this;
	e.preventDefault();
	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_checkout_token');
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
	 * Verify woocommerce pay order captcha form.
	 */
	public function woo_verify_pay_order_captcha() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$captcha_lable = get_option( 'wbc_recapcha_guestcheckout_title' );
			if ( '' == trim( $captcha_lable ) ) {

				$captcha_lable = 'recaptcha';
			}

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$wbc_recapcha_checkout_timeout          = get_option( 'wbc_recapcha_checkout_timeout' );
			if ( null == $wbc_recapcha_checkout_timeout || '' == $wbc_recapcha_checkout_timeout ) {

				$wbc_recapcha_checkout_timeout = 3;
			}
			$secret_key = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_payfororder' );

			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', '<strong>' . ucfirst( $captcha_lable ) . '</strong>', $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', '<strong>' . $captcha_lable . '</strong>', $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', '<strong>' . $captcha_lable . '</strong>', $recapcha_error_msg_captcha_invalid );

			if ( 'yes' == $is_enabled && ( ( isset( $_POST['woocommerce-pay-nonce'] ) && ! empty( $_POST['woocommerce-pay-nonce'] ) ) || ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) ) ) {

				$nonce_value = '';
				if ( isset( $_REQUEST['woocommerce-pay-nonce'] ) || isset( $_REQUEST['_wpnonce'] ) ) {

					if ( isset( $_REQUEST['woocommerce-pay-nonce'] ) && ! empty( $_REQUEST['woocommerce-pay-nonce'] ) ) {

						$nonce_value = sanitize_text_field( $_REQUEST['woocommerce-pay-nonce'] );
					} elseif ( isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ) {

						$nonce_value = sanitize_text_field( $_REQUEST['_wpnonce'] );
					}
				}

				if ( wp_verify_nonce( $nonce_value, 'woocommerce-pay' ) ) {

					if ( 'yes' != get_transient( $nonce_value ) ) {

						if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {

							// Google reCAPTCHA API secret key.
							$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

							// Verify the reCAPTCHA response.
							$verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

							if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

								// Decode json data.
								$responseData = json_decode( $verifyResponse['body'] );

								// If reCAPTCHA response is valid.
								if ( ! $responseData->success ) {

									if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										wc_add_notice( __( 'Invalid recaptcha.', 'buddypress-recaptcha' ), 'error' );
										return;

									} else {

										wc_add_notice( $recapcha_error_msg_captcha_invalid, 'error' );
										return;

									}
								} else {

									if ( 0 != $wbc_recapcha_checkout_timeout ) {

										set_transient( $nonce_value, 'yes', ( $wbc_recapcha_checkout_timeout * 60 ) );
									}
								}
							} else {

								if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

									wc_add_notice( __( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ), 'error' );
									return;

								} else {

									wc_add_notice( $recapcha_error_msg_captcha_no_response, 'error' );
									return;

								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

								wc_add_notice( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ), 'error' );
								return;

							} else {
								wc_add_notice( $recapcha_error_msg_captcha_blank, 'error' );
								return;

							}
						}
					}
				} else {

					wc_add_notice( __( 'Could not verify request.', 'buddypress-recaptcha' ), 'error' );
					return;

				}
			}
		} else {

			$wbc_recapcha_checkout_score_threshold_v3 = get_option( 'wbc_recapcha_checkout_score_threshold_v3' );
			if ( '' == $wbc_recapcha_checkout_score_threshold_v3 ) {

				$wbc_recapcha_checkout_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_checkout_action_v3 = get_option( 'wbc_recapcha_checkout_action_v3' );
			if ( '' == $wbc_recapcha_checkout_action_v3 ) {

				$wbc_recapcha_checkout_action_v3 = 'checkout';
			}

			$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
			$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
			$is_enabled                             = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
			$wbc_recapcha_enable_on_logincheckout   = get_option( 'wbc_recapcha_enable_on_logincheckout' );

			$wbc_recapcha_checkout_timeout = get_option( 'wbc_recapcha_checkout_timeout' );
			if ( null == $wbc_recapcha_checkout_timeout || '' == $wbc_recapcha_checkout_timeout ) {

				$wbc_recapcha_checkout_timeout = 3;
			}

			$nonce_value = '';
			if ( isset( $_REQUEST['woocommerce-pay-nonce'] ) || isset( $_REQUEST['_wpnonce'] ) ) {

				if ( isset( $_REQUEST['woocommerce-pay-nonce'] ) && ! empty( $_REQUEST['woocommerce-pay-nonce'] ) ) {

					$nonce_value = sanitize_text_field( $_REQUEST['woocommerce-pay-nonce'] );
				} elseif ( isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ) {

					$nonce_value = sanitize_text_field( $_REQUEST['_wpnonce'] );
				}
			}

			if ( 'yes' == $is_enabled && ( ( isset( $_POST['woocommerce-pay-nonce'] ) && ! empty( $_POST['woocommerce-pay-nonce'] ) ) || ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) ) && wp_verify_nonce( $nonce_value, 'woocommerce-pay' ) ) {

				if ( 'yes' != get_transient( $nonce_value ) ) {

					if ( isset( $_POST['wbc_checkout_token'] ) && ! empty( $_POST['wbc_checkout_token'] ) ) {
						// Google reCAPTCHA API secret key.
						$response = sanitize_text_field( $_POST['wbc_checkout_token'] );

						// Verify the reCAPTCHA response.
						$verifyResponse = wp_remote_post(
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

						if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

							// Decode json data.
							$responseData = json_decode( $verifyResponse['body'] );

							// If reCAPTCHA response is valid.
							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

												wc_add_notice( __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ), 'error' );
												return;

								} else {

											wc_add_notice( $recapcha_error_msg_captcha_invalid, 'error' );
											return;

								}
							} else {

								if ( $responseData->score < $wbc_recapcha_checkout_score_threshold_v3 || $responseData->action != $wbc_recapcha_checkout_action_v3 ) {

									if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										wc_add_notice( __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ), 'error' );
										return;

									} else {

										wc_add_notice( $recapcha_error_msg_captcha_invalid, 'error' );
										return;

									}
								} else {

									if ( 0 != $wbc_recapcha_checkout_timeout ) {

										set_transient( $nonce_value, 'yes', ( $wbc_recapcha_checkout_timeout * 60 ) );
									}
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

								wc_add_notice( __( 'Could not get response from reCAPTCHA server.', 'buddypress-recaptcha' ), 'error' );
								return;

							} else {

								wc_add_notice( $recapcha_error_msg_captcha_no_response, 'error' );
								return;

							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

							wc_add_notice( __( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ), 'error' );
							return;

						} else {

							wc_add_notice( $recapcha_error_msg_captcha_blank, 'error' );
							return;

						}
					}
				}
			}
		}

	}

	/**
	 * Checks the Payment is complate or not.
	 *
	 * @param  int $order_id Order ID.
	 */
	public function woo_payment_complete( $order_id ) {

		$nonece = isset( $_POST['woocommerce-process-checkout-nonce'] ) ? wc_clean( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' == trim( $nonece ) ) {

			$nonece = isset( $_POST['_wpnonce'] ) ? wc_clean( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		}

		if ( wp_verify_nonce( $nonece, 'woocommerce-process_checkout' ) ) {
			if ( ! empty( $nonece ) ) {

				delete_transient( $nonece );
			}
		}

	}

	/**
	 * Function displays the woocommerce payment request button captcha.
	 */
	public function woocommerce_payment_request_btn_captcha() {

		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );

		$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

				$wbc_recaptcha_login_recpacha_for_req_btn = get_option( 'wbc_recaptcha_login_recpacha_for_req_btn' );
			if ( '' == $wbc_recaptcha_login_recpacha_for_req_btn ) {
				$wbc_recaptcha_login_recpacha_for_req_btn = 'no';
			}
			if ( 'yes' == $wbc_recaptcha_login_recpacha_for_req_btn ) {

				$wbc_recapcha_hide_label_checkout = get_option( 'wbc_recapcha_hide_label_checkout' );
				$captcha_lable                    = get_option( 'wbc_recapcha_guestcheckout_title' );
				$captcha_lable_                   = get_option( 'wbc_recapcha_guestcheckout_title' );
				$refresh_lable                    = get_option( 'wbc_recapcha_guestcheckout_refresh' );
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

					if ( 'yes' == $wbc_recapcha_no_conflict ) {

								global $wp_scripts;

									$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) ) {
														wp_dequeue_script( $handle );

														break;
								}
							}
						}
					}
								wp_enqueue_script( 'jquery' );
								wp_enqueue_script( 'wbc-woo-captcha' );

					?>
							<p class="guest-checkout-recaptcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<?php
								if ( 'yes' != $wbc_recapcha_hide_label_checkout ) :
									?>
						<label for="reg_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label><?php endif; ?>
							<div id="g-recaptcha-checkout-wbc" name="g-recaptcha" class="g-recaptcha-" data-callback="verifyCallback_add_guestcheckout"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
													<div id='refresh_captcha' style="width:100%;padding-top:5px">
															<a href="javascript:grecaptcha.reset(myCaptcha);" style="clear:both"><?php echo esc_html( $refresh_lable ); ?></a>
													</div>

							</p>
							<script type="text/javascript">
								var myCaptcha = null;
								var capchaChecked = false;
								var recap_val='';
								<?php $intval_guest_checkout = uniqid( 'interval_' ); ?>

							var <?php echo esc_html( $intval_guest_checkout ); ?> = setInterval(function() {

							if(document.readyState === 'complete') {

											clearInterval(<?php echo esc_html( $intval_guest_checkout ); ?>);




												if (typeof (grecaptcha.render) !== 'undefined' && myCaptcha === null) {

																	try{
																			myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
																					'sitekey': '<?php echo esc_html( $site_key ); ?>',
																					'callback' : verifyCallback_add_guestcheckout
																			});


																	}catch(error){}

													}


												jQuery(document).ajaxSend(function( event, jqxhr, settings ) {


																settings.data = settings.data + '&g-recaptcha-response='+window.recap_val;




													});



									}
								}, 100);


								var verifyCallback_add_guestcheckout = function(response) {

										if(response.length!==0){

												window.recap_val= response;
												if (typeof woo_guest_checkout_recaptcha_verified === "function") {

															woo_guest_checkout_recaptcha_verified(response);
													}


											}

									};




							</script>
							<?php

				} elseif ( 'yes' == $is_enabled_logincheckout && is_user_logged_in() ) {

					if ( 'yes' == $wbc_recapcha_no_conflict ) {

						global $wp_scripts;

							$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) ) {
													wp_dequeue_script( $handle );

													break;
								}
							}
						}
					}
						wp_enqueue_script( 'jquery' );
						wp_enqueue_script( 'wbc-woo-captcha' );

					?>
							<p class="login-checkout-captcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<?php
						if ( 'yes' != $wbc_recapcha_hide_label_checkout ) :
							?>
							<label for="reg_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label><?php endif; ?>
							<div id="g-recaptcha-checkout-wbc" name="g-recaptcha" class="g-recaptcha-" data-callback="verifyCallback_add_logincheckout"   data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
													<div id='refresh_captcha' style="width:100%;padding-top:5px"> <a href="javascript:grecaptcha.reset(myCaptcha);"><?php echo esc_html( $refresh_lable ); ?></a></div>

							</p>
							<script type="text/javascript">
										var myCaptcha = null;
										var recap_val='';
										<?php $intval_login_checkout = uniqid( 'interval_' ); ?>

									var <?php echo esc_html( $intval_login_checkout ); ?> = setInterval(function() {

									if(document.readyState === 'complete') {

														clearInterval(<?php echo esc_html( $intval_login_checkout ); ?>);


														if (typeof (grecaptcha.render) !== 'undefined' && myCaptcha === null) {

																			try{
																					myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
																							'sitekey': '<?php echo esc_html( $site_key ); ?>',
																							'callback' : verifyCallback_add_logincheckout
																					});
																			}catch(error){}

															}

															jQuery(document).ajaxSend(function( event, jqxhr, settings ) {


																	settings.data = settings.data + '&g-recaptcha-response='+window.recap_val;




														});

											}
										}, 100);


								var verifyCallback_add_logincheckout = function(response) {

											if(response.length!==0){

														window.recap_val= response;
														if (typeof woo_login_checkout_recaptcha_verified === "function") {

																	woo_login_checkout_recaptcha_verified(response);
															}
											}



									};


							</script>
							<?php

				}
			}
		} else {

					$wbc_recaptcha_v3_login_recpacha_for_req_btn = get_option( 'wbc_recaptcha_v3_login_recpacha_for_req_btn' );
			if ( '' == $wbc_recaptcha_v3_login_recpacha_for_req_btn ) {
				$wbc_recaptcha_v3_login_recpacha_for_req_btn = 'no';
			}
			if ( 'yes' == $wbc_recaptcha_v3_login_recpacha_for_req_btn ) {

				$is_enabled               = get_option( 'wbc_recapcha_enable_on_guestcheckout' );
				$is_enabled_logincheckout = get_option( 'wbc_recapcha_enable_on_logincheckout' );

				if ( ( 'yes' == $is_enabled && ! is_user_logged_in() ) || ( 'yes' == $is_enabled_logincheckout && is_user_logged_in() ) ) {

					if ( 'yes' == $wbc_recapcha_no_conflict ) {

						global $wp_scripts;

						$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

						foreach ( $wp_scripts->queue as $handle ) {

							foreach ( $urls as $url ) {
								if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) ) {
																		wp_dequeue_script( $handle );

																		break;
								}
							}
						}
					}
								wp_enqueue_script( 'jquery' );
								wp_enqueue_script( 'wbc-woo-captcha-v3' );

							$site_key                        = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
							$wbc_recapcha_checkout_action_v3 = get_option( 'wbc_recapcha_checkout_action_v3' );
					if ( '' == $wbc_recapcha_checkout_action_v3 ) {

						$wbc_recapcha_checkout_action_v3 = 'checkout';
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


																									jQuery( document ).ajaxComplete(function() {

																												if(jQuery(".woocommerce-error").is(":visible") || jQuery(".woocommerce_error").is(":visible")){

																																grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

																																		var recaptchaResponse = document.getElementById('wbc_checkout_token');
																																		recaptchaResponse.value = token;

																																}, function (reason) {

																																});
																												}

																										});



																								setInterval(function() {

																										grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_checkout_action_v3 ); ?>' }).then(function (token) {

																												var recaptchaResponse = document.getElementById('wbc_checkout_token');
																												recaptchaResponse.value = token;
																										});

																								}, 40 * 1000);



																								jQuery(document).ajaxSend(function( event, jqxhr, settings ) {

																											settings.data = settings.data + '&wbc_checkout_token='+jQuery('#wbc_checkout_token').val();


																								});

																				}

																		}, 100);





														</script>
							<?php
				}
			}
		}

	}

	/**
	 * Function added the captcha form into woocommerce comment form.
	 */
	public function woo_add_comment_form_captcha() {
		$woo_recaptcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $woo_recaptcha_version ) {
			$woo_recaptcha_version = 'v2';
		}
		if ( 'v2' == strtolower( $woo_recaptcha_version ) ) {

			$disable_submit_btn            = get_option( 'wbc_recapcha_disable_submitbtn_woo_comment' );
			$wbc_recapcha_hide_label_login = get_option( 'wbc_recapcha_hide_label_woo_comment' );
			$captcha_lable                 = get_option( 'wbc_recapcha_woo_comment_title' );
			$captcha_lable_                = $captcha_lable;

			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_woo_comment_theme' );
			$size                     = get_option( 'wbc_recapcha_woo_comment_size' );
			$is_enabled               = apply_filters( 'wbc_recapcha_enable_in_comment_form', get_option( 'wbc_recapcha_enable_on_woo_comment' ) );
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
	<div class="woo-comment-captcha woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php
				if ( 'yes' != $wbc_recapcha_hide_label_login ) :
					?>
	<label for="comment_captcha"><?php echo esc_html( ( '' == trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;<span class="required">*</span></label>
					<?php
			endif;
				?>
	<style>  #g-recaptcha-comment-wbc{margin-bottom: 10px;}</style>
	<div id="g-recaptcha-comment-wbc" name="g-recaptcha-comment-wbc" class="g-recaptcha" data-callback="verifyCallback_woo_comment" data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>


	</div>



	<script type="text/javascript">

				<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);

				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery('#commentform').find('#submit').attr("disabled", true);
					<?php if ( '' == $recapcha_error_msg_captcha_blank ) : ?>
	jQuery('#commentform').find('#submit').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery('#commentform').find('#submit').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>

	<?php endif; ?>
	}
	}, 100);


	var verifyCallback_woo_comment = function(response) {

	if(response.length!==0){
				<?php if ( 'yes' == trim( $disable_submit_btn ) ) : ?>
	jQuery('#commentform').find('#submit').removeAttr("title");
	jQuery('#commentform').find('#submit').attr("disabled", false);
	<?php endif; ?>


	if (typeof woo_comment_captcha_verified === "function") {

	woo_comment_captcha_verified(response);
	}

	}

	};



	</script>


				<?php

			}
		} else {

			$is_enabled                          = get_option( 'wbc_recapcha_enable_on_woo_comment' );
			$wbc_recapcha_no_conflict            = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_token_generation_v3_woo_comment = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_comment' );

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

				$site_key                       = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_comment_action_v3 = get_option( 'wbc_recapcha_woo_comment_method_action_v3' );
				if ( '' == trim( $wbc_recapcha_comment_action_v3 ) ) {

					$wbc_recapcha_comment_action_v3 = 'comment';
				}

				if ( '' == trim( $wbc_token_generation_v3_woo_comment ) ) {

					$wbc_token_generation_v3_woo_comment = 'no';
				}

				?>
	<input type="hidden" value="" name="wbc_recaptcha_comment_token" id="wbc_recaptcha_comment_token"/>
	<script type="text/javascript">

				<?php $intval_login = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_login ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_login ); ?>);

	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_comment_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_comment_token');
	recaptchaResponse.value = token;
	}, function (reason) {

	});
	});



				<?php if ( 'yes' == $wbc_token_generation_v3_woo_comment ) : ?>
	setInterval(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_comment_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_comment_token');
	recaptchaResponse.value = token;
	});

	}, 40 * 1000);
	<?php else : ?>
	jQuery('#commentform').on('submit', function (e) {
	var frm = this;
	e.preventDefault();
	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_comment_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_comment_token');
	recaptchaResponse.value = token;

	HTMLFormElement.prototype.submit.call(frm);
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
