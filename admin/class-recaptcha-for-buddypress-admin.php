<?php
/**
 * Legacy admin service class — settings-engine loader (post-migration).
 *
 * As of 2.1.0 this class no longer renders any admin UI. The card-panel
 * admin (BPRC_Admin, includes/admin/) owns the menu, enqueue, page render,
 * notice suppression, and hub. This class is RETAINED only because its
 * constructor:
 *
 *   1. Loads WBC_BuddyPress_Settings_Page — the wbc_save()/wbc_output()
 *      engine that BPRC_Admin delegates to (settings save mechanism is
 *      unchanged: custom POST handler keyed on the
 *      bp_recaptcha_submit_fields_nonce field).
 *   2. Initialises WBC_Settings_Integration (option-key migrations).
 *
 * The legacy menu/render/enqueue methods were removed in 2.1.0 along with
 * the admin/wbcom/ wrapper they depended on. See
 * references/wbcom-wrapper-migration.md Part 3.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin
 */

/**
 * Legacy admin service class.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_BuddyPress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Load the settings engine + settings-migration integration.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name The name of this plugin.
	 * @param    string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// The settings engine BPRC_Admin delegates save/output to.
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-wbc-buddypress-settings-page.php';

		// Option-key migrations (underscore/hyphen normalisation).
		if ( class_exists( 'WBC_Settings_Integration' ) ) {
			WBC_Settings_Integration::init();
		}
	}
}
