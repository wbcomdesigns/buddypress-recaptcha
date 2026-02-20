<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_BuddyPress_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * Note: Since WordPress 6.7, translations are loaded just-in-time.
	 * The load_plugin_textdomain() call has been removed to comply with
	 * Plugin Check requirements.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		// Intentionally left empty.
		// WordPress 6.7+ handles translation loading automatically.
	}
}
