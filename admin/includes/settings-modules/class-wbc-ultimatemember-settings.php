<?php
/**
 * Ultimate Member Settings Module
 *
 * Handles settings for Ultimate Member forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Ultimate Member Forms Settings
 *
 * Only active when Ultimate Member plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_UltimateMember_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'ultimatemember';
		$this->module_name = __( 'Ultimate Member', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Ultimate Member is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return defined( 'ultimatemember_version' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_ultimatemember_protection',
			__( 'Ultimate Member', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_um_login',
					'label'   => __( 'Login Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect Ultimate Member login form from brute force attacks', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_um_register',
					'label'   => __( 'Registration Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect Ultimate Member registration form from spam accounts', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_um_password',
					'label'   => __( 'Password Reset Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect Ultimate Member password reset form from abuse', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_um_login',
			'wbc_recaptcha_enable_on_um_register',
			'wbc_recaptcha_enable_on_um_password',
		);
	}
}
