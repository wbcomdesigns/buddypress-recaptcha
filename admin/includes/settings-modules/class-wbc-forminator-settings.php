<?php
/**
 * Forminator Settings Module
 *
 * Handles settings for Forminator forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Forminator Forms Settings
 *
 * Only active when Forminator plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_Forminator_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'forminator';
		$this->module_name = __( 'Forminator', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Forminator is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Forminator' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_forminator_protection',
			__( 'Forminator', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_forminator',
					'label'   => __( 'Forminator Forms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect all Forminator forms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_forminator',
		);
	}
}
