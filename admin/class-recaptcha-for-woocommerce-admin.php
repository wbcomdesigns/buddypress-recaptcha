<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_Woocommerce
 * @subpackage Recaptcha_For_Woocommerce/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Recaptcha_For_Woocommerce_Admin {

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
		include plugin_dir_path( __FILE__ ) . 'includes/class-wbc-woocommerce-settings-page.php';
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
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recaptcha-for-woocommerce-admin.css', array(), $this->version, 'all' );

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
		 * defined in Recaptcha_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Recaptcha_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recaptcha-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

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
	 * Add Woo Pincode Checker Menu in admin.
	 *
	 * @since    1.0.0
	 */
	public function wpc_admin_menu() {

		/* add sub menu in wnplugin setting page */
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'wb-recaptcha' ), esc_html__( 'WB Plugins', 'wb-recaptcha' ), 'manage_options', 'wbcomplugins', array( $this, 'wpc_admin_settings_page_welcome' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'wb-recaptcha' ), esc_html__( 'General', 'wb-recaptcha' ), 'manage_options', 'wbcomplugins' );
		}

		add_submenu_page( 'wbcomplugins', esc_html__( 'WB Recaptcha', 'wb-recaptcha' ), esc_html__( 'WB Recaptcha', 'wb-recaptcha' ), 'manage_options', 'wb-recaptcha', array( $this, 'wpc_admin_settings_page' ) );
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wpc_admin_settings_page_welcome() {
		$wbc_woo_commerce_settings_page = new Wbc_WooCommerce_Settings_Page();
		$current                        = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'wpc-general';

		?>

		<div class="wrap">
			<div class="ess-admin-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'WB Recaptcha', 'wb-recaptcha' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
				<?php $this->wpc_plugin_settings_tabs_wbcom(); ?>
				<?php include 'wbcom-welcome-page.php'; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wpc_admin_settings_page() {
		$wbc_woo_commerce_settings_page = new Wbc_WooCommerce_Settings_Page();
		$current                        = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'wpc-general';

		?>

		<div class="wrap">
			<div class="ess-admin-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'WB Recaptcha', 'wb-recaptcha' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
				<?php $this->wpc_plugin_settings_tabs(); ?>
				<form method="post" id="wbrecaptcha" action="" enctype="multipart/form-data">
				<?php
				if ( $_POST ) {
					$wbc_woo_commerce_settings_page->save( $current );
				}
				$wbc_woo_commerce_settings_page->output( $current );
				?>
					<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Register all settings.
	 */
	public function wpc_add_admin_register_setting() {
		$this->plugin_settings_tabs['wpc-general']['name'] = esc_html__( 'General Settings', 'wb-recaptcha' );
		$this->plugin_settings_tabs['wpc-general']['icon'] = 'dashicons-admin-generic';
		if ( class_exists( 'WooCommerce' ) ) {
			$this->plugin_settings_tabs['signup']['name'] = esc_html__( 'Woo Registration', 'wb-recaptcha' );
			$this->plugin_settings_tabs['signup']['icon'] = 'dashicons-buddicons-buddypress-logo';

			$this->plugin_settings_tabs['login']['name'] = esc_html__( 'Woo Login', 'wb-recaptcha' );
			$this->plugin_settings_tabs['login']['icon'] = 'dashicons-wordpress';

			$this->plugin_settings_tabs['forgotpassword']['name'] = esc_html__( 'Woo Lost Password', 'wb-recaptcha' );
			$this->plugin_settings_tabs['forgotpassword']['icon'] = 'dashicons-visibility';

			$this->plugin_settings_tabs['guestcheckout']['name'] = esc_html__( 'Woo Checkout', 'wb-recaptcha' );
			$this->plugin_settings_tabs['guestcheckout']['icon'] = 'dashicons-cart';

			$this->plugin_settings_tabs['add_payment_method']['name'] = esc_html__( 'Woo Add Payment Method', 'wb-recaptcha' );
			$this->plugin_settings_tabs['add_payment_method']['icon'] = 'dashicons-money-alt';

			$this->plugin_settings_tabs['woo_review']['name'] = esc_html__( 'Woo Product Review Form', 'wb-recaptcha' );
			$this->plugin_settings_tabs['woo_review']['icon'] = 'dashicons-twitch';

			$this->plugin_settings_tabs['woo_comments']['name'] = esc_html__( 'Woo Post Comment Form', 'wb-recaptcha' );
			$this->plugin_settings_tabs['woo_comments']['icon'] = 'dashicons-twitch';
		}
		$this->plugin_settings_tabs['wp_login']['name'] = esc_html__( 'WP Login', 'wb-recaptcha' );
		$this->plugin_settings_tabs['wp_login']['icon'] = 'dashicons-wordpress';

		$this->plugin_settings_tabs['wp_register']['name'] = esc_html__( 'WP Registration', 'wb-recaptcha' );
		$this->plugin_settings_tabs['wp_register']['icon'] = 'dashicons-buddicons-buddypress-logo';

		$this->plugin_settings_tabs['wp_lostpassword']['name'] = esc_html__( 'WP Lost Password', 'wb-recaptcha' );
		$this->plugin_settings_tabs['wp_lostpassword']['icon'] = 'dashicons-visibility';

		if ( class_exists( 'BuddyPress' ) ) {
			$this->plugin_settings_tabs['bp_register']['name'] = esc_html__( 'BP Registration', 'wb-recaptcha' );
			$this->plugin_settings_tabs['bp_register']['icon'] = 'dashicons-buddicons-buddypress-logo';
		}
		if ( class_exists( 'bbPress' ) ) {
			$this->plugin_settings_tabs['bb_press_topic']['name'] = esc_html__( 'BB Press Topic', 'wb-recaptcha' );
			$this->plugin_settings_tabs['bb_press_topic']['icon'] = 'dashicons-editor-paste-text';
		}

		register_setting( 'wpc_general_settings', 'wpc_general_settings' );
		add_settings_section( 'wpc-general', ' ', array( $this, 'wpc_general_settings_content' ), 'wpc-general' );
	}
	/**
	 * Add tab in setting page
	 */
	public function wpc_plugin_settings_tabs() {
		$current = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'wpc-general';

		$tab_html = '<div class="wbcom-tabs-section"><h2 class="nav-tab-wrapper">';

		foreach ( $this->plugin_settings_tabs as $edd_tab => $tab_name ) {
			$class     = ( $edd_tab === $current ) ? 'nav-tab-active' : '';
			$page      = 'wb-recaptcha';
			$tab_html .= '<a id="' . $edd_tab . '" class="nav-tab ' . $class . '" href="admin.php?page=' . $page . '&tab=' . $edd_tab . '"><span class="dashicons ' . $tab_name['icon'] . '"></span>&nbsp;' . $tab_name['name'] . '</a>';
		}
		$tab_html .= '</h2></div>';
		echo $tab_html;
	}

	/**
	 * Template Class Doc Comment
	 *
	 * Template Class.
	 */
	public function wpc_plugin_settings_tabs_wbcom() {
		$current = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'wpc-general';

		$tab_html                                    = '<div class="wbcom-tabs-section"><h2 class="nav-tab-wrapper">';
		$plugin_settings_tabs['wpc-general']['name'] = esc_html__( 'General', 'wbcomplugins' );
		$plugin_settings_tabs['wpc-general']['icon'] = 'dashicons-admin-home';

		foreach ( $plugin_settings_tabs as $edd_tab => $tab_name ) {
			$class     = ( $edd_tab === $current ) ? 'nav-tab-active' : '';
			$page      = 'wbcomplugins';
			$tab_html .= '<a id="' . $edd_tab . '" class="nav-tab ' . $class . '" href="admin.php?page=' . $page . '&tab=' . $edd_tab . '"><span class="dashicons ' . $tab_name['icon'] . '"></span>&nbsp;' . $tab_name['name'] . '</a>';
		}
		$tab_html .= '</h2></div>';
		echo $tab_html;
	}

	/**
	 * Get general settings html.
	 */
	public function wpc_general_settings_content() {
		include plugin_dir_path( __FILE__ ) . 'includes/class-wbc-woocommerce-settings-page.php';
	}


}
