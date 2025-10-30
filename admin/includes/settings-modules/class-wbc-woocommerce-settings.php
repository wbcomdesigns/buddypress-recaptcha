<?php
/**
 * WooCommerce Settings Module
 *
 * Handles settings for WooCommerce forms (login, registration, checkout).
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * WooCommerce Forms Settings
 *
 * Only active when WooCommerce plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_WooCommerce_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'woocommerce';
		$this->module_name = __( 'WooCommerce Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_woo_protection',
			__( 'WooCommerce Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_login',
					'label'   => __( 'Customer Login', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect customer account login', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_signup',
					'label'   => __( 'Customer Registration', 'buddypress-recaptcha' ),
					'desc'    => __( 'Prevent fake customer accounts', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_lostpassword',
					'label'   => __( 'Lost Password', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect password reset from abuse', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_guestcheckout',
					'label'   => __( 'Guest Checkout', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect checkout from bots (not logged in)', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_logincheckout',
					'label'   => __( 'Logged-in Checkout', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect checkout for logged-in users', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
			)
		);
	}

	/**
	 * Get checkbox field IDs
	 *
	 * @return array
	 */
	public function get_checkbox_ids() {
		return array(
			'wbc_recaptcha_enable_on_login',
			'wbc_recaptcha_enable_on_signup',
			'wbc_recaptcha_enable_on_lostpassword',
			'wbc_recaptcha_enable_on_guestcheckout',
			'wbc_recaptcha_enable_on_logincheckout',
		);
	}
}
