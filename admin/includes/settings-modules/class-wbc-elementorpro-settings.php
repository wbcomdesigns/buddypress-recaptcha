<?php
/**
 * Elementor Pro Settings Module
 *
 * Handles settings for Elementor Pro forms.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Elementor Pro Forms Settings
 *
 * Only active when Elementor Pro plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_ElementorPro_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'elementorpro';
		$this->module_name = __( 'Elementor Pro', 'buddypress-recaptcha' );
	}

	/**
	 * Check if Elementor Pro is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_elementorpro_protection',
			__( 'Elementor Pro', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_elementorpro',
					'label'   => __( 'Elementor Pro Forms', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect all Elementor Pro forms from spam submissions', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_elementorpro',
		);
	}
}
