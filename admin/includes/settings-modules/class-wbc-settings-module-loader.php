<?php
/**
 * Settings Module Loader
 *
 * Loads and manages all settings modules.
 * Provides centralized access to all integration settings.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Settings Module Loader Class
 *
 * Singleton class that:
 * - Loads all settings module files
 * - Instantiates each module
 * - Collects settings from active modules only
 * - Provides aggregated settings and checkbox IDs
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_Settings_Module_Loader {

	/**
	 * Singleton instance
	 *
	 * @var WBC_Settings_Module_Loader
	 */
	private static $instance = null;

	/**
	 * Registered modules
	 *
	 * @var array
	 */
	private $modules = array();

	/**
	 * Get singleton instance
	 *
	 * @return WBC_Settings_Module_Loader
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_modules();
	}

	/**
	 * Load all module dependencies
	 *
	 * @return void
	 */
	private function load_dependencies() {
		$modules_dir = plugin_dir_path( __FILE__ );

		// Load interface
		require_once $modules_dir . 'interface-wbc-settings-module.php';

		// Load abstract base class
		require_once $modules_dir . 'abstract-wbc-settings-module.php';

		// Load all module implementations
		require_once $modules_dir . 'class-wbc-wordpress-settings.php';
		require_once $modules_dir . 'class-wbc-woocommerce-settings.php';
		require_once $modules_dir . 'class-wbc-buddypress-settings.php';
		require_once $modules_dir . 'class-wbc-bbpress-settings.php';
		require_once $modules_dir . 'class-wbc-cf7-settings.php';
		require_once $modules_dir . 'class-wbc-wpforms-settings.php';
		require_once $modules_dir . 'class-wbc-gravityforms-settings.php';
		require_once $modules_dir . 'class-wbc-ninjaforms-settings.php';
		require_once $modules_dir . 'class-wbc-forminator-settings.php';
		require_once $modules_dir . 'class-wbc-elementorpro-settings.php';
		require_once $modules_dir . 'class-wbc-divi-settings.php';
		require_once $modules_dir . 'class-wbc-edd-settings.php';
		require_once $modules_dir . 'class-wbc-memberpress-settings.php';
		require_once $modules_dir . 'class-wbc-ultimatemember-settings.php';
	}

	/**
	 * Register all settings modules
	 *
	 * @return void
	 */
	private function register_modules() {
		// WordPress Core (always active)
		$this->register_module( new WBC_WordPress_Settings() );

		// WooCommerce (conditional)
		$this->register_module( new WBC_WooCommerce_Settings() );

		// BuddyPress (conditional)
		$this->register_module( new WBC_BuddyPress_Settings() );

		// bbPress (conditional)
		$this->register_module( new WBC_bbPress_Settings() );

		// Contact Form 7 (conditional)
		$this->register_module( new WBC_CF7_Settings() );

		// WPForms (conditional)
		$this->register_module( new WBC_WPForms_Settings() );

		// Gravity Forms (conditional)
		$this->register_module( new WBC_GravityForms_Settings() );

		// Ninja Forms (conditional)
		$this->register_module( new WBC_NinjaForms_Settings() );

		// Forminator (conditional)
		$this->register_module( new WBC_Forminator_Settings() );

		// Elementor Pro (conditional)
		$this->register_module( new WBC_ElementorPro_Settings() );

		// Divi Builder (conditional)
		$this->register_module( new WBC_Divi_Settings() );

		// Easy Digital Downloads (conditional)
		$this->register_module( new WBC_EDD_Settings() );

		// MemberPress (conditional)
		$this->register_module( new WBC_MemberPress_Settings() );

		// Ultimate Member (conditional)
		$this->register_module( new WBC_UltimateMember_Settings() );

		/**
		 * Allow third-party plugins to register custom settings modules
		 *
		 * @since 2.1.0
		 *
		 * @param WBC_Settings_Module_Loader $loader The settings module loader instance.
		 */
		do_action( 'wbc_recaptcha_register_settings_modules', $this );
	}

	/**
	 * Register a settings module
	 *
	 * @param WBC_Settings_Module_Interface $module The module to register.
	 * @return void
	 */
	public function register_module( WBC_Settings_Module_Interface $module ) {
		$this->modules[ $module->get_module_id() ] = $module;
	}

	/**
	 * Get all registered modules
	 *
	 * @return array Array of module instances.
	 */
	public function get_modules() {
		return $this->modules;
	}

	/**
	 * Get active modules only
	 *
	 * @return array Array of active module instances.
	 */
	public function get_active_modules() {
		return array_filter(
			$this->modules,
			function( $module ) {
				return $module->is_active();
			}
		);
	}

	/**
	 * Get all protection settings from active modules
	 *
	 * Combines settings from all active modules into a single array
	 * suitable for the WooCommerce Settings API.
	 *
	 * @return array Combined settings array.
	 */
	public function get_all_protection_settings() {
		$all_settings = array();

		foreach ( $this->get_active_modules() as $module ) {
			$module_settings = $module->get_protection_settings();
			if ( ! empty( $module_settings ) ) {
				$all_settings = array_merge( $all_settings, $module_settings );
			}
		}

		/**
		 * Filter the combined protection settings
		 *
		 * @since 2.1.0
		 *
		 * @param array $all_settings Combined settings from all active modules.
		 */
		return apply_filters( 'wbc_recaptcha_all_protection_settings', $all_settings );
	}

	/**
	 * Get all checkbox IDs from active modules
	 *
	 * Returns array of all checkbox option names that need to be saved.
	 *
	 * @return array Array of checkbox field IDs.
	 */
	public function get_all_checkbox_ids() {
		$all_checkbox_ids = array();

		foreach ( $this->get_active_modules() as $module ) {
			$module_ids       = $module->get_checkbox_ids();
			$all_checkbox_ids = array_merge( $all_checkbox_ids, $module_ids );
		}

		/**
		 * Filter the combined checkbox IDs
		 *
		 * @since 2.1.0
		 *
		 * @param array $all_checkbox_ids Combined checkbox IDs from all active modules.
		 */
		return apply_filters( 'wbc_recaptcha_all_checkbox_ids', $all_checkbox_ids );
	}

	/**
	 * Get a specific module by ID
	 *
	 * @param string $module_id Module ID.
	 * @return WBC_Settings_Module_Interface|null Module instance or null if not found.
	 */
	public function get_module( $module_id ) {
		return isset( $this->modules[ $module_id ] ) ? $this->modules[ $module_id ] : null;
	}

	/**
	 * Check if a module is active
	 *
	 * @param string $module_id Module ID.
	 * @return bool True if module exists and is active, false otherwise.
	 */
	public function is_module_active( $module_id ) {
		$module = $this->get_module( $module_id );
		return $module ? $module->is_active() : false;
	}

	/**
	 * Get count of active modules
	 *
	 * @return int Number of active modules.
	 */
	public function get_active_module_count() {
		return count( $this->get_active_modules() );
	}
}

/**
 * Helper function to get settings module loader instance
 *
 * @return WBC_Settings_Module_Loader
 */
function wbc_settings_module_loader() {
	return WBC_Settings_Module_Loader::get_instance();
}
