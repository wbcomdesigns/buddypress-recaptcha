<?php
/**
 * WordPress Core Settings Module
 *
 * Handles settings for WordPress core forms (login, registration, lost password, comments).
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * WordPress Core Forms Settings
 *
 * Always active since these are core WordPress forms.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class WBC_WordPress_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'WordPress';
		$this->module_name = __( 'WordPress Core Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if module is active
	 *
	 * WordPress core is always active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_wp_protection',
			__( 'WordPress Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_wplogin',
					'label'   => __( 'Login Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect WordPress login from brute-force attacks', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_wpregister',
					'label'   => __( 'Registration Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Prevent spam user registrations', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_wplostpassword',
					'label'   => __( 'Lost Password Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect password reset from abuse', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_comment',
					'label'   => __( 'Comment Form', 'buddypress-recaptcha' ),
					'desc'    => __( 'Stop comment spam', 'buddypress-recaptcha' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_widget_login',
					'label'   => __( 'AJAX Login Widget', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect AJAX login widget from brute-force attacks', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_wplogin',
			'wbc_recaptcha_enable_on_wpregister',
			'wbc_recaptcha_enable_on_wplostpassword',
			'wbc_recaptcha_enable_on_comment',
			'wbc_recaptcha_enable_on_widget_login',
		);
	}
}
