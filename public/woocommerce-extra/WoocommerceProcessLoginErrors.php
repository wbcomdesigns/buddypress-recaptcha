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
class WoocommerceProcessLoginErrors {
	public function woocomm_validate_login_captcha( $validation_errors, $username, $password) {

		
		$reCapcha_version = get_option('i13_recapcha_version'); 
		if (''==$reCapcha_version) {
			$reCapcha_version='v2';
		}

		if ('v2'== strtolower($reCapcha_version)) {

			$recapcha_error_msg_captcha_blank = get_option('wc_settings_tab_recapcha_error_msg_captcha_blank');
			$recapcha_error_msg_captcha_no_response = get_option('wc_settings_tab_recapcha_error_msg_captcha_no_response');
			$recapcha_error_msg_captcha_invalid = get_option('wc_settings_tab_recapcha_error_msg_captcha_invalid');

			$captcha_lable = get_option('i13_recapcha_login_title');
			if (''==trim($captcha_lable)) {
					
				$captcha_lable='recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace('[recaptcha]', ucfirst($captcha_lable), $recapcha_error_msg_captcha_blank);
			$recapcha_error_msg_captcha_no_response = str_replace('[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response);
			$recapcha_error_msg_captcha_invalid = str_replace('[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid);

			$secret_key = get_option('wc_settings_tab_recapcha_secret_key');
			$is_enabled = get_option('i13_recapcha_enable_on_login');

			$nonce_value = '';
			if (isset($_REQUEST['woocommerce-login-nonce']) || isset($_REQUEST['_wpnonce'])) {
					
					
				if (isset($_REQUEST['woocommerce-login-nonce']) && !empty($_REQUEST['woocommerce-login-nonce'])) {

					$nonce_value=sanitize_text_field($_REQUEST['woocommerce-login-nonce']);
				} else if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {

					$nonce_value=sanitize_text_field($_REQUEST['_wpnonce']);
				}
			
			}
			if ('yes' == $is_enabled && isset($_POST['username'])) {

				if (wp_verify_nonce($nonce_value, 'woocommerce-login')) {
					if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
						// Google reCAPTCHA API secret key 
						$response = sanitize_text_field($_POST['g-recaptcha-response']);

						// Verify the reCAPTCHA response 
						$verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array('timeout'=> 30));

						if (is_array($verifyResponse) && !is_wp_error($verifyResponse) && isset($verifyResponse['body'])) {

							// Decode json data 
							$responseData = json_decode($verifyResponse['body']);

							// If reCAPTCHA response is valid 
							if (!$responseData->success) {
													
								if (''==trim($recapcha_error_msg_captcha_invalid)) {

									$validation_errors->add('g-recaptcha_error', __('Invalid recaptcha.', 'recaptcha-for-woocommerce'));
								} else {
									$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_invalid);
								}
	
							}
						} else {

							if (''==trim($recapcha_error_msg_captcha_no_response)) {
													
								$validation_errors->add('g-recaptcha_error', __('Could not get response from recaptcha server.', 'recaptcha-for-woocommerce'));
							} else {
								$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_no_response);
							}
						}
					} else {

						if (''==trim($recapcha_error_msg_captcha_blank)) {

							 $validation_errors->add('g-recaptcha_error', __('Recaptcha is a required field.', 'recaptcha-for-woocommerce'));
						} else {
							$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_blank);
						}  
					}
				} else {

					$validation_errors->add('g-recaptcha_error', __('Could not verify request.', 'recaptcha-for-woocommerce'));
				}
			}
				
		} else {

			$i13_recapcha_login_score_threshold_v3 = get_option('i13_recapcha_login_score_threshold_v3');
			if (''==$i13_recapcha_login_score_threshold_v3) {

				$i13_recapcha_login_score_threshold_v3='0.5';
			}
			$i13_recapcha_login_action_v3 = get_option('i13_recapcha_login_action_v3');
			if (''==$i13_recapcha_login_action_v3) {

				$i13_recapcha_login_action_v3='login';
			}

			$recapcha_error_msg_captcha_blank = get_option('i13_recapcha_error_msg_captcha_blank_v3');
			$recapcha_error_msg_captcha_no_response = get_option('i13_recapcha_error_msg_captcha_no_response_v3');
			$recapcha_error_msg_captcha_invalid = get_option('i13_recapcha_error_msg_v3_invalid_captcha');
			$secret_key = get_option('wc_settings_tab_recapcha_secret_key_v3');
			$is_enabled = get_option('i13_recapcha_enable_on_login');
			$nonce_value = '';
			if (isset($_REQUEST['woocommerce-login-nonce']) || isset($_REQUEST['_wpnonce'])) {


				if (isset($_REQUEST['woocommerce-login-nonce']) && !empty($_REQUEST['woocommerce-login-nonce'])) {

					$nonce_value=sanitize_text_field($_REQUEST['woocommerce-login-nonce']);
				} else if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {

					$nonce_value=sanitize_text_field($_REQUEST['_wpnonce']);
				}

			}
			if ('yes' == $is_enabled && isset($_POST['username']) && wp_verify_nonce($nonce_value, 'woocommerce-login')) {

						

				if (isset($_POST['i13_recaptcha_login_token']) && !empty($_POST['i13_recaptcha_login_token'])) {
					// Google reCAPTCHA API secret key 
					$response = sanitize_text_field($_POST['i13_recaptcha_login_token']);

					// Verify the reCAPTCHA response 
					$verifyResponse = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
						'method'      => 'POST',
						'timeout'     => 45,
						'body'        => array(
						'secret' => $secret_key,
						'response' => $response
						)

						)
					);


					if (is_array($verifyResponse) && !is_wp_error($verifyResponse) && isset($verifyResponse['body'])) {

								 // Decode json data 
								 $responseData = json_decode($verifyResponse['body']);

								 // If reCAPTCHA response is valid 
						if (!$responseData->success) {


							if (''==trim($recapcha_error_msg_captcha_invalid)) {

								 $validation_errors->add('g-recaptcha_error', __('Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce'));

							} else {
								$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_invalid);

							}
						} else {

													

							if ($responseData->score < $i13_recapcha_login_score_threshold_v3 || $responseData->action!=$i13_recapcha_login_action_v3) {

								if (''==trim($recapcha_error_msg_captcha_invalid)) {

									$validation_errors->add('g-recaptcha_error', __('Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce'));  

								} else {

									$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_invalid);   

								}

							}

						}
					} else {

						if (''==trim($recapcha_error_msg_captcha_no_response)) {

							$validation_errors->add('g-recaptcha_error', __('Could not get response from reCAPTCHA server.', 'recaptcha-for-woocommerce'));  

						} else {

							$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_no_response);  

						}

					}
				} else {

					if (''==trim($recapcha_error_msg_captcha_blank)) {

						$validation_errors->add('g-recaptcha_error', __('Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce'));  

					} else {

						$validation_errors->add('g-recaptcha_error', $recapcha_error_msg_captcha_blank);  

					}


				}

			}

		}
				
		return $validation_errors;
	}
}
