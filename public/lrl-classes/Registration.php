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
class Registration {

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function woo_extra_wp_register_form() {
		$recpatcha_system_ip = get_option( 'wbc_recapcha_ip_to_skip_captcha' );
		if ( $recpatcha_system_ip && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return false;
		}
		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}
		if ( 'v2' === strtolower( $re_capcha_version ) ) {
			$disable_submit_btn               = get_option( 'wbc_recapcha_disable_submitbtn_wp_register' );
			$site_key                         = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                            = get_option( 'wbc_recapcha_wpregister_theme' );
			$size                             = get_option( 'wbc_recapcha_wpregister_size' );
			$is_enabled                       = get_option( 'wbc_recapcha_enable_on_wpregister' );
			$wbc_recapcha_no_conflict         = get_option( 'wbc_recapcha_no_conflict' );
			$recapcha_error_msg_captcha_blank = get_option( 'wc_settings_tab_recapcha_error_msg_captcha_blank' );
			$captcha_lable_                   = 'Captcha';
			$recapcha_error_msg_captcha_blank = str_replace( '[recaptcha]', ucfirst( $captcha_lable_ ), $recapcha_error_msg_captcha_blank );

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
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<p class="wp_register_captcha">
<div name="g-recaptcha-wp-register-wbc" class="g-recaptcha" data-callback="verifyCallback_wp_register"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
<br/>


</p>

<script type="text/javascript">

var myCaptcha = null;
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


var verifyCallback_wp_register = function(response) {

if(response.length!==0){

	<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
	jQuery('#wp-submit').removeAttr("title");
	jQuery('#wp-submit').attr("disabled", false);
	<?php endif; ?>

	if ( jQuery('.bplock-login-form-container').length ) {
		jQuery('#bplock-login-btn').removeClass('has-recaptcha-error');
	}
	if ( jQuery('.bplock-register-form-container').length ) {
		jQuery('#bplock-register-btn').removeClass('has-recaptcha-error');
	}

	if (typeof woo_wp_register_captcha_verified === "function") {

	woo_wp_register_captcha_verified(response);
	}
}


};



</script>
				<?php if ( 'compact' !== $size ) : ?>
<style type="text/css">
[name="g-recaptcha-wp-register-wbc"]{
transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
}
</style>
<?php endif; ?>
				<?php

			}
		} else {

			$is_enabled                          = get_option( 'wbc_recapcha_enable_on_wpregister' );
			$wbc_recapcha_no_conflict            = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_recapcha_wp_disable_wp_register = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_wp_register' );
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

				$site_key                                  = get_option( 'wc_settings_tab_recapcha_site_key_v3' );
				$wbc_recapcha_wp_register_method_action_v3 = get_option( 'wbc_recapcha_wp_register_method_action_v3' );
				if ( '' === trim( $wbc_recapcha_wp_register_method_action_v3 ) ) {

					$wbc_recapcha_wp_register_method_action_v3 = 'wp_registration';
				}

				if ( '' === trim( $wbc_recapcha_wp_disable_wp_register ) ) {

					$wbc_recapcha_wp_disable_wp_register = 'no';
				}
				?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<input type="hidden" autocomplete="off" name="wbc_recaptcha_wp_register_token" value="" id="wbc_recaptcha_wp_register_token" />

<script type="text/javascript">

				<?php $intval_wp_register = uniqid( 'interval_' ); ?>

var <?php echo esc_html( $intval_wp_register ); ?> = setInterval(function() {

if(document.readyState === 'complete') {

clearInterval(<?php echo esc_html( $intval_wp_register ); ?>);


grecaptcha.ready(function () {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});
});

				<?php if ( 'yes' === $wbc_recapcha_wp_disable_wp_register ) : ?>

setInterval(function() {

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});

}, 40 * 1000);

<?php else : ?>
jQuery('#registerform').on('submit', function (e) {

var frm = this;
e.preventDefault();

grecaptcha.execute('<?php echo esc_html( $site_key ); ?>', { action: '<?php echo esc_html( $wbc_recapcha_wp_register_method_action_v3 ); ?>' }).then(function (token) {

submitval=jQuery("#wp-submit").val();
var recaptchaResponse = document.getElementById('wbc_recaptcha_wp_register_token');
recaptchaResponse.value = token;
jQuery('#registerform').prepend('<input type="hidden" name="wp-submit" value="' + submitval + '">');

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
