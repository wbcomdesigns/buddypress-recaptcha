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
class Regisrtationbp {

public function woo_extra_wp_register_form() {

$reCapcha_version = get_option('i13_recapcha_version'); 
if (''==$reCapcha_version) {
$reCapcha_version='v2';
}
if ('v2'==strtolower($reCapcha_version)) {
$disable_submit_btn=get_option('i13_recapcha_disable_submitbtn_woo_signup_bp');
$i13_recapcha_hide_label_wpregister=get_option('i13_recapcha_hide_label_signup_bp');
$captcha_lable = get_option('i13_recapcha_signup_title_bp');
$captcha_lable_ = $captcha_lable;
$site_key = get_option('wc_settings_tab_recapcha_site_key');
$theme = get_option('i13_recapcha_signup_theme_bp');
$size = get_option('i13_recapcha_signup_size_bp');
$is_enabled = get_option('i13_recapcha_enable_on_signup_bp');
$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict');
$recapcha_error_msg_captcha_blank = get_option('wc_settings_tab_recapcha_error_msg_captcha_blank');
if (''==trim($captcha_lable_)) {

$captcha_lable_='recaptcha';
}
$recapcha_error_msg_captcha_blank = str_replace('[recaptcha]', ucfirst($captcha_lable_), $recapcha_error_msg_captcha_blank);


if ('yes' == $is_enabled) {

if ('yes'== $i13_recapcha_no_conflict) {

global $wp_scripts;

$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

foreach ( $wp_scripts->queue as $handle ) {

foreach ( $urls as $url ) {
if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
wp_dequeue_script($handle);
wp_deregister_script($handle);
break;
}
}
}
}
wp_enqueue_script('jquery');
wp_enqueue_script('i13-woo-captcha');
?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html(wp_create_nonce('wp-register-nonce')); ?>" />
<p class="wp_register_captcha">
<?php 
if ('yes'!=$i13_recapcha_hide_label_wpregister) :
?>
<label for="g-recaptcha-wp-register-i13"><?php echo esc_html(( ''==trim($captcha_lable) )? __('Captcha', 'recaptcha-for-woocommerce') :esc_html($captcha_lable)); ?>&nbsp;</label>
<?php 
endif; 
?>
<div name="g-recaptcha-wp-register-i13" class="g-recaptcha" data-callback="verifyCallback_wp_register"  data-sitekey="<?php echo esc_html($site_key); ?>" data-theme="<?php echo esc_html($theme); ?>" data-size="<?php echo esc_html($size); ?>"></div>
<br/>


</p>

<script type="text/javascript">

var myCaptcha = null;    
<?php $intval_signup= uniqid('interval_'); ?>

var <?php echo esc_html($intval_signup); ?> = setInterval(function() {

if(document.readyState === 'complete') {

clearInterval(<?php echo esc_html($intval_signup); ?>);
<?php if ('yes'==trim($disable_submit_btn)) : ?>

	jQuery('#wp-submit').attr("disabled", true);
	<?php if (''==$recapcha_error_msg_captcha_blank) : ?>
			  jQuery('#wp-submit').attr("title", "<?php echo esc_html(__('Recaptcha is a required field.', 'recaptcha-for-woocommerce')); ?>");
	<?php else : ?>
					jQuery('#wp-submit').attr("title", "<?php echo esc_html($recapcha_error_msg_captcha_blank); ?>");
	<?php endif; ?>    
<?php endif; ?>


}    
}, 100);    


var verifyCallback_wp_register = function(response) {

if(response.length!==0){

<?php if ('yes'==trim($disable_submit_btn)) : ?> 
jQuery('#wp-submit').removeAttr("title");
jQuery('#wp-submit').attr("disabled", false);
<?php endif; ?> 

if (typeof woo_wp_register_captcha_verified === "function") { 

woo_wp_register_captcha_verified(response);
}  
}


};  



</script>        
<?php if ('compact'!=$size) : ?>                                       
<style type="text/css">
[name="g-recaptcha-wp-register-i13"]{
transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
}
</style>  
<?php endif; ?>                                                        
<?php

}
} else {

$is_enabled = get_option('i13_recapcha_enable_on_signup_bp');
$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict_v3');
$i13_recapcha_wp_disable_wp_register=get_option('i13_recapcha_wp_disable_submit_token_generation_v3_woo_signup_bp');
if ('yes' == $is_enabled) {

if ('yes'== $i13_recapcha_no_conflict) {

global $wp_scripts;

$urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );

foreach ( $wp_scripts->queue as $handle ) {

foreach ( $urls as $url ) {
if (false !== strpos($wp_scripts->registered[ $handle ]->src, $url) && ( 'i13-woo-captcha'!=$handle && 'i13-woo-captcha-v3'!=$handle ) ) {
wp_dequeue_script($handle);
wp_deregister_script($handle);
break;
}
}
}
}
wp_enqueue_script('jquery');
wp_enqueue_script('i13-woo-captcha-v3');

$site_key = get_option('wc_settings_tab_recapcha_site_key_v3');
$i13_recapcha_wp_register_method_action_v3 = get_option('i13_recapcha_wp_register_method_action_v3');
if (''==trim($i13_recapcha_wp_register_method_action_v3)) {

$i13_recapcha_wp_register_method_action_v3='wp_registration';
}

if (''==trim($i13_recapcha_wp_disable_wp_register)) {

$i13_recapcha_wp_disable_wp_register='no';
}
?>
<input type="hidden" autocomplete="off" name="wp-register-nonce" value="<?php echo esc_html(wp_create_nonce('wp-register-nonce')); ?>" />
<input type="hidden" autocomplete="off" name="i13_recaptcha_wp_register_token" value="" id="i13_recaptcha_wp_register_token" />

<script type="text/javascript">

<?php $intval_wp_register= uniqid('interval_'); ?>

var <?php echo esc_html($intval_wp_register); ?> = setInterval(function() {

if(document.readyState === 'complete') {

clearInterval(<?php echo esc_html($intval_wp_register); ?>);


grecaptcha.ready(function () {

grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_register_method_action_v3); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('i13_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});
});

<?php if ('yes'==$i13_recapcha_wp_disable_wp_register) : ?>

setInterval(function() {

grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_register_method_action_v3); ?>' }).then(function (token) {

var recaptchaResponse = document.getElementById('i13_recaptcha_wp_register_token');
recaptchaResponse.value = token;
});

}, 40 * 1000); 

<?php else : ?>
jQuery('#registerform').on('submit', function (e) {

var frm = this;
e.preventDefault();

grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_register_method_action_v3); ?>' }).then(function (token) {

submitval=jQuery("#wp-submit").val();
var recaptchaResponse = document.getElementById('i13_recaptcha_wp_register_token');
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
do_action('bp_accept_tos_errors');
}


function innovage_validate_user_registration() {
	global $bp;
	$disable_submit_btn=get_option('i13_recapcha_enable_on_signup_bp');

		if($disable_submit_btn=='yes' && empty($_POST['g-recaptcha-response'])){
			$bp->signup->errors['accept_tos'] = __('reCaptcha token is invalid'.$disable_submit_btn, 'buddypress');
		}
		return;
	}

function form_field() {
	$is_enabled = get_option('recapcha_enable_on_bbpress_topic');
	if($is_enabled=='yes'){
		$lable = get_option('recapcha_bbpress_topic_title');
		$hide_lable = get_option('recapcha_hide_label_bbpress_topic');			
		if(!empty($lable) AND $hide_lable != 'yes'){echo $lable;}
		echo $this->form_field_return();
	}	
}

function form_field_bp(){
	echo $this->form_field_return();
		$this->v2_checkbox_script();
}
function form_field_return( $return = '' ) {
		// $ip = $_SERVER['REMOTE_ADDR'];
		// if ( in_array( $ip, array_filter( explode( '\n', anr_get_option( 'whitelisted_ips' ) ) ) ) ) {
		// 	return $return;
		// }
		return $return . $this->captcha_form_field();
	}
function captcha_form_field() {
			$version = get_option('i13_recapcha_version'); 
			$site_key = get_option('wc_settings_tab_recapcha_site_key');
			$number   = 0;
			
			$field = '<div class="anr_captcha_field"><div id="anr_captcha_field_' . $number . '" class="anr_captcha_field_div">';

			if ( 'v3' === $version ) {
				$field .= '<input type="hidden" name="g-recaptcha-response" value="" />';
			}

			$field .= '</div></div>';

			if ('v2' === $version ) {

				$field .= sprintf( '<noscript>
						  <div>
							<div style="width: 302px; height: 422px; position: relative;">
							  <div style="width: 302px; height: 422px; position: absolute;">
								<iframe src="https://www.%s/recaptcha/api/fallback?k=' . $site_key . '"
										frameborder="0" scrolling="no"
										style="width: 302px; height:422px; border-style: none;">
								</iframe>
							  </div>
							</div>
							<div style="width: 300px; height: 60px; border-style: none;
										   bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
										   background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
							  <textarea id="g-recaptcha-response-' . $number . '" name="g-recaptcha-response"
										   class="g-recaptcha-response"
										   style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
												  margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						  </div>
						</noscript>', $this->anr_recaptcha_domain() );
			}
			return $field;
		}

	function anr_recaptcha_domain(){
		// $domain = anr_get_option( 'recaptcha_domain', 'google.com' );
		$domain = 'google.com';
		return apply_filters( 'anr_recaptcha_domain', $domain );
	}

	function verify( $response = false ) {
			static $last_verify = null;

			if ( is_user_logged_in() ) {
				return true;
			}
	
			$secre_key  = trim( get_option( 'wc_settings_tab_recapcha_secret_key' ) );
			$remoteip = $_SERVER['REMOTE_ADDR'];
			$verify = false;

			
			if ( false === $response ) {
				$response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
			}
			
			
			if ( ! $secre_key ) { // if $secre_key is not set
				return true;
			}

			if ( ! $response || ! $remoteip ) {
				return $verify;
			}
			
			if ( null !== $last_verify ) {
				return $last_verify;
			}

			$url = apply_filters( 'anr_google_verify_url', sprintf( 'https://www.%s/recaptcha/api/siteverify', anr_recaptcha_domain() ) );

			// make a POST request to the Google reCAPTCHA Server
			$request = wp_remote_post(
				$url, array(
					'timeout' => 10,
					'body'    => array(
						'secret'   => $secre_key,
						'response' => $response,
						'remoteip' => $remoteip,
					),
				)
			);

			// get the request response body
			$request_body = wp_remote_retrieve_body( $request );
			if ( ! $request_body ) {
				return $verify;
			}

				$result = json_decode( $request_body, true );
			if ( isset( $result['success'] ) && true == $result['success'] ) {
				if ( 'v3' === get_option( 'i13_recapcha_version' ) ) {
					$score = isset( $result['score'] ) ? $result['score'] : 0;
					$action = isset( $result['action'] ) ? $result['action'] : '';
					$verify = anr_get_option( 'score', '0.5' ) <= $score && 'advanced_nocaptcha_recaptcha' == $action;
				} else {
					$verify = true;
				}
			}
			$verify = apply_filters( 'anr_verify_captcha', $verify, $result, $response );
			$last_verify = $verify;

			return $verify;
		}

		function bbp_new_verify( $forum_id ) {
			$is_enabled = get_option('recapcha_enable_on_bbpress_topic');
			if($is_enabled=='yes' && empty($_POST['g-recaptcha-response'])){
				bbp_add_error( 'anr_error', 'reCaptcha is required' );
			}
			if ( ! $this->verify() ) {
				bbp_add_error( 'anr_error', $this->add_error_to_mgs() );
			}
		}

		function bbp_reply_verify( $topic_id = '', $forum_id = '' ) {
			$is_enabled = get_option('recapcha_enable_on_bbpress_topic');
			if($is_enabled=='yes' && empty($_POST['g-recaptcha-response'])){
				bbp_add_error( 'anr_error', 'reCaptcha is required' );
			}
			if ( ! $this->verify() ) {
				bbp_add_error( 'anr_error', $this->add_error_to_mgs() );
			}
		}

		function v2_checkbox_script() {
			?>
			<script type="text/javascript" async defer>
				var anr_onloadCallback = function() {
					for ( var i = 0; i < document.forms.length; i++ ) {
						var form = document.forms[i];
						var captcha_div = form.querySelector( '.anr_captcha_field_div' );

						if ( null === captcha_div )
							continue;
						captcha_div.innerHTML = '';
						( function( form ) {
							var anr_captcha = grecaptcha.render( captcha_div,{
								'sitekey' : '<?php echo esc_js( trim( get_option( 'wc_settings_tab_recapcha_site_key' ) ) ); ?>',
								'size'  : '<?php echo esc_js( get_option( 'recapcha_size_bbpress_topic' ) ); ?>',
								'theme' : '<?php echo esc_js( get_option( 'recapcha_theme_bbpress_topic' ) ); ?>'
							});
							if ( typeof jQuery !== 'undefined' ) {
								jQuery( document.body ).on( 'checkout_error', function(){
									grecaptcha.reset(anr_captcha);
								});
							}
							if ( typeof wpcf7 !== 'undefined' ) {
								document.addEventListener( 'wpcf7submit', function() {
									grecaptcha.reset(anr_captcha);
								}, false );
							}
						})(form);
					}
				};
			</script>
			<?php
			$language = trim( get_option( 'language' ) );

			$lang = 'eng';
			if ( $language ) {
				$lang = '&hl=' . $language;
			}
			$google_url = apply_filters( 'anr_v2_checkbox_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, $this->anr_recaptcha_domain() ), $lang );
			?>
			<script src="<?php echo esc_url( $google_url ); ?>"
				async defer>
			</script>
			<?php
		}
}
