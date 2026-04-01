<?php
/**
 * MemberPress Settings Module
 *
 * Handles settings for MemberPress forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * MemberPress Forms Settings
 *
 * Only active when MemberPress plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_MemberPress_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'memberpress';
		$this->module_name = __( 'MemberPress', 'buddypress-recaptcha' );
	}

	/**
	 * Check if MemberPress is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return defined( 'MEPR_VERSION' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_memberpress_protection',
			__( 'MemberPress', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_memberpress_login',
					'label'   => __( 'Login Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect MemberPress login form from brute force attacks', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_memberpress_register',
					'label'   => __( 'Registration Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect MemberPress registration form from spam accounts', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_memberpress_login',
			'wbc_recaptcha_enable_on_memberpress_register',
		);
	}
}
