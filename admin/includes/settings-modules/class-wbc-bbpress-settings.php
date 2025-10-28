<?php
/**
 * bbPress Settings Module
 *
 * Handles settings for bbPress forum forms (topics, replies).
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * bbPress Forum Forms Settings
 *
 * Only active when bbPress plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_bbPress_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'bbpress';
		$this->module_name = __( 'bbPress Forum Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if bbPress is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'bbPress' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_bbpress_protection',
			__( 'bbPress Forum Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_bbpress_topic',
					'label'   => __( 'New Topics', 'buddypress-recaptcha' ),
					'desc'    => __( 'Prevent spam topics', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_bbpress_reply',
					'label'   => __( 'Topic Replies', 'buddypress-recaptcha' ),
					'desc'    => __( 'Stop spam replies', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_bbpress_topic',
			'wbc_recaptcha_enable_on_bbpress_reply',
		);
	}
}
