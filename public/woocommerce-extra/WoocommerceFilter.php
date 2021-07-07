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
class WoocommerceFilter {
	public function woo_wp_verify_login_captcha( $user, $password ) {

		$reCapcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $reCapcha_version ) {
			$reCapcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $reCapcha_version ) ) {

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplogin' );

			$captcha_lable                          = get_option( 'wbc_recapcha_wplogin_title' );
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			$nonce_value  = isset( $_POST['wp-login-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-login-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
			  $varifyNone = wp_verify_nonce( $nonce_value, 'wp-login-nonce' );
			if ( 'yes' == $is_enabled && isset( $_POST['log'] ) ) {

				if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
					// Google reCAPTCHA API secret key
					$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

					// Verify the reCAPTCHA response
					$verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

					if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

						// Decode json data
						$responseData = json_decode( $verifyResponse['body'] );

						// If reCAPTCHA response is valid
						if ( ! $responseData->success ) {

							if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Invalid recaptcha.', 'recaptcha-for-woocommerce' ) );
							} else {
								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
						} else {
							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Recaptcha is a required field.', 'recaptcha-for-woocommerce' ) );
					} else {
						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		} else {

			$wbc_recapcha_wp_login_score_threshold_v3 = get_option( 'wbc_recapcha_wp_login_score_threshold_v3' );
			if ( '' == $wbc_recapcha_wp_login_score_threshold_v3 ) {

				$wbc_recapcha_wp_login_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_wp_login_action_v3 = get_option( 'wbc_recapcha_wp_login_action_v3' );
			if ( '' == $wbc_recapcha_wp_login_action_v3 ) {

				$wbc_recapcha_wp_login_action_v3 = 'wp_login';
			}

							$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
							$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
							$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
							$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
							$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplogin' );
							$nonce_value                            = isset( $_POST['wp-login-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-login-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
							$varifyNone                             = wp_verify_nonce( $nonce_value, 'wp-login-nonce' );
			if ( 'yes' == $is_enabled && isset( $_POST['log'] ) ) {

				if ( isset( $_POST['wbc_recaptcha_token'] ) && ! empty( $_POST['wbc_recaptcha_token'] ) ) {
					// Google reCAPTCHA API secret key
					$response = sanitize_text_field( $_POST['wbc_recaptcha_token'] );

					// Verify the reCAPTCHA response
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

								  // Decode json data
								  $responseData = json_decode( $verifyResponse['body'] );
								  // If reCAPTCHA response is valid
						if ( ! $responseData->success ) {

							if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

								  return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
							} else {
								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						} else {

							if ( $responseData->score < $wbc_recapcha_wp_login_score_threshold_v3 || $responseData->action != $wbc_recapcha_wp_login_action_v3 ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
								} else {
									return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
								}
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from reCAPTCHA server.', 'recaptcha-for-woocommerce' ) );
						} else {
							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce' ) );
					} else {
						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		}

		return $user;
	}

	public function woo_verify_wp_register_captcha( $username, $email, $validation_errors ) {

		$reCapcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $reCapcha_version ) {
			$reCapcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $reCapcha_version ) ) {

			$secret_key = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_wpregister' );

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );

			$captcha_lable = trim( get_option( 'wbc_recapcha_wpregister_title' ) );
			if ( '' == trim( $captcha_lable ) ) {

				$captcha_lable = 'captcha';
			}

			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			$nonce_value            = isset( $_POST['wp-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-register-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
						$varifyNone = wp_verify_nonce( $nonce_value, 'wp-register-nonce' );

			if ( 'yes' == $is_enabled && isset( $_POST['user_login'] ) && ! empty( $_POST['user_login'] ) ) {

				if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
						 // Google reCAPTCHA API secret key
						 $response = sanitize_text_field( $_POST['g-recaptcha-response'] );

						 // Verify the reCAPTCHA response
						 $verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

					if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

										// Decode json data
										$responseData = json_decode( $verifyResponse['body'] );

										// If reCAPTCHA response is valid
						if ( ! $responseData->success ) {
							if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

														$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Invalid recaptcha.', 'recaptcha-for-woocommerce' ) );
							} else {
																$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
						} else {
							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

										 $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Recaptcha is a required field.', 'recaptcha-for-woocommerce' ) );
					} else {
											  $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		} else {

			$wbc_recapcha_wp_register_score_threshold_v3 = get_option( 'wbc_recapcha_wp_register_score_threshold_v3' );
			if ( '' == $wbc_recapcha_wp_register_score_threshold_v3 ) {

				$wbc_recapcha_wp_register_score_threshold_v3 = '0.5';
			}
			   $wbc_recapcha_wp_register_method_action_v3 = get_option( 'wbc_recapcha_wp_register_method_action_v3' );
			if ( '' == $wbc_recapcha_wp_register_method_action_v3 ) {

				$wbc_recapcha_wp_register_method_action_v3 = 'wp_registration';
			}

						$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
						$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
						$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
						$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
						$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wpregister' );
						$nonce_value                            = isset( $_POST['wp-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-register-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
						$varifyNone                             = wp_verify_nonce( $nonce_value, 'wp-register-nonce' );

			if ( 'yes' == $is_enabled && isset( $_POST['user_login'] ) ) {

				if ( isset( $_POST['wbc_recaptcha_wp_register_token'] ) && ! empty( $_POST['wbc_recaptcha_wp_register_token'] ) ) {
					// Google reCAPTCHA API secret key
					$response = sanitize_text_field( $_POST['wbc_recaptcha_wp_register_token'] );

					// Verify the reCAPTCHA response
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

								 // Decode json data
								 $responseData = json_decode( $verifyResponse['body'] );

								 // If reCAPTCHA response is valid
						if ( ! $responseData->success ) {

							if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

								  $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );

							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

							}
						} else {

							if ( $responseData->score < $wbc_recapcha_wp_register_score_threshold_v3 || $responseData->action != $wbc_recapcha_wp_register_method_action_v3 ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );

								} else {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

								}
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from reCAPTCHA server.', 'recaptcha-for-woocommerce' ) );

						} else {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );

						}
					}
				} else {

					if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce' ) );

					} else {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );

					}
				}
			}
		}

		return $validation_errors;
	}

	public function woo_verify_wp_lostpassword_captcha( $validation_errors ) {

		$reCapcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' == $reCapcha_version ) {
			$reCapcha_version = 'v2';
		}

		if ( 'v2' == strtolower( $reCapcha_version ) ) {

			$secret_key = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_wplostpassword' );

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$nonce_value                            = isset( $_POST['wp-lostpassword-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-lostpassword-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification

			$captcha_lable = get_option( 'wbc_recapcha_wplostpassword_title' );
			if ( '' == trim( $captcha_lable ) ) {

				$captcha_lable = 'captcha';
			}
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			if ( 'yes' == $is_enabled && isset( $_POST['wp-lostpassword-nonce'] ) && ! empty( $_POST['wp-lostpassword-nonce'] ) ) {

				if ( wp_verify_nonce( $nonce_value, 'wp-lostpassword-nonce' ) ) {
					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
						// Google reCAPTCHA API secret key
						$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

						// Verify the reCAPTCHA response
						$verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

							// Decode json data
							$responseData = json_decode( $verifyResponse['body'] );

							// If reCAPTCHA response is valid
							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Invalid recaptcha.', 'recaptcha-for-woocommerce' ) );
								} else {
									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

																				   $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

																				   $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Recaptcha is a required field.', 'recaptcha-for-woocommerce' ) );
						} else {
							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
						}
					}
				} else {

					$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not verify request.', 'recaptcha-for-woocommerce' ) );
				}
			}
		} else {

			$wbc_recapcha_wp_lost_password_score_threshold_v3 = get_option( 'wbc_recapcha_wp_lost_password_score_threshold_v3' );
			if ( '' == $wbc_recapcha_wp_lost_password_score_threshold_v3 ) {

				$wbc_recapcha_wp_lost_password_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_wp_lost_password_method_action_v3 = get_option( 'wbc_recapcha_wp_lost_password_method_action_v3' );
			if ( '' == $wbc_recapcha_wp_lost_password_method_action_v3 ) {

				$wbc_recapcha_wp_lost_password_method_action_v3 = 'wp_forgot_password';
			}

							$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
							$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
							$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
							$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
							$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplostpassword' );
							$nonce_value                            = isset( $_POST['wp-lostpassword-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-lostpassword-nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
							$varifyNone                             = wp_verify_nonce( $nonce_value, 'wp-lostpassword-nonce' );
			if ( 'yes' == $is_enabled && isset( $_POST['wp-lostpassword-nonce'] ) && wp_verify_nonce( $nonce_value, 'wp-lostpassword-nonce' ) ) {

				if ( isset( $_POST['wbc_recaptcha_token'] ) && ! empty( $_POST['wbc_recaptcha_token'] ) ) {
					// Google reCAPTCHA API secret key
					$response = sanitize_text_field( $_POST['wbc_recaptcha_token'] );

					// Verify the reCAPTCHA response
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

								  // Decode json data
								  $responseData = json_decode( $verifyResponse['body'] );

								  // If reCAPTCHA response is valid
						if ( ! $responseData->success ) {

							if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

								  $validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );

							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

							}
						} else {

							if ( $responseData->score < $wbc_recapcha_wp_lost_password_score_threshold_v3 || $responseData->action != $wbc_recapcha_wp_lost_password_method_action_v3 ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );

								} else {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

								}
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Could not get response from reCAPTCHA server.', 'recaptcha-for-woocommerce' ) );

						} else {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );

						}
					}
				} else {

					if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . __( 'Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce' ) );

					} else {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'recaptcha-for-woocommerce' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );

					}
				}
			}
		}

		return $validation_errors;

	}

	public function woo_remove_no_conflict() {

		return false;
	}

	public function woo_check_review_captcha( $comment_data ) {

			$is_enabled = get_option( 'wbc_recapcha_enable_on_woo_review' );
		if ( 'yes' == $is_enabled ) {

				$reCapcha_version = get_option( 'wbc_recapcha_version' );
			if ( '' == $reCapcha_version ) {
				$reCapcha_version = 'v2';
			}

			if ( 'v2' == strtolower( $reCapcha_version ) ) {

														$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
														$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
														$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
														$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );

														$captcha_lable = get_option( 'wbc_recapcha_woo_review_title' );
				if ( '' == trim( $captcha_lable ) ) {

					$captcha_lable = 'captcha';
				}

														$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
														$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
														$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

							$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
							$varifyNone  = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' === $comment_data['comment_type'] && wc_reviews_enabled() ) { // WPCS: input var ok, CSRF ok.

					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {

												// Google reCAPTCHA API secret key
												$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

												// Verify the reCAPTCHA response
												$verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

									// Decode json data
									$responseData = json_decode( $verifyResponse['body'] );

									// If reCAPTCHA response is valid
							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									wp_die( esc_html__( 'Invalid recaptcha.', 'recaptcha-for-woocommerce' ) );
									exit;

								} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

														   wp_die( esc_html__( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
														   exit;

							} else {

															 wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
															 exit;

							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'Recaptcha is a required field.', 'recaptcha-for-woocommerce' ) );
								exit;

						} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
								exit;

						}
					}
				}
			} else {

					  $wbc_recapcha_woo_review_score_threshold_v3 = get_option( 'wbc_recapcha_woo_review_score_threshold_v3' );
				if ( '' == $wbc_recapcha_woo_review_score_threshold_v3 ) {

					 $wbc_recapcha_woo_review_score_threshold_v3 = '0.5';
				}

					$wbc_recapcha_woo_review_method_action_v3 = get_option( 'wbc_recapcha_woo_review_method_action_v3' );
				if ( '' == $wbc_recapcha_woo_review_method_action_v3 ) {

					 $wbc_recapcha_woo_review_method_action_v3 = 'review';
				}

					$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
					$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
					$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
					$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );

					$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
					$varifyNone  = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' === $comment_data['comment_type'] && wc_reviews_enabled() ) { // WPCS: input var ok, CSRF ok.

					if ( isset( $_POST['wbc_recaptcha_review_token'] ) && ! empty( $_POST['wbc_recaptcha_review_token'] ) ) {
						// Google reCAPTCHA API secret key
						$response = sanitize_text_field( $_POST['wbc_recaptcha_review_token'] );

						// Verify the reCAPTCHA response
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

										// Decode json data
								$responseData = json_decode( $verifyResponse['body'] );
								// If reCAPTCHA response is valid

							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										   wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
										   exit;

								} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
															exit;
								}
							} else {

								if ( $responseData->score < $wbc_recapcha_woo_review_score_threshold_v3 || $responseData->action != $wbc_recapcha_woo_review_method_action_v3 ) {

									if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
										exit;

									} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
									}
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

								wp_die( esc_html__( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
								exit;

							} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
								exit;
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

								   wp_die( esc_html__( 'Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce' ) );
								   exit;

						} else {

							wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
							exit;

						}
					}
				}
			}
		}

			return $comment_data;
	}

	public function woo_check_comment_captcha( $comment_data ) {

		$is_enabled = get_option( 'wbc_recapcha_enable_on_woo_comment' );
		if ( 'yes' == $is_enabled ) {

				$reCapcha_version = get_option( 'wbc_recapcha_version' );
			if ( '' == $reCapcha_version ) {
				$reCapcha_version = 'v2';
			}

			if ( 'v2' == strtolower( $reCapcha_version ) ) {

								$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
								$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
								$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
								$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );

								$captcha_lable = get_option( 'wbc_recapcha_woo_comment_title' );
				if ( '' == trim( $captcha_lable ) ) {

					$captcha_lable = 'captcha';
				}

								$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
								$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
								$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

								$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
								$varifyNone  = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' !== get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' !== $comment_data['comment_type'] ) { // WPCS: input var ok, CSRF ok.

					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {

												// Google reCAPTCHA API secret key
												$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

												// Verify the reCAPTCHA response
												$verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verifyResponse ) && ! is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {

									// Decode json data
									$responseData = json_decode( $verifyResponse['body'] );

									// If reCAPTCHA response is valid
							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

									wp_die( esc_html__( 'Invalid recaptcha.', 'recaptcha-for-woocommerce' ) );
									exit;

								} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

														   wp_die( esc_html__( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
														   exit;

							} else {

															 wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
															 exit;

							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'Recaptcha is a required field.', 'recaptcha-for-woocommerce' ) );
								exit;

						} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
								exit;

						}
					}
				}
			} else {

					  $wbc_recapcha_woo_comment_score_threshold_v3 = get_option( 'wbc_recapcha_woo_comment_score_threshold_v3' );
				if ( '' == $wbc_recapcha_woo_comment_score_threshold_v3 ) {

					 $wbc_recapcha_woo_comment_score_threshold_v3 = '0.5';
				}

					$wbc_recapcha_woo_comment_method_action_v3 = get_option( 'wbc_recapcha_woo_comment_method_action_v3' );
				if ( '' == $wbc_recapcha_woo_comment_method_action_v3 ) {

					 $wbc_recapcha_woo_comment_method_action_v3 = 'comment';
				}

								$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
								$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
								$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
								$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );

								$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
								$varifyNone  = wp_verify_nonce( $nonce_value, 'wp-comment-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' !== get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' !== $comment_data['comment_type'] ) { // WPCS: input var ok, CSRF ok.

					if ( isset( $_POST['wbc_recaptcha_comment_token'] ) && ! empty( $_POST['wbc_recaptcha_comment_token'] ) ) {
						// Google reCAPTCHA API secret key
						$response = sanitize_text_field( $_POST['wbc_recaptcha_comment_token'] );

						// Verify the reCAPTCHA response
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

										// Decode json data
								$responseData = json_decode( $verifyResponse['body'] );
								// If reCAPTCHA response is valid

							if ( ! $responseData->success ) {

								if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										   wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
										   exit;

								} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
															exit;
								}
							} else {

								if ( $responseData->score < $wbc_recapcha_woo_comment_score_threshold_v3 || $responseData->action != $wbc_recapcha_woo_comment_method_action_v3 ) {

									if ( '' == trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'recaptcha-for-woocommerce' ) );
										exit;

									} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
									}
								}
							}
						} else {

							if ( '' == trim( $recapcha_error_msg_captcha_no_response ) ) {

								wp_die( esc_html__( 'Could not get response from recaptcha server.', 'recaptcha-for-woocommerce' ) );
								exit;

							} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
								exit;
							}
						}
					} else {

						if ( '' == trim( $recapcha_error_msg_captcha_blank ) ) {

								   wp_die( esc_html__( 'Google reCAPTCHA token is missing.', 'recaptcha-for-woocommerce' ) );
								   exit;

						} else {

							wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
							exit;

						}
					}
				}
			}
		}

		return $comment_data;
	}
}
