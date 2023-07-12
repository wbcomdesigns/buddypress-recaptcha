<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/
 * @since 1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/bp-classes
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
class Regisrtationbp {

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function woo_extra_bp_register_form() {
		$recpatcha_system_ip = get_option( 'wbc_recapcha_ip_to_skip_captcha' );
		if ( $recpatcha_system_ip && wb_recaptcha_restriction_recaptcha_by_ip() ) {
			return false;
		}
		$re_capcha_version = get_option( 'wbc_recapcha_version' );
		if ( '' === $re_capcha_version ) {
			$re_capcha_version = 'v2';
		}
		if ( 'v2' === strtolower( $re_capcha_version ) ) {
			$disable_submit_btn               = get_option( 'wbc_recapcha_disable_submitbtn_woo_signup_bp' );
			$site_key                         = get_option( 'wc_settings_tab_recapcha_site_key' );
			$theme                            = get_option( 'wbc_recapcha_signup_theme_bp' );
			$size                             = get_option( 'wbc_recapcha_signup_size_bp' );
			$is_enabled                       = get_option( 'wbc_recapcha_enable_on_signup_bp' );
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
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html( wp_create_nonce( 'wp-register-nonce' ) ); ?>" />
<p class="wp_register_captcha">
<div name="g-recaptcha-wp-register-wbc" class="g-recaptcha" data-callback="verifyCallback_wp_register"  data-sitekey="<?php echo esc_html( $site_key ); ?>" data-theme="<?php echo esc_html( $theme ); ?>" data-size="<?php echo esc_html( $size ); ?>"></div>
<br/>


</p>

<script type="text/javascript">

jQuery(document).ready(function(){
	var myCaptcha = null;
				<?php $intval_signup = uniqid( 'interval_' ); ?>

	var <?php echo esc_html( $intval_signup ); ?> = setInterval(function() {

	clearInterval(<?php echo esc_html( $intval_signup ); ?>);
					<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
					jQuery('#submit').attr("disabled", true);
					jQuery('#signup_submit').attr("disabled", true);
						<?php if ( '' === $recapcha_error_msg_captcha_blank ) : ?>
				jQuery('#submit').attr("title", "<?php echo esc_html( __( 'reCaptcha is a required field.', 'buddypress-recaptcha' ) ); ?>");
		<?php else : ?>
						jQuery('#submit').attr("title", "<?php echo esc_html( $recapcha_error_msg_captcha_blank ); ?>");
		<?php endif; ?>
					<?php endif; ?>

	}, 500);
});

var verifyCallback_wp_register = function(response) {
if(response.length!==0){
				<?php if ( 'yes' === trim( $disable_submit_btn ) ) : ?>
				jQuery('#submit').removeAttr("disabled");
				jQuery('#signup_submit').removeAttr("disabled");
				jQuery('#submit').removeAttr("title");
				jQuery('#signup_submit').removeAttr("title");
				<?php endif; ?>

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

			$is_enabled                          = get_option( 'wbc_recapcha_enable_on_signup_bp' );
			$wbc_recapcha_no_conflict            = get_option( 'wbc_recapcha_no_conflict_v3' );
			$wbc_recapcha_wp_disable_wp_register = get_option( 'wbc_recapcha_wp_disable_submit_token_generation_v3_woo_signup_bp' );
			if ( 'yes' === $is_enabled ) {

				if ( 'yes' === $wbc_recapcha_no_conflict ) {

					global $wp_scripts;

					$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

					foreach ( $wp_scripts->queue as $handle ) {

						foreach ( $urls as $url ) {
							if ( false !== strpos( $wp_scripts->registered[ $handle ]->src, $url ) && ( 'wbc-woo-captcha' !== $handle && 'wbc-woo-captcha-v3' != $handle ) ) {
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
		do_action( 'bp_accept_tos_errors' );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function innovage_validate_user_registration() {
		global $bp;
		$disable_submit_btn               = get_option( 'wbc_recapcha_enable_on_signup_bp' );
		$re_capcha_version                = get_option( 'wbc_recapcha_version' );
		$wbc_recapcha_enable_on_signup_bp = get_option( 'wbc_recapcha_enable_on_signup_bp' );
		$nonce                            = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $nonce, 'bp_new_signup' ) ) {
			die( 'Busted!' );
		}
		if ( 'yes' === $wbc_recapcha_enable_on_signup_bp ) {
			if ( 'v2' !== $re_capcha_version ) {
				$secret_key      = get_option( 'wc_settings_tab_recapcha_secret_key_v3' );
				$response        = ( isset( $_POST['wbc_recaptcha_wp_register_token'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wbc_recaptcha_wp_register_token'] ) ) : '';
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
				$response_data = json_decode( $verify_response['body'] );
				if ( ! $response_data->success ) {
					$error_meaasge                    = '<div class="bp-messages bp-feedback error"><span class="bp-icon" aria-hidden="true"></span><p>' . esc_html__( 'reCaptcha token is invalid', 'buddypress-recaptcha' ) . '</p></div>';
					$bp->signup->errors['accept_tos'] = $error_meaasge;
				}
			} else {
				if ( 'yes' === $disable_submit_btn && empty( $_POST['g-recaptcha-response'] ) ) {
					$error_meaasge                    = '<div class="bp-messages bp-feedback error"><span class="bp-icon" aria-hidden="true"></span><p>' . esc_html__( 'reCaptcha token is invalid', 'buddypress-recaptcha' ) . '</p></div>';
					$bp->signup->errors['accept_tos'] = $error_meaasge;
				}
			}
		}
		return;
	}
}