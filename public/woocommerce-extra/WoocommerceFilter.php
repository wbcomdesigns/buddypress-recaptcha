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
class WoocommerceFilter {

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $user The position of the current token.
	 * @param array $password The position of the current token.
	 * Template Class.
	 */
	public function woo_wp_verify_login_captcha( $user, $password ) {

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $re_capcha_version ) ) {

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplogin' );

			$captcha_lable                          = get_option( 'wbc_recapcha_wplogin_title' );
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			$nonce_value = isset( $_POST['wp-login-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-login-nonce'] ) ) : '';
			$varify_none = wp_verify_nonce( $nonce_value, 'wp-login-nonce' );
			if ( 'yes' === $is_enabled && isset( $_POST['log'] ) ) {

				if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
					// Google reCAPTCHA API secret key.
					$response = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );

					// Verify the reCAPTCHA response.
					$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

					if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

						// Decode json data.
						$response_data = json_decode( $verify_response['body'] );

						// If reCAPTCHA response is valid.
						if ( ! $response_data->success ) {

							if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );
							} else {
								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
						} else {
							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) );
					} else {
						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		} else {
			$wbc_recapcha_wp_login_score_threshold_v3 = get_option( 'wbc_recapcha_wp_login_score_threshold_v3' );
			if ( empty( $wbc_recapcha_wp_login_score_threshold_v3 ) ) {

				$wbc_recapcha_wp_login_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_wp_login_action_v3 = get_option( 'wbc_recapcha_wp_login_action_v3' );
			if ( empty( $wbc_recapcha_wp_login_action_v3 ) ) {

				$wbc_recapcha_wp_login_action_v3 = 'wp_login';
			}

							$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
							$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
							$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
							$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
							$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplogin' );
							$nonce_value                            = isset( $_POST['wp-login-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-login-nonce'] ) ) : '';
							$varify_none                            = wp_verify_nonce( $nonce_value, 'wp-login-nonce' );
			if ( 'yes' === $is_enabled && isset( $_POST['log'] ) ) {

				if ( isset( $_POST['wbc_recaptcha_token'] ) && ! empty( $_POST['wbc_recaptcha_token'] ) ) {
					// Google reCAPTCHA API secret key.
					$response = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_token'] ) );

					// Verify the reCAPTCHA response.
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

					if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

								// Decode json data.
								$response_data = json_decode( $verify_response['body'] );
								// If reCAPTCHA response is valid.
						if ( ! $response_data->success ) {

							if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
							} else {
								return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						} else {
							if ( $response_data->score < $wbc_recapcha_wp_login_score_threshold_v3 || $response_data->action !== $wbc_recapcha_wp_login_action_v3 ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {
									return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
								} else {
									return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
								}
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not get response from reCAPTCHA server.', 'buddypress-recaptcha' ) );
						} else {
							return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ) );
					} else {
						return new WP_Error( 'Captcha Invalid', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		}

		return $user;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $username The position of the current token.
	 * @param array $email The position of the current token.
	 * @param array $validation_errors The position of the current token.
	 * Template Class.
	 */
	public function woo_verify_wp_register_captcha( $username, $email, $validation_errors ) {

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $re_capcha_version ) ) {

			$secret_key = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_wpregister' );

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$captcha_lable                          = 'captcha';
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			$nonce_value             = isset( $_POST['wp-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-register-nonce'] ) ) : '';
						$varify_none = wp_verify_nonce( $nonce_value, 'wp-register-nonce' );

			if ( 'yes' === $is_enabled && isset( $_POST['user_login'] ) && ! empty( $_POST['user_login'] ) ) {

				if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
						// Google reCAPTCHA API secret key.
						$response        = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );
						$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

					if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

										// Decode json data.
										$response_data = json_decode( $verify_response['body'] );

										// If reCAPTCHA response is valid.
						if ( ! $response_data->success ) {
							if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

														$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );
							} else {
																$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
						} else {
							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
						}
					}
				} else {

					if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

										$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) );
					} else {
										$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
					}
				}
			}
		} else {

			$wbc_recapcha_wp_register_score_threshold_v3 = get_option( 'wbc_recapcha_wp_register_score_threshold_v3' );
			if ( '' === $wbc_recapcha_wp_register_score_threshold_v3 ) {

				$wbc_recapcha_wp_register_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_wp_register_method_action_v3 = get_option( 'wbc_recapcha_wp_register_method_action_v3' );
			if ( '' === $wbc_recapcha_wp_register_method_action_v3 ) {

				$wbc_recapcha_wp_register_method_action_v3 = 'wp_registration';
			}

						$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
						$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
						$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
						$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
						$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wpregister' );
						$nonce_value                            = isset( $_POST['wp-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-register-nonce'] ) ) : '';
						$varify_none                            = wp_verify_nonce( $nonce_value, 'wp-register-nonce' );

			if ( 'yes' === $is_enabled && isset( $_POST['user_login'] ) ) {

				if ( isset( $_POST['wbc_recaptcha_wp_register_token'] ) && ! empty( $_POST['wbc_recaptcha_wp_register_token'] ) ) {
					// Google reCAPTCHA API secret key.
					$response = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_wp_register_token'] ) );

					// Verify the reCAPTCHA response.
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

					if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

							// Decode json data.
							$response_data = json_decode( $verify_response['body'] );

							// If reCAPTCHA response is valid.
						if ( ! $response_data->success ) {

							if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );

							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

							}
						} else {

							if ( $response_data->score < $wbc_recapcha_wp_register_score_threshold_v3 || $response_data->action !== $wbc_recapcha_wp_register_method_action_v3 ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );

								} else {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );

								}
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not get response from reCAPTCHA server.', 'buddypress-recaptcha' ) );

						} else {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );

						}
					}
				} else {

					if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ) );

					} else {

						$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );

					}
				}
			}
		}

		return $validation_errors;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $validation_errors The position of the current token
	 * Template Class.
	 */
	public function woo_verify_wp_lostpassword_captcha( $validation_errors ) {

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $re_capcha_version ) ) {

			$secret_key = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
			$is_enabled = get_option( 'wbc_recapcha_enable_on_wplostpassword' );

			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$nonce_value                            = isset( $_POST['wp-lostpassword-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-lostpassword-nonce'] ) ) : '';
			$captcha_lable                          = 'Captcha';
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			if ( 'yes' === $is_enabled && isset( $_POST['wp-lostpassword-nonce'] ) && ! empty( $_POST['wp-lostpassword-nonce'] ) ) {
				if ( wp_verify_nonce( $nonce_value, 'wp-lostpassword-nonce' ) ) {
					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
						// Google reCAPTCHA API secret key.
						$response = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );

						// Verify the reCAPTCHA response.
						$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

							// Decode json data.
							$response_data = json_decode( $verify_response['body'] );

							// If reCAPTCHA response is valid.
							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );
								} else {
									$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_no_response );
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) );
						} else {
							$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_blank );
						}
					}
				} else {

					$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Could not verify request.', 'buddypress-recaptcha' ) );
				}
			}
		} else {
			$wbc_recapcha_wp_lost_password_score_threshold_v3 = get_option( 'wbc_recapcha_wp_lost_password_score_threshold_v3' );
			if ( empty( $wbc_recapcha_wp_lost_password_score_threshold_v3 ) ) {

				$wbc_recapcha_wp_lost_password_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_wp_lost_password_method_action_v3 = get_option( 'wbc_recapcha_wp_lost_password_method_action_v3' );
			if ( empty( $wbc_recapcha_wp_lost_password_method_action_v3 ) ) {

				$wbc_recapcha_wp_lost_password_method_action_v3 = 'wp_forgot_password';
			}

							$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
							$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
							$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
							$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
							$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wplostpassword' );
			if ( empty( $_POST['wp-lostpassword-nonce'] ) ) {
				$nonce_value = isset( $_POST['woocommerce-lost-password-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-lostpassword-nonce'] ) ) : '';
				$varify_none = wp_verify_nonce( $nonce_value, 'woocommerce-lost-password-nonce' );
			} else {
				$nonce_value = isset( $_POST['wp-lostpassword-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-lostpassword-nonce'] ) ) : '';
				$varify_none = wp_verify_nonce( $nonce_value, 'wp-lostpassword-nonce' );
			}

			if ( 'yes' == $is_enabled ) {
				if ( isset( $_POST['wbc_recaptcha_token'] ) && ! empty( $_POST['wbc_recaptcha_token'] ) ) {
					// Google reCAPTCHA API secret key.
					$response = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_token'] ) );

					// Verify the reCAPTCHA response.
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

					if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {
						// Decode json data.
						$response_data = json_decode( $verify_response['body'] );
						// If reCAPTCHA response is valid.
						if ( ! $response_data->success ) {
							if ( empty( trim( $recapcha_error_msg_captcha_invalid ) ) ) {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
							} else {
								$validation_errors->add( 'g-recaptcha_error', '<strong>' . __( 'ERROR:', 'buddypress-recaptcha' ) . '</strong> ' . $recapcha_error_msg_captcha_invalid );
							}
						} else {
							$validation_errors->remove( 'g-recaptcha_error' );
						}
					}
				}
			}
		}

		return $validation_errors;

	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function woo_remove_no_conflict() {

		return false;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $comment_data The position of the current token
	 * Template Class.
	 */
	public function woo_check_review_captcha( $comment_data ) {

			$is_enabled = get_option( 'wbc_recapcha_enable_on_woo_review' );
		if ( 'yes' === $is_enabled ) {

				$re_capcha_version = get_option( 'wbc_recapcha_version' );
			if ( '' === $re_capcha_version ) {
				$re_capcha_version = 'v2';
			}

			if ( 'v2' === strtolower( $re_capcha_version ) ) {

						$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
						$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
						$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
						$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );

														$captcha_lable = get_option( 'wbc_recapcha_woo_review_title' );
				if ( '' === trim( $captcha_lable ) ) {

					$captcha_lable = 'captcha';
				}

							$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
							$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
							$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

							$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
							$varify_none = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' === $comment_data['comment_type'] && wc_reviews_enabled() ) {

					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {

												// Google reCAPTCHA API secret key.
												$response = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );

												// Verify the reCAPTCHA response.
												$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

									// Decode json data.
									$response_data = json_decode( $verify_response['body'] );

									// If reCAPTCHA response is valid.
							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									wp_die( esc_html__( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );
									exit;

								} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

								wp_die( esc_html__( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
								exit;

							} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
														exit;

							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) );
								exit;

						} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
								exit;

						}
					}
				}
			} else {

					$wbc_recapcha_woo_review_score_threshold_v3 = get_option( 'wbc_recapcha_woo_review_score_threshold_v3' );
				if ( '' === $wbc_recapcha_woo_review_score_threshold_v3 ) {

					$wbc_recapcha_woo_review_score_threshold_v3 = '0.5';
				}

					$wbc_recapcha_woo_review_method_action_v3 = get_option( 'wbc_recapcha_woo_review_method_action_v3' );
				if ( '' === $wbc_recapcha_woo_review_method_action_v3 ) {

					$wbc_recapcha_woo_review_method_action_v3 = 'review';
				}

					$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
					$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
					$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
					$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );

					$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
					$varify_none = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' === $comment_data['comment_type'] && wc_reviews_enabled() ) {

					if ( isset( $_POST['wbc_recaptcha_review_token'] ) && ! empty( $_POST['wbc_recaptcha_review_token'] ) ) {
						// Google reCAPTCHA API secret key.
						$response = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_review_token'] ) );

						// Verify the reCAPTCHA response.
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

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

										// Decode json data.
								$response_data = json_decode( $verify_response['body'] );
								// If reCAPTCHA response is valid.

							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
										exit;

								} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
															exit;
								}
							} else {

								if ( $response_data->score < $wbc_recapcha_woo_review_score_threshold_v3 || $response_data->action !== $wbc_recapcha_woo_review_method_action_v3 ) {

									if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
										exit;

									} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
									}
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

								wp_die( esc_html__( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
								exit;

							} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
								exit;
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ) );
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

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $comment_data The position of the current token
	 * Template Class.
	 */
	public function woo_check_comment_captcha( $comment_data ) {

		$is_enabled = get_option( 'wbc_recapcha_enable_on_woo_comment' );
		if ( 'yes' === $is_enabled ) {

				$re_capcha_version = get_option( 'wbc_recapcha_version' );
			if ( '' === $re_capcha_version ) {
				$re_capcha_version = 'v2';
			}

			if ( 'v2' === strtolower( $re_capcha_version ) ) {

				$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
				$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
				$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
				$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
				$captcha_lable                          = 'Captcha';
				$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
				$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
				$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

				$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
				$varify_none = wp_verify_nonce( $nonce_value, 'wp-review-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' !== get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' !== $comment_data['comment_type'] ) {

					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {

												// Google reCAPTCHA API secret key.
												$response = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );

												// Verify the reCAPTCHA response.
												$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

									// Decode json data.
									$response_data = json_decode( $verify_response['body'] );

									// If reCAPTCHA response is valid.
							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									wp_die( esc_html__( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );
									exit;

								} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

														wp_die( esc_html__( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
														exit;

							} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
															exit;

							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) );
								exit;

						} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_blank ) );
								exit;

						}
					}
				}
			} else {

					$wbc_recapcha_woo_comment_score_threshold_v3 = get_option( 'wbc_recapcha_woo_comment_score_threshold_v3' );
				if ( '' === $wbc_recapcha_woo_comment_score_threshold_v3 ) {

					$wbc_recapcha_woo_comment_score_threshold_v3 = '0.5';
				}

					$wbc_recapcha_woo_comment_method_action_v3 = get_option( 'wbc_recapcha_woo_comment_method_action_v3' );
				if ( '' === $wbc_recapcha_woo_comment_method_action_v3 ) {

					$wbc_recapcha_woo_comment_method_action_v3 = 'comment';
				}

								$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
								$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
								$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
								$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );

								$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
								$varify_none = wp_verify_nonce( $nonce_value, 'wp-comment-nonce' );

				if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && 'product' !== get_post_type( absint( $_POST['comment_post_ID'] ) ) && 'review' !== $comment_data['comment_type'] ) {

					if ( isset( $_POST['wbc_recaptcha_comment_token'] ) && ! empty( $_POST['wbc_recaptcha_comment_token'] ) ) {
						// Google reCAPTCHA API secret key.
						$response = sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_comment_token'] ) );

						// Verify the reCAPTCHA response.
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

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

								// Decode json data.
								$response_data = json_decode( $verify_response['body'] );
								// If reCAPTCHA response is valid.

							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
										exit;

								} else {

															wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
															exit;
								}
							} else {

								if ( $response_data->score < $wbc_recapcha_woo_comment_score_threshold_v3 || $response_data->action !== $wbc_recapcha_woo_comment_method_action_v3 ) {

									if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

										wp_die( esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );
										exit;

									} else {

										wp_die( esc_html( $recapcha_error_msg_captcha_invalid ) );
										exit;
									}
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

								wp_die( esc_html__( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
								exit;

							} else {

								wp_die( esc_html( $recapcha_error_msg_captcha_no_response ) );
								exit;
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

								wp_die( esc_html__( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ) );
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
