<?php
/**
 * Easy Digital Downloads Settings Module
 *
 * Handles settings for Easy Digital Downloads forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Easy Digital Downloads Forms Settings
 *
 * Only active when Easy Digital Downloads plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_EDD_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'edd';
		$this->module_name = __( 'Easy Digital Downloads', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Easy Digital Downloads is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Easy_Digital_Downloads' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_edd_protection',
			__( 'Easy Digital Downloads', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_edd_checkout',
					'label'   => __( 'Checkout Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect checkout form from spam registrations', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_edd_login',
					'label'   => __( 'Login Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect login form from brute force attacks', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_edd_register',
					'label'   => __( 'Registration Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect registration form from spam accounts', 'buddypress-recaptcha' ),
					'default' => 'yes',
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
			'wbc_recaptcha_enable_on_edd_checkout',
			'wbc_recaptcha_enable_on_edd_login',
			'wbc_recaptcha_enable_on_edd_register',
		);
	}
}
