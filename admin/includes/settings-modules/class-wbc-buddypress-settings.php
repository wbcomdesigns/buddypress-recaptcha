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
		$fields = array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_buddypress',
					'label'   => __( 'Member Registration', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect community registration', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_bp_group_create',
					'label'   => __( 'Group Creation', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect group creation form from spam', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
		);

		if ( self::contact_me_active() ) {
			$fields[] = array(
				'id'      => 'wbc_recaptcha_enable_on_contact_me',
				'label'   => __( 'Contact Me Form', 'buddypress-recaptcha' ),
				'desc'    => __( 'Protect the BuddyPress Contact Me form for logged-out visitors. When off, Contact Me uses its own math question.', 'buddypress-recaptcha' ),
				'default' => 'no',
			);
		}

		return $this->create_settings_section(
			'wbc_bp_protection',
			__( 'BuddyPress Forms', 'buddypress-recaptcha' ),
			$fields
		);
	}

	/**
	 * Whether BuddyPress Contact Me is active, so its switch is only shown when it can do something.
	 *
	 * @return bool
	 */
	private static function contact_me_active() {
		return defined( 'BUDDYPRESS_CONTACT_ME_VERSION' );
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
			'wbc_recaptcha_enable_on_contact_me',
		);
	}
}
