<?php
/**
 * WPForms Settings Module
 *
 * Handles settings for WPForms forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * WPForms Forms Settings
 *
 * Only active when WPForms plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_WPForms_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'wpforms';
		$this->module_name = __( 'WPForms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if WPForms is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'wpforms' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_wpforms_protection',
			__( 'WPForms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_wpforms',
					'label'   => __( 'WPForms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect all WPForms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_wpforms',
		);
	}
}
