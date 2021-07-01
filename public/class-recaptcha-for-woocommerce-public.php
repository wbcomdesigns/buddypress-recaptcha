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
class Recaptcha_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '+.js/recaptcha-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

	}

	public function woo_recaptcha_load_styles_and_js() {

		$reCapcha_version = get_option('i13_recapcha_version'); 
		if (''==$reCapcha_version) {
			$reCapcha_version='v2';
		}
			 
		if ('v2'== strtolower($reCapcha_version)) {
								$i13_recapcha_v2_lang=esc_html(get_option('i13_recapcha_v2_lang')); 
			//wp_register_style('i13-woo-styles', plugins_url('/public/css/styles.css', __FILE__), array(), '1.0');
			if (''!=$i13_recapcha_v2_lang) {
									
				wp_register_script('i13-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&hl=' . $i13_recapcha_v2_lang, array(), '1.0');
				wp_register_script('i13-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=explicit&hl=' . $i13_recapcha_v2_lang, array(), '2.0');
			} else {
				 wp_register_script('i13-woo-captcha', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha', array(), '1.0');
				 wp_register_script('i13-woo-captcha-explicit', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=explicit', array(), '2.0');
			}
			$is_enabled = get_option('i13_recapcha_enable_on_guestcheckout');
			  $is_enabled_on_payment_page = get_option('i13_recapcha_enable_on_addpaymentmethod');

			  $is_enabled_logincheckout = get_option('i13_recapcha_enable_on_logincheckout');
			  $i13_recapcha_enable_on_payfororder = get_option('i13_recapcha_enable_on_payfororder');
			  $i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict');


			if ('yes' == $is_enabled_on_payment_page && is_user_logged_in() && is_wc_endpoint_url(get_option('woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method')) ) {

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
				wp_enqueue_script('i13-woo-captcha');
			}

			if ('yes' == $is_enabled && ( !is_user_logged_in() || $i13_recapcha_enable_on_payfororder ) && is_checkout() ) {

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
							wp_enqueue_script('i13-woo-captcha-explicit');
			} else if ('yes' == ( $is_enabled_logincheckout || $i13_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

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
				wp_enqueue_script('i13-woo-captcha-explicit');
			}
		} else {
					
					
			$site_key = get_option('wc_settings_tab_recapcha_site_key_v3');
			wp_register_script('i13-woo-captcha-v3', 'https://www.google.com/recaptcha/api.js?from=i13_recaptcha&render=' . esc_html($site_key), array('jquery'), '1.0');
			$is_enabled = get_option('i13_recapcha_enable_on_guestcheckout');
			$is_enabled_on_payment_page = get_option('i13_recapcha_enable_on_addpaymentmethod');
			$is_enabled_logincheckout = get_option('i13_recapcha_enable_on_logincheckout');
			$i13_recapcha_enable_on_payfororder = get_option('i13_recapcha_enable_on_payfororder');
			$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict_v3');


			if ('yes' == $is_enabled_on_payment_page && is_user_logged_in() && is_wc_endpoint_url(get_option('woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method')) ) {

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
				wp_enqueue_script('i13-woo-captcha-v3');
			}

			if ('yes' == $is_enabled && ( !is_user_logged_in() || $i13_recapcha_enable_on_payfororder ) && is_checkout() ) {

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
							wp_enqueue_script('i13-woo-captcha-v3');
			} else if ('yes' == ( $is_enabled_logincheckout || $i13_recapcha_enable_on_payfororder ) && is_user_logged_in() && is_checkout() ) {

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
				wp_enqueue_script('i13-woo-captcha-v3');
			}
					
					
		}
	}

	public function woo_extra_wp_login_form() {
	$reCapcha_version = get_option('i13_recapcha_version'); 
	if (''==$reCapcha_version) {
		$reCapcha_version='v2';
	}
	if ('v2'==strtolower($reCapcha_version)) {
	$disable_submit_btn=get_option('i13_recapcha_disable_submitbtn_wp_login');
	$i13_recapcha_hide_label_wplogin=get_option('i13_recapcha_hide_label_wplogin');
	$captcha_lable = get_option('i13_recapcha_wplogin_title');
	$captcha_lable_ = $captcha_lable;

	$recapcha_error_msg_captcha_blank = get_option('wc_settings_tab_recapcha_error_msg_captcha_blank');
	if (''==trim($captcha_lable_)) {

	$captcha_lable_='recaptcha';
	}
	$recapcha_error_msg_captcha_blank = str_replace('[recaptcha]', ucfirst($captcha_lable_), $recapcha_error_msg_captcha_blank);

	$site_key = get_option('wc_settings_tab_recapcha_site_key');
	$theme = get_option('i13_recapcha_wplogin_theme');
	$size = get_option('i13_recapcha_wplogin_size');
	$is_enabled = get_option('i13_recapcha_enable_on_wplogin');
	$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict');
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
	<input type="hidden" autocomplete="off" name="wp-login-nonce" value="<?php echo esc_html(wp_create_nonce('wp-login-nonce')); ?>" />
	<p class="i13_woo_wp_login_captcha">
	<?php 
	if ('yes'!=$i13_recapcha_hide_label_wplogin) :
	?>
	<label for="g-recaptcha-wp-login-i13"><?php echo esc_html(( ''==trim($captcha_lable) )? __('Captcha', 'recaptcha-for-woocommerce') :esc_html($captcha_lable)); ?>&nbsp;</label>
	<?php 
	endif; 
	?>
	<div name="g-recaptcha-wp-login-i13" class="g-recaptcha" data-callback="verifyCallback_wp_login"  data-sitekey="<?php echo esc_html($site_key); ?>" data-theme="<?php echo esc_html($theme); ?>" data-size="<?php echo esc_html($size); ?>"></div>
	<br/>
	</p>
	<script type="text/javascript">
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
	var verifyCallback_wp_login = function(response) {
	if(response.length!==0){
	<?php if ('yes'==trim($disable_submit_btn)) : ?>
	jQuery('#wp-submit').removeAttr("title");
	jQuery('#wp-submit').attr("disabled", false);
	<?php endif; ?>    
	if (typeof woo_wp_login_captcha_verified === "function") { 
	woo_wp_login_captcha_verified(response);
	}  
	}
	};  
	</script>
	<?php if ('compact'!=$size) : ?>                                       
	<style type="text/css">
	[name="g-recaptcha-wp-login-i13"]{
	transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;
	}
	</style>  
	<?php endif; ?>            
	<?php

	}
	} else {

	$is_enabled = get_option('i13_recapcha_enable_on_wplogin');
	$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict_v3');
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
	$i13_recapcha_wp_login_action_v3 = get_option('i13_recapcha_wp_login_action_v3');
	$i13_recapcha_wp_disable_generation_v3 = get_option('i13_recapcha_wp_disable_submit_token_generation_v3');
	if (''==trim($i13_recapcha_wp_login_action_v3)) {

	$i13_recapcha_wp_login_action_v3='wp_login';
	}
	if (''==$i13_recapcha_wp_disable_generation_v3) {

	$i13_recapcha_wp_disable_generation_v3='no';
	}

	?>
	<input type="hidden" autocomplete="off" name="wp-login-nonce" value="<?php echo esc_html(wp_create_nonce('wp-login-nonce')); ?>" />
	<input type="hidden" autocomplete="off" name="i13_recaptcha_token" value="" id="i13_recaptcha_token" />

	<script type="text/javascript">

	<?php $intval_wplogin= uniqid('interval_'); ?>

	var <?php echo esc_html($intval_wplogin); ?> = setInterval(function() {

	if(document.readyState === 'complete') {

	clearInterval(<?php echo esc_html($intval_wplogin); ?>);


	grecaptcha.ready(function () {

	grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_login_action_v3); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('i13_recaptcha_token');
	recaptchaResponse.value = token;
	});
	});

	<?php if ('yes'==$i13_recapcha_wp_disable_generation_v3) : ?>

	setInterval(function() {

	grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_login_action_v3); ?>' }).then(function (token) {

	var recaptchaResponse = document.getElementById('i13_recaptcha_token');
	recaptchaResponse.value = token;
	});

	}, 40 * 1000); 

	jQuery( document ).ajaxStart(function() {

	grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_login_action_v3); ?>' }).then(function (token) {

		  var recaptchaResponse = document.getElementById('i13_recaptcha_token');
		  recaptchaResponse.value = token;
	});

	});
	jQuery( document ).ajaxStop(function() {

	grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_login_action_v3); ?>' }).then(function (token) {

		  var recaptchaResponse = document.getElementById('i13_recaptcha_token');
		  recaptchaResponse.value = token;
	});

	});

	<?php else : ?>
	jQuery('#loginform').on('submit', function (e) {

	var frm = this;
	e.preventDefault();

	grecaptcha.execute('<?php echo esc_html($site_key); ?>', { action: '<?php echo esc_html($i13_recapcha_wp_login_action_v3); ?>' }).then(function (token) {

	submitval=jQuery("#wp-submit").val();
	var recaptchaResponse = document.getElementById('i13_recaptcha_token');
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

public function woo_extra_wp_register_form() {

$reCapcha_version = get_option('i13_recapcha_version'); 
if (''==$reCapcha_version) {
$reCapcha_version='v2';
}
if ('v2'==strtolower($reCapcha_version)) {
$disable_submit_btn=get_option('i13_recapcha_disable_submitbtn_wp_register');
$i13_recapcha_hide_label_wpregister=get_option('i13_recapcha_hide_label_wpregister');
$captcha_lable = get_option('i13_recapcha_wpregister_title');
$captcha_lable_ = $captcha_lable;
$site_key = get_option('wc_settings_tab_recapcha_site_key');
$theme = get_option('i13_recapcha_wpregister_theme');
$size = get_option('i13_recapcha_wpregister_size');
$is_enabled = get_option('i13_recapcha_enable_on_wpregister');
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

$is_enabled = get_option('i13_recapcha_enable_on_wpregister');
$i13_recapcha_no_conflict = get_option('i13_recapcha_no_conflict_v3');
$i13_recapcha_wp_disable_wp_register=get_option('i13_recapcha_wp_disable_submit_token_generation_v3_woo_wp_register');
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
}
}
