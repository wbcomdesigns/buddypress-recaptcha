<?php
/**
 * Divi Builder Settings Module
 *
 * Handles settings for Divi Builder contact forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Divi Builder Forms Settings
 *
 * Only active when Divi theme or Divi Builder plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_Divi_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'divi';
		$this->module_name = __( 'Divi Builder', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Divi Builder is active
	 *
	 * @return bool
	 */
	public function is_active() {
		// Check for Divi theme or Divi Builder plugin.
		return defined( 'ET_BUILDER_VERSION' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_divi_protection',
			__( 'Divi Builder', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_divi',
					'label'   => __( 'Divi Contact Forms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect Divi Builder contact forms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_divi',
		);
	}
}
