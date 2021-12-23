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
class Login {

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function woo_extra_wp_login_form() {
		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}
		if ( 'v2' === strtolower( $re_capcha_version ) ) {
			$disable_submit_btn              = get_option( 'wbc_recapcha_disable_submitbtn_wp_login' );
			$wbc_recapcha_hide_label_wplogin = get_option( 'wbc_recapcha_hide_label_wplogin' );
			$captcha_lable                   = get_option( 'wbc_recapcha_wplogin_title' );
			$captcha_lable_                  = $captcha_lable;

			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			if ( '' === trim( $captcha_lable_ ) ) {

				$captcha_lable_ = 'recaptcha';
			}
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

			$site_key                 = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                    = get_option( 'wbc_recapcha_wplogin_theme' );
			$size                     = get_option( 'wbc_recapcha_wplogin_size' );
			$is_enabled               = get_option( 'wbc_recapcha_enable_on_wplogin' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict' );
			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
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
	<input type="hidden" autocomplete="off" name="wp-login-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-login-nonce' ) ); ?>" />
	<p class="wbc_woo_wp_login_captcha">
				<?php
				if ( 'yes' === $wbc_recapcha_hide_label_wplogin ) :
					?>
	<label for="g-recaptcha-wp-login-wbc"><?php echo esc_html( ( '' === trim( $captcha_lable ) ) ? __( 'Captcha', 'buddypress-recaptcha' ) : esc_html( $captcha_lable ) ); ?>&nbsp;</label>
					<?php
			endif;
				?>
	<div name="g-recaptcha-wp-login-wbc" class="g-recaptcha" data-callback="verifyCallback_wp_login"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
	<br/>
	</p>
	<script type="text/javascript">
				<?php $intval_signup = uniqid( 'interval_' ); ?>
	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);

				<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
	jQuery('#wp-submit').attr("disabled", true);
					<?php if ( '' === $recapcha_error_msg_captcha_blank ) : ?>
	jQuery('#wp-submit').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
	<?php else : ?>
	jQuery('#wp-submit').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
	<?php endif; ?>
	<?php endif; ?>
	}
	}, 100);
	var verifyCallback_wp_login = function(response) {
	if(response.length!==0){
				<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
	jQuery('#wp-submit').removeAttr("title");
	jQuery('#wp-submit').attr("disabled", false);
	<?php endif; ?>
	if (typeof woo_wp_login_captcha_verified === "function") {
	woo_wp_login_captcha_verified(response);
	}
	}
	};
	</script>
				<?php if ( 'compact' !== $size ) : ?>
	<style type="text/css">
	[name="g-recaptcha-wp-login-wbc"]{
	transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
	}
	</style>
	<?php endif; ?>
				<?php

			}
		} else {

			$is_enabled               = get_option( 'wbc_recapcha_enable_on_wplogin' );
			$wbc_recapcha_no_conflict = get_option( 'wbc_recapcha_no_conflict_v3' );
			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' !== $handle ) ) {
								wp_dequeue_script( $handle );
								wp_deregister_script( $handle );
								break;
							}
						}
					}
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wbc-woo-captcha-v3' );

				$site_key                              = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_wp_login_action_v3       = get_option( 'wbc_recapcha_wp_login_action_v3' );
				$wbc_recapcha_wp_disable_generation_v3 = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3' );
				if ( '' === trim( $wbc_recapcha_wp_login_action_v3 ) ) {

					$wbc_recapcha_wp_login_action_v3 = 'wp_login';
				}
				if ( '' === $wbc_recapcha_wp_disable_generation_v3 ) {

					$wbc_recapcha_wp_disable_generation_v3 = 'no';
				}

				?>
	<input type="hidden" autocomplete="off" name="wp-login-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-login-nonce' ) ); ?>" />
	<input type="hidden" autocomplete="off" name="wbc_recaptcha_token" value="" id="wbc_recaptcha_token" />

	<script type="text/javascript">

				<?php $intval_wplogin = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_wplogin ); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html( $intval_wplogin ); ?>);


	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_login_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
	recaptchaResponse.value = token;
	});
	});

				<?php if ( 'yes' === $wbc_recapcha_wp_disable_generation_v3 ) : ?>

	setInterval(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_login_action_v3 ); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
	recaptchaResponse.value = token;
	});

	}, 40 * 1000);

	jQuery( document ).ajaxStart(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_login_action_v3 ); ?>' }).then(function (token) {

		var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
		recaptchaResponse.value = token;
	});

	});
	jQuery( document ).ajaxStop(function() {

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_login_action_v3 ); ?>' }).then(function (token) {

		var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
		recaptchaResponse.value = token;
	});

	});

	<?php else : ?>
	jQuery('#loginform').on('submit', function (e) {

	var frm = this;
	e.preventDefault();

	grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_login_action_v3 ); ?>' }).then(function (token) {

	submitval=jQuery("#wp-submit").val();
	var recaptchaResponse = document.getElementById('wbc_recaptcha_token');
	recaptchaResponse.value = token;
	jQuery('#loginform').prepend('<input type="hidden" name="Log In" value="' + submitval + '">');

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
