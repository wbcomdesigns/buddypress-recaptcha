<?php
/**
 * Settings Module Interface
 *
 * Interface for modular settings in Wbcom CAPTCHA Manager plugin.
 * Each integration (WooCommerce, FluentCart, BuddyPress, etc.) implements this interface.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Settings Module Interface
 *
 * Defines the contract for all settings modules.
 * Each module is responsible for:
 * - Checking if its dependent plugin is active
 * - Returning its protection settings
 * - Returning its checkbox field IDs for saving
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
interface WBC_Settings_Module_Interface {

	/**
	 * Check if the dependent plugin/feature is active
	 *
	 * @return bool True if plugin is active, false otherwise.
	 */
	public function is_active();

	/**
	 * Get module identifier
	 *
	 * @return string Unique module ID (e.g., 'woocommerce', 'fluentcart').
	 */
	public function get_module_id();

	/**
	 * Get module display name
	 *
	 * @return string Human-readable module name.
	 */
	public function get_module_name();

	/**
	 * Get protection settings array
	 *
	 * Returns settings array in WooCommerce Settings API format.
	 * Should include:
	 * - Section title
	 * - Protection checkboxes
	 * - Section end
	 *
	 * @return array Settings array or empty array if not active.
	 */
	public function get_protection_settings();

	/**
	 * Get checkbox field IDs for this module
	 *
	 * Returns array of option names that need to be saved.
	 * Used by the main settings page to know which fields to save.
	 *
	 * @return array Array of checkbox option IDs.
	 */
	public function get_checkbox_ids();
}
