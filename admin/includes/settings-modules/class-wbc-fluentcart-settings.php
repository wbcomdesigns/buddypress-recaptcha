<?php
/**
 * FluentCart Settings Module
 *
 * Handles settings for FluentCart forms (login, registration).
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * FluentCart Forms Settings
 *
 * Only active when FluentCart plugin is installed and active.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_FluentCart_Settings extends WBC_Settings_Module_Abstract {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->module_id   = 'fluentcart';
		$this->module_name = __( 'FluentCart Forms', 'buddypress-recaptcha' );
	}

	/**
	 * Check if FluentCart is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'FluentCart\App\App' ) || defined( 'FLUENT_CART_VERSION' );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		return $this->create_settings_section(
			'wbc_fluentcart_protection',
			__( 'FluentCart Forms', 'buddypress-recaptcha' ),
			array(
				array(
					'id'      => 'wbc_recaptcha_enable_on_fluentcart_login',
					'label'   => __( 'Customer Login', 'buddypress-recaptcha' ),
					'desc'    => __( 'Protect FluentCart customer login form', 'buddypress-recaptcha' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wbc_recaptcha_enable_on_fluentcart_register',
					'label'   => __( 'Customer Registration', 'buddypress-recaptcha' ),
					'desc'    => __( 'Prevent fake FluentCart customer accounts', 'buddypress-recaptcha' ),
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
			'wbc_recaptcha_enable_on_fluentcart_login',
			'wbc_recaptcha_enable_on_fluentcart_register',
		);
	}
}
