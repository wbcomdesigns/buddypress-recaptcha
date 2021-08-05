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
class WoocommerceRegisterPost {

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $username The position of the current token.
	 * @param array $email The position of the current token.
	 * @param array $validation_errors The position of the current token.
	 * Template Class.
	 */
	public function woocomm_validate_signup_captcha( $username, $email, $validation_errors ) {

		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}

		if ( 'v2' === strtolower( $re_capcha_version ) ) {

			$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key' );
			$is_enabled                             = get_option( 'wbc_recapcha_enable_on_signup' );
			$recapcha_error_msg_captcha_blank       = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_no_response' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_invalid' );
			$captcha_lable                          = get_option( 'wbc_recapcha_signup_title' );
			if ( '' === trim( $captcha_lable ) ) {

				$captcha_lable = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank       = str_replace( '[recaptcha]', ucfirst( $captcha_lable ), $recapcha_error_msg_captcha_blank );
			$recapcha_error_msg_captcha_no_response = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_no_response );
			$recapcha_error_msg_captcha_invalid     = str_replace( '[recaptcha]', $captcha_lable, $recapcha_error_msg_captcha_invalid );

			$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
			$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['woocommerce-register-nonce'] ) ) : $nonce_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification

			if ( 'yes' === $is_enabled && ( ( isset( $_POST['woocommerce-register-nonce'] ) && ! empty( $_POST['woocommerce-register-nonce'] ) ) || ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) ) ) {

				if ( wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {

					if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
						// Google reCAPTCHA API secret key.
						$response = sanitize_text_field( $_POST['g-recaptcha-response'] );

						// Verify the reCAPTCHA response.
						$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response, array( 'timeout' => 30 ) );

						if ( is_array( $verify_response ) && ! is_wp_error( $verify_response ) && isset( $verify_response['body'] ) ) {

							// Decode json data.
							$response_data = json_decode( $verify_response['body'] );

							// If reCAPTCHA response is valid.
							if ( ! $response_data->success ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', __( 'Invalid recaptcha.', 'buddypress-recaptcha' ) );

								} else {

									$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_invalid );
								}
							}
						} else {

							if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

								$validation_errors->add( 'g-recaptcha_error', __( 'Could not get response from recaptcha server.', 'buddypress-recaptcha' ) );
							} else {
								$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_no_response );
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

							$validation_errors->add( 'g-recaptcha_error', __( 'Recaptcha is a required field.', 'buddypress-recaptcha' ) );

						} else {
							$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_blank );
						}
					}
				} else {

					$validation_errors->add( 'g-recaptcha_error', __( 'Could not verify request.', 'buddypress-recaptcha' ) );
				}
			}
		} else {

			$wbc_recapcha_signup_score_threshold_v3 = get_option( 'wbc_recapcha_signup_score_threshold_v3' );
			if ( '' === $wbc_recapcha_signup_score_threshold_v3 ) {

				$wbc_recapcha_signup_score_threshold_v3 = '0.5';
			}
			$wbc_recapcha_signup_action_v3 = get_option( 'wbc_recapcha_signup_action_v3' );
			if ( '' === $wbc_recapcha_signup_action_v3 ) {

				$wbc_recapcha_signup_action_v3 = 'signup';
			}

			$recapcha_error_msg_captcha_blank       = get_option( 'wbc_recapcha_error_msg_captcha_blank_v3' );
			$recapcha_error_msg_captcha_no_response = get_option( 'wbc_recapcha_error_msg_captcha_no_response_v3' );
			$recapcha_error_msg_captcha_invalid     = get_option( 'wbc_recapcha_error_msg_v3_invalid_captcha' );
			$secret_key                             = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
			$is_enabled                             = get_option( 'wbc_recapcha_enable_on_wpregister' );
			$nonce_value                            = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification
			$nonce_value                            = isset( $_POST['woocommerce-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['woocommerce-register-nonce'] ) ) : $nonce_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.NoNonceVerification

			if ( 'yes' === $is_enabled && ( isset( $_POST['woocommerce-register-nonce'] ) && ! empty( $_POST['woocommerce-register-nonce'] ) ) || ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) ) {

				if ( isset( $_POST['wbc_recaptcha_register_token'] ) && ! empty( $_POST['wbc_recaptcha_register_token'] ) ) {
					// Google reCAPTCHA API secret key.
					$response = sanitize_text_field( $_POST['wbc_recaptcha_register_token'] );

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

								$validation_errors->add( 'g-recaptcha_error', __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );

							} else {
								$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_invalid );

							}
						} else {

							if ( $response_data->score < $wbc_recapcha_signup_score_threshold_v3 || $response_data->action !== $wbc_recapcha_signup_action_v3 ) {

								if ( '' === trim( $recapcha_error_msg_captcha_invalid ) ) {

									$validation_errors->add( 'g-recaptcha_error', __( 'Google reCAPTCHA verification failed, please try again later.', 'buddypress-recaptcha' ) );

								} else {

									$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_invalid );

								}
							}
						}
					} else {

						if ( '' === trim( $recapcha_error_msg_captcha_no_response ) ) {

							$validation_errors->add( 'g-recaptcha_error', __( 'Could not get response from reCAPTCHA server.', 'buddypress-recaptcha' ) );

						} else {

							$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_no_response );

						}
					}
				} else {

					if ( '' === trim( $recapcha_error_msg_captcha_blank ) ) {

						$validation_errors->add( 'g-recaptcha_error', __( 'Google reCAPTCHA token is missing.', 'buddypress-recaptcha' ) );

					} else {

						$validation_errors->add( 'g-recaptcha_error', $recapcha_error_msg_captcha_blank );

					}
				}
			}
		}
		return $validation_errors;
	}
}
