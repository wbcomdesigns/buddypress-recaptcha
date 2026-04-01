<?php
/**
 * BuddyPress Settings Module
 *
 * Handles settings for BuddyPress/BuddyBoss forms (registration).
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * BuddyPress Forms Settings
 *
 * Only active when BuddyPress or BuddyBoss Platform is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_BuddyPress_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'buddypress';
		$this->module_name = __( 'BuddyPress Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if BuddyPress is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'BuddyPress' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_bp_protection',
			__( 'BuddyPress Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_buddypress',
					'label'   => __( 'Member Registration', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect community registration', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_bp_group_create',
					'label'   => __( 'Group Creation', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect group creation form from spam', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_buddypress',
			'wbc_recaptcha_enable_on_bp_group_create',
		);
	}
}
