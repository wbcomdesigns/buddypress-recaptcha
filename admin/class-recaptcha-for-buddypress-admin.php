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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-buddypress-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recaptcha-for-buddypress-admin.js', array( 'jquery' ), $this->version, false );

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

			add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress reCaptcha', 'buddypress-recaptcha' ), esc_html__( 'BuddyPress reCaptcha', 'buddypress-recaptcha' ), 'manage_options', 'buddypress-recaptcha', array( $this, 'rfw_admin_settings_page' ) );
		}
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function rfw_admin_settings_page() {
		$wbc_woo_commerce_settings_page = new Wbc_WooCommerce_Settings_Page();
		$current                        = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'rfw-welcome';
		?>

		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
					<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/" target="_blank">
						<img src="<?php echo esc_url( RFB_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
					</a>
				</div>
			</div>
			<div class="wbcom-wrap">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'BuddyPress reCaptcha', 'buddypress-recaptcha' ); ?>
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
						<div class="wbcom-tab-content rfw-tab-content">
							<form method="post" id="wb-recaptcha" action="" enctype="multipart/form-data">
								<?php
								$nonce = isset( $_POST['bp_recaptcha_submit_fields_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['bp_recaptcha_submit_fields_nonce'] ) ) : '';
								if ( isset( $_POST['bp_recaptcha_submit_fields_nonce'] ) && wp_verify_nonce( $nonce, 'bp_recaptcha_submit_nonce' ) ) {
									if ( $_POST ) {
										$wbc_woo_commerce_settings_page->save( $current );
									}
								}
								$wbc_woo_commerce_settings_page->output( $current );
								?>
								<p class="submit">
									<?php wp_nonce_field( 'bp_recaptcha_submit_nonce', 'bp_recaptcha_submit_fields_nonce' ); ?>
									<button name="save" class="button button-primary" type="submit" value="Save changes"><?php esc_html_e( 'Save changes', 'buddypress-recaptcha' ); ?></button>
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
		$this->plugin_settings_tabs['rfw-welcome']['name'] = esc_html__( 'Welcome', 'buddypress-recaptcha' );

		$this->plugin_settings_tabs['rfw-general']['name'] = esc_html__( 'General', 'buddypress-recaptcha' );

		$this->plugin_settings_tabs['wp_login']['name'] = esc_html__( 'WP Login', 'buddypress-recaptcha' );

		$this->plugin_settings_tabs['wp_register']['name'] = esc_html__( 'WP Registration', 'buddypress-recaptcha' );

		$this->plugin_settings_tabs['wp_lostpassword']['name'] = esc_html__( 'WP Lost Password', 'buddypress-recaptcha' );

		$this->plugin_settings_tabs['woo_comments']['name'] = esc_html__( 'Post Comment Form', 'buddypress-recaptcha' );

		if ( class_exists( 'BuddyPress' ) ) {
			$this->plugin_settings_tabs['bp_register']['name'] = esc_html__( 'BP Registration', 'buddypress-recaptcha' );
		}

		if ( class_exists( 'bbPress' ) ) {
			$this->plugin_settings_tabs['bb_press_topic']['name'] = esc_html__( 'bbPress Topic', 'buddypress-recaptcha' );

			$this->plugin_settings_tabs['bb_press_reply']['name'] = esc_html__( 'bbPress Reply', 'buddypress-recaptcha' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$this->plugin_settings_tabs['signup']['name'] = esc_html__( 'Woo Registration', 'buddypress-recaptcha' );

			$this->plugin_settings_tabs['login']['name'] = esc_html__( 'Woo Login', 'buddypress-recaptcha' );

			$this->plugin_settings_tabs['forgotpassword']['name'] = esc_html__( 'Woo Lost Password', 'buddypress-recaptcha' );

			$this->plugin_settings_tabs['guestcheckout']['name'] = esc_html__( 'Woo Checkout', 'buddypress-recaptcha' );

		}

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

	/**
	 * Get general settings html.
	 */
	public function rfw_general_settings_content() {
		include plugin_dir_path( __FILE__ ) . 'includes/class-wbc-buddypress-settings-page.php';
	}


}
