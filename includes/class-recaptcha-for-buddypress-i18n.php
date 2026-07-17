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
	 * Just-in-time loading only resolves language packs that exist on
	 * translate.wordpress.org. This plugin ships its own translations in
	 * languages/, which WordPress never reads on its own, so the domain must
	 * be registered here. load_plugin_textdomain() checks the WordPress.org
	 * pack first and falls back to the bundled file, so both paths work.
	 *
	 * Must run on init (not earlier): loading a text domain before init
	 * triggers _load_textdomain_just_in_time on WordPress 6.7+.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'buddypress-recaptcha',
			false,
			basename( RFB_PLUGIN_PATH ) . '/languages/'
		);
	}
}
