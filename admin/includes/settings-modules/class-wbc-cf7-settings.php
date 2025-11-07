<?php
/**
 * Contact Form 7 Settings Module
 *
 * Handles settings for Contact Form 7 forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Contact Form 7 Forms Settings
 *
 * Only active when Contact Form 7 plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_CF7_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'cf7';
		$this->module_name = __( 'Contact Form 7', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Contact Form 7 is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'WPCF7' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_cf7_protection',
			__( 'Contact Form 7', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_cf7',
					'label'   => __( 'Contact Forms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect all Contact Form 7 forms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_cf7',
		);
	}
}
