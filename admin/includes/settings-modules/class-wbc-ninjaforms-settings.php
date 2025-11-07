<?php
/**
 * Ninja Forms Settings Module
 *
 * Handles settings for Ninja Forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Ninja Forms Settings
 *
 * Only active when Ninja Forms plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_NinjaForms_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'ninjaforms';
		$this->module_name = __( 'Ninja Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Ninja Forms is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Ninja_Forms' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_ninjaforms_protection',
			__( 'Ninja Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_ninjaforms',
					'label'   => __( 'Ninja Forms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect all Ninja Forms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_ninjaforms',
		);
	}
}
