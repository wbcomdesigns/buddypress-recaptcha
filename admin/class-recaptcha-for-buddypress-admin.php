<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
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
	 * Plugins setting tab of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_settings_tabs  Plugin's admin settings tabs.
	 */
	private $plugin_settings_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		include plugin_dir_path( __FILE__ ) . 'includes/class-wbc-buddypress-settings-page.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_BuddyPress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_BuddyPress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Only load on our plugin pages
		if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'buddypress-recaptcha' || $_GET['page'] === 'wbcomplugins' || $_GET['page'] === 'wbcom-plugins-page' || $_GET['page'] === 'wbcom-support-page' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-buddypress-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-cards', plugin_dir_url( __FILE__ ) . 'css/recaptcha-appearance-cards.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Recaptcha_For_BuddyPress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_BuddyPress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Get current page
		$screen = get_current_screen();

		// Only load on our plugin pages
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'buddypress-recaptcha' ) {
			// Main admin script
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recaptcha-for-buddypress-admin.js', array( 'jquery' ), $this->version, false );

			// Dynamic settings script for service selection
			wp_enqueue_script( $this->plugin_name . '-dynamic', plugin_dir_url( __FILE__ ) . 'js/wbc-admin-dynamic.js', array( 'jquery' ), $this->version, true );

			// Localize script for AJAX operations if needed
			wp_localize_script( $this->plugin_name . '-dynamic', 'wbc_admin', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wbc_admin_nonce' ),
				'strings'  => array(
					'testing'   => __( 'Testing connection...', 'buddypress-recaptcha' ),
					'success'   => __( 'Connection successful!', 'buddypress-recaptcha' ),
					'error'     => __( 'Connection failed. Please check your keys.', 'buddypress-recaptcha' ),
					'select'    => __( 'Please select a service first.', 'buddypress-recaptcha' ),
					'enter_keys'=> __( 'Please enter both keys.', 'buddypress-recaptcha' ),
				),
			) );
		}

	}

	/**
	 * Template Class Doc Comment
	 *
	 * @param array $settings The position of the current token
	 * Template Class.
	 */
	public function woocomm_load_custom_settings_tab( $settings ) {
		return $settings;
	}


	/**
	 * Hide all notices from the setting page.
	 *
	 * @return void
	 */
	public function wbcom_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress-recaptcha' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}

	}

	/**
	 * Add Woo Pincode Checker Menu in admin.
	 *
	 * @since    1.0.0
	 */
	public function rfw_admin_menu() {
		if ( class_exists( 'WooCommerce' ) || class_exists( 'BuddyPress' ) || class_exists( 'bbPress' ) ) {
			/* add sub menu in wnplugin setting page */
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
				add_menu_page( esc_html__( 'WB Plugins', 'buddypress-recaptcha' ), esc_html__( 'WB Plugins', 'buddypress-recaptcha' ), 'manage_options', 'wbcomplugins', array( $this, 'rfw_admin_settings_page' ), 'dashicons-lightbulb', 59 );
				add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-recaptcha' ), esc_html__( 'General', 'buddypress-recaptcha' ), 'manage_options', 'wbcomplugins' );
			}

			add_submenu_page( 'wbcomplugins', esc_html__( 'Wbcom CAPTCHA Manager', 'buddypress-recaptcha' ), esc_html__( 'CAPTCHA', 'buddypress-recaptcha' ), 'manage_options', 'buddypress-recaptcha', array( $this, 'rfw_admin_settings_page' ) );
		}
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function rfw_admin_settings_page() {
		$wbc_settings_page = new WBC_BuddyPress_Settings_Page();
		$current           = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'rfw-welcome';
		?>

		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
				</div>
			</div>
			<div class="wbcom-wrap">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'Wbcom CAPTCHA Manager', 'buddypress-recaptcha' ); ?>
							<span><?php 
							/* translators: %s: */
							printf( esc_html__( 'Version %s', 'buddypress-recaptcha' ), esc_attr( RFB_PLUGIN_VERSION ) ); 
							?></span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				<div class="wbcom-admin-settings-page">
					<?php $this->rfw_plugin_settings_tabs(); ?>
						<?php if ( 'rfw-welcome' === $current ) { ?>
							<?php include 'wbcom-welcome-page.php'; ?>
						<?php } else { ?>
						<?php
						// Process form submission
						$nonce = isset( $_POST['bp_recaptcha_submit_fields_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['bp_recaptcha_submit_fields_nonce'] ) ) : '';
						if ( isset( $_POST['bp_recaptcha_submit_fields_nonce'] ) && wp_verify_nonce( $nonce, 'bp_recaptcha_submit_nonce' ) ) {
							$wbc_settings_page->wbc_save( $current );
						}

						// Display success/error messages
						settings_errors( 'wbc_recaptcha_messages' );
						?>
						<div class="wbcom-tab-content rfw-tab-content">
							<form method="post" id="wb-recaptcha" action="" enctype="multipart/form-data">
								<?php
								$wbc_settings_page->wbc_output( $current );
								?>
								<p class="submit">
									<?php wp_nonce_field( 'bp_recaptcha_submit_nonce', 'bp_recaptcha_submit_fields_nonce' ); ?>
									<?php
									$button_text = ( 'rfw-general' === $current ) ? __( 'Save Selection', 'buddypress-recaptcha' ) : __( 'Save Changes', 'buddypress-recaptcha' );
									$button_class = ( 'rfw-general' === $current ) ? 'button-primary button-hero' : 'button-primary';
									?>
									<button name="save" class="button <?php echo esc_attr( $button_class ); ?>" type="submit" value="Save changes"><?php echo esc_html( $button_text ); ?></button>
								</p>
							</form>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Register all settings.
	 */
	public function rfw_add_admin_register_setting() {
		// Simplified tab structure - only 4 tabs instead of 7+
		$this->plugin_settings_tabs['rfw-welcome']['name'] = esc_html__( 'Welcome', 'buddypress-recaptcha' );
		$this->plugin_settings_tabs['rfw-general']['name'] = esc_html__( 'Quick Setup', 'buddypress-recaptcha' );
		$this->plugin_settings_tabs['protection']['name'] = esc_html__( 'Protection', 'buddypress-recaptcha' );
		$this->plugin_settings_tabs['advanced']['name'] = esc_html__( 'Advanced', 'buddypress-recaptcha' );
	}
	/**
	 * Add tab in setting page
	 */
	public function rfw_plugin_settings_tabs() {
		$current = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'rfw-welcome';

		$tab_html = '<div class="wbcom-tabs-section rfw-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';

		foreach ( $this->plugin_settings_tabs as $edd_tab => $tab_name ) {
			$class     = ( $edd_tab === $current ) ? 'nav-tab-active' : '';
			$page      = 'buddypress-recaptcha';
			$tab_html .= '<li><a id="' . $edd_tab . '" class="nav-tab ' . $class . '" href="admin.php?page=' . $page . '&tab=' . $edd_tab . '">' . $tab_name['name'] . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo wp_kses_post( $tab_html );
	}



}
