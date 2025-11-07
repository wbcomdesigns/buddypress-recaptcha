<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_BuddyPress_Activator {

	/**
	 * Initialize default protection settings
	 *
	 * Sets default values for form protection options on plugin activation.
	 * These defaults can be changed by the user in the admin settings.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Initialize default protection options if they don't exist
		$defaults = array(
			// WordPress Core Forms
			'wbc_recaptcha_enable_on_wplogin' => 'yes',
			'wbc_recaptcha_enable_on_wpregister' => 'yes',
			'wbc_recaptcha_enable_on_wplostpassword' => 'yes',
			// WooCommerce Forms
			'wbc_recaptcha_enable_on_login' => 'no',
			'wbc_recaptcha_enable_on_signup' => 'yes',
			'wbc_recaptcha_enable_on_lostpassword' => 'no',
			'wbc_recaptcha_enable_on_guestcheckout' => 'yes',
			'wbc_recaptcha_enable_on_logincheckout' => 'no',
			// BuddyPress/BuddyBoss Forms
			'wbc_recaptcha_enable_on_buddypress' => 'yes',
			'wbc_recaptcha_enable_on_bp_group_create' => 'yes',
			// bbPress Forms
			'wbc_recaptcha_enable_on_bbpress_topic' => 'yes',
			'wbc_recaptcha_enable_on_bbpress_reply' => 'yes',
			// Other Forms
			'wbc_recaptcha_enable_on_comment' => 'yes',
			// Form Plugins
			'wbc_recaptcha_enable_on_cf7' => 'yes',
			'wbc_recaptcha_enable_on_wpforms' => 'yes',
			'wbc_recaptcha_enable_on_gravityforms' => 'yes',
			'wbc_recaptcha_enable_on_ninjaforms' => 'yes',
			'wbc_recaptcha_enable_on_forminator' => 'yes',
			'wbc_recaptcha_enable_on_elementorpro' => 'yes',
			'wbc_recaptcha_enable_on_divi' => 'yes',
			// Easy Digital Downloads
			'wbc_recaptcha_enable_on_edd_checkout' => 'yes',
			'wbc_recaptcha_enable_on_edd_login' => 'no',
			'wbc_recaptcha_enable_on_edd_register' => 'yes',
			// MemberPress
			'wbc_recaptcha_enable_on_memberpress_login' => 'no',
			'wbc_recaptcha_enable_on_memberpress_register' => 'yes',
			// Ultimate Member
			'wbc_recaptcha_enable_on_um_login' => 'no',
			'wbc_recaptcha_enable_on_um_register' => 'yes',
			'wbc_recaptcha_enable_on_um_password' => 'no',
			// Widgets
			'wbc_recaptcha_enable_on_widget_login' => 'no',
		);

		foreach ( $defaults as $option_name => $default_value ) {
			// Only set if the option doesn't exist (to avoid overwriting user settings)
			if ( false === get_option( $option_name ) ) {
				add_option( $option_name, $default_value );
			}
		}
	}

}
