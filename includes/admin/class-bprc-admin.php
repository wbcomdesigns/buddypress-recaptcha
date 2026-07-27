<?php
/**
 * BPRC Admin: menu, enqueue, page rendering for the card-panel admin.
 *
 * Replaces the legacy Wbcom wrapper chrome (admin/wbcom/*). Follows the
 * wp-plugin-development skill Part 6 card-panel pattern and the
 * references/wbcom-wrapper-migration.md playbook (Parts 5, 6, 7, 16).
 *
 * UX-only rebuild. Everything below is preserved byte-for-byte from the
 * legacy admin:
 *   - Menu slug ............... buddypress-recaptcha
 *   - Tab slugs ............... rfw-welcome / rfw-general / protection / advanced
 *   - Save nonce field ........ bp_recaptcha_submit_fields_nonce
 *   - Save nonce action ....... bp_recaptcha_submit_nonce
 *   - Save mechanism .......... WBC_BuddyPress_Settings_Page::wbc_save( $tab )
 *   - Field output ............ WBC_BuddyPress_Settings_Page::wbc_output( $tab )
 *   - Settings-error group .... wbc_recaptcha_messages
 *   - Option keys ............. wbc_* (untouched)
 *   - EDD SL SDK .............. keyless registration in the main file (untouched)
 * Only the admin chrome changed (card-panel shell instead of the wrapper).
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage Recaptcha_For_BuddyPress/admin
 * @since      2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class BPRC_Admin
 *
 * @since 2.1.0
 */
class BPRC_Admin {

	/**
	 * Menu slug — kept identical to the legacy admin so bookmarks/links
	 * (admin.php?page=buddypress-recaptcha) keep working.
	 */
	const MENU_SLUG = 'buddypress-recaptcha';

	/**
	 * Save form nonce field + action — preserved from the legacy admin so
	 * an in-flight save posted from either UI stays valid.
	 */
	const NONCE_FIELD  = 'bp_recaptcha_submit_fields_nonce';
	const NONCE_ACTION = 'bp_recaptcha_submit_nonce';

	/**
	 * Settings-error group used by add_settings_error()/settings_errors().
	 */
	const ERROR_GROUP = 'wbc_recaptcha_messages';

	/**
	 * Tabs rendered inside the single admin page.
	 *
	 * The slugs map 1:1 to the legacy WBC_BuddyPress_Settings_Page sections
	 * (rfw-welcome / rfw-general / protection / advanced). Do not rename —
	 * wbc_save()/wbc_output() switch on these strings.
	 *
	 * @return array<string, array{label:string, icon:string, group:string, form:bool}>
	 */
	public static function get_tabs() {
		$tabs = array(
			'rfw-welcome' => array(
				'label' => __( 'Overview', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-chart-bar',
				'group' => 'main',
				'form'  => false,
			),
			'rfw-general' => array(
				'label' => __( 'Quick Setup', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-admin-settings',
				'group' => 'settings',
				'form'  => true,
			),
			'protection'  => array(
				'label' => __( 'Protection', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-shield',
				'group' => 'settings',
				'form'  => true,
			),
			'advanced'    => array(
				'label' => __( 'Advanced', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-admin-generic',
				'group' => 'settings',
				'form'  => true,
			),
			'updates'     => array(
				'label' => __( 'Updates', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-update',
				'group' => 'account',
				'form'  => false,
			),
			'discover'    => array(
				'label' => __( 'Discover', 'buddypress-recaptcha' ),
				'icon'  => 'dashicons-lightbulb',
				'group' => 'account',
				'form'  => false,
			),
		);

		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- shared Wbcom admin filter prefix.
		return apply_filters( 'bprc_admin_tabs', $tabs );
	}

	/**
	 * Bootstrap hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		// Priority 999 runs after every other plugin has registered. Reclaims
		// the hub's landing render when a legacy wbcom-wrapper plugin was the
		// first to register wbcomplugins. See playbook Part 16.
		add_action( 'admin_menu', array( $this, 'takeover_hub_landing' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'in_admin_header', array( $this, 'suppress_foreign_notices' ), 1 );
	}

	/**
	 * Attach as a single submenu under the shared WB Plugins hub.
	 */
	public function add_menu() {
		$cap = 'manage_options';

		// First Wbcom plugin to load creates the shared WB Plugins hub.
		// Peer plugins are auto-discovered via $GLOBALS['submenu']['wbcomplugins'].
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page(
				esc_html__( 'WB Plugins', 'buddypress-recaptcha' ),
				esc_html__( 'WB Plugins', 'buddypress-recaptcha' ),
				$cap,
				'wbcomplugins',
				array( $this, 'render_hub' ),
				'dashicons-lightbulb',
				59
			);
		}

		add_submenu_page(
			'wbcomplugins',
			esc_html__( 'Wbcom CAPTCHA Manager', 'buddypress-recaptcha' ),
			esc_html__( 'CAPTCHA', 'buddypress-recaptcha' ),
			$cap,
			self::MENU_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Force the shared hub's landing to render our clean card-panel
	 * dashboard, overriding whatever a legacy wbcom-wrapper plugin may
	 * have registered first. See playbook Part 16.
	 */
	public function takeover_hub_landing() {
		global $admin_page_hooks;
		if ( empty( $admin_page_hooks['wbcomplugins'] ) ) {
			return;
		}
		remove_all_actions( 'toplevel_page_wbcomplugins' );
		add_action( 'toplevel_page_wbcomplugins', array( $this, 'render_hub' ) );
	}

	/**
	 * Enqueue admin assets only on our screen + the shared hub landing.
	 *
	 * Loads the card-panel chrome (assets/css/admin.css + assets/js/admin.js)
	 * and re-uses the retained content stylesheets/scripts that drive the
	 * settings fields themselves.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_assets( $hook_suffix ) {
		unset( $hook_suffix ); // We inspect the screen object instead.
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}

		$version = defined( 'RFB_PLUGIN_VERSION' ) ? RFB_PLUGIN_VERSION : '1.0.0';

		// Card-panel chrome.
		wp_enqueue_style(
			'bprc-admin',
			RFB_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$version
		);

		wp_enqueue_script(
			'bprc-admin',
			RFB_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$version,
			true
		);

		wp_localize_script(
			'bprc-admin',
			'bprcAdmin',
			array(
				'i18n' => array(
					'confirmDanger'   => __( 'Are you sure? This cannot be undone.', 'buddypress-recaptcha' ),
					'confirmContinue' => __( 'Continue', 'buddypress-recaptcha' ),
					'confirmCancel'   => __( 'Cancel', 'buddypress-recaptcha' ),
				),
			)
		);

		// Retained content styles/scripts that drive the settings fields
		// (service show/hide, ALTCHA key gen, copy-key, password toggle).
		// These are plugin content, not wrapper chrome — kept as-is.
		wp_enqueue_style(
			'recaptcha-for-buddypress-admin',
			RFB_PLUGIN_URL . 'admin/css/recaptcha-for-buddypress-admin.css',
			array( 'bprc-admin' ),
			$version
		);

		wp_enqueue_script(
			'recaptcha-for-buddypress-admin',
			RFB_PLUGIN_URL . 'admin/js/recaptcha-for-buddypress-admin.js',
			array( 'jquery' ),
			$version,
			true
		);
		wp_localize_script(
			'recaptcha-for-buddypress-admin',
			'wbcAdminFields',
			array(
				'strings' => array(
					/* translators: %s: CAPTCHA provider name, e.g. "Google reCAPTCHA v2". */
					'siteKeyRequired'   => __( '%s Site Key is required', 'buddypress-recaptcha' ),
					/* translators: %s: CAPTCHA provider name, e.g. "Google reCAPTCHA v2". */
					'secretKeyRequired' => __( '%s Secret Key is required', 'buddypress-recaptcha' ),
					'providerV2'        => __( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ),
					'providerV3'        => __( 'Google reCAPTCHA v3', 'buddypress-recaptcha' ),
					'providerTurnstile' => 'Cloudflare Turnstile',
					'fixErrors'         => __( 'Please fix the following errors:', 'buddypress-recaptcha' ),
					'getKeysHere'       => __( 'Get your keys here', 'buddypress-recaptcha' ),
					'keyGenerated'      => __( 'Key Generated!', 'buddypress-recaptcha' ),
					'toggleVisibility'  => __( 'Toggle visibility', 'buddypress-recaptcha' ),
					'hideSecretKey'     => __( 'Hide Secret Key', 'buddypress-recaptcha' ),
					'showSecretKey'     => __( 'Show Secret Key', 'buddypress-recaptcha' ),
					'copyToClipboard'   => __( 'Copy to Clipboard', 'buddypress-recaptcha' ),
					'copied'            => __( 'Copied!', 'buddypress-recaptcha' ),
				),
			)
		);
		wp_enqueue_script(
			'recaptcha-for-buddypress-admin-dynamic',
			RFB_PLUGIN_URL . 'admin/js/wbc-admin-dynamic.js',
			array( 'jquery' ),
			$version,
			true
		);
		wp_localize_script(
			'recaptcha-for-buddypress-admin-dynamic',
			'wbc_admin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'strings'  => array(
					'key_generated'    => __( 'Random key generated successfully!', 'buddypress-recaptcha' ),
					'dismiss_notice'   => __( 'Dismiss this notice.', 'buddypress-recaptcha' ),
					// Per-service help card: title + one-line explanation.
					'v2_title'         => __( 'Google reCAPTCHA v2 Configuration', 'buddypress-recaptcha' ),
					'v2_help'          => __( 'The classic checkbox CAPTCHA. Users must click "I\'m not a robot".', 'buddypress-recaptcha' ),
					'v3_title'         => __( 'Google reCAPTCHA v3 Configuration', 'buddypress-recaptcha' ),
					'v3_help'          => __( 'Invisible verification using risk analysis score.', 'buddypress-recaptcha' ),
					'turnstile_title'  => __( 'Cloudflare Turnstile Configuration', 'buddypress-recaptcha' ),
					'turnstile_help'   => __( 'Privacy-first CAPTCHA alternative from Cloudflare.', 'buddypress-recaptcha' ),
					'hcaptcha_title'   => __( 'hCaptcha Configuration', 'buddypress-recaptcha' ),
					'hcaptcha_help'    => __( 'Privacy-focused CAPTCHA that rewards websites.', 'buddypress-recaptcha' ),
					'altcha_title'     => __( 'ALTCHA Configuration', 'buddypress-recaptcha' ),
					'altcha_help'      => __( 'Self-hosted proof-of-work challenge. No external API required.', 'buddypress-recaptcha' ),
				),
			)
		);
	}

	/**
	 * Suppress 3rd-party admin notices on our screen.
	 */
	public function suppress_foreign_notices() {
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * True if the current screen is the plugin's admin page OR the shared
	 * hub landing (our callback owns the hub render too).
	 *
	 * @param WP_Screen $screen Current screen.
	 * @return bool
	 */
	private function is_our_screen( $screen ) {
		if ( empty( $screen->id ) ) {
			return false;
		}
		return (bool) preg_match( '/_page_' . preg_quote( self::MENU_SLUG, '/' ) . '$/', $screen->id )
			|| 'toplevel_page_wbcomplugins' === $screen->id;
	}

	/**
	 * Render the shared WB Plugins hub landing page.
	 */
	public function render_hub() {
		$view = RFB_PLUGIN_PATH . 'includes/admin/views/hub.php';
		if ( file_exists( $view ) ) {
			include $view;
		}
	}

	/**
	 * Render the single admin page inside the card-panel shell.
	 *
	 * The save path is identical to the legacy admin: a tab's form posts
	 * the bp_recaptcha_submit_fields_nonce field, we verify it and call
	 * WBC_BuddyPress_Settings_Page::wbc_save( $tab ); the same instance's
	 * wbc_output( $tab ) renders the fields. No option key, nonce, field
	 * name, or save mechanism changed.
	 */
	public function render_page() {
		$bprc_tabs = self::get_tabs();
		$tab_slugs = array_keys( $bprc_tabs );

		// Tab switching is a read-only GET that only changes which view
		// renders; it mutates nothing, so no nonce is needed here.
		$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $tab_slugs[0]; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $bprc_tabs[ $active ] ) ) {
			$active = $tab_slugs[0];
		}

		$page_url   = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		$is_form    = ! empty( $bprc_tabs[ $active ]['form'] );
		$settings   = class_exists( 'WBC_BuddyPress_Settings_Page' ) ? new WBC_BuddyPress_Settings_Page() : null;
		$plugin_ver = defined( 'RFB_PLUGIN_VERSION' ) ? RFB_PLUGIN_VERSION : '';

		// Process the save exactly as the legacy admin did — same nonce
		// field, same nonce action, same wbc_save() call. The menu page that
		// renders this is registered with manage_options, but verify the cap
		// explicitly here too so the save can never run for an under-privileged
		// user even if render_page() is ever reached another way.
		if ( $is_form && $settings && current_user_can( 'manage_options' ) ) {
			$nonce = isset( $_POST[ self::NONCE_FIELD ] )
				? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) )
				: '';
			if ( isset( $_POST[ self::NONCE_FIELD ] ) && wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
				$settings->wbc_save( $active );
			}
		}

		// Map the active tab to its content view partial.
		$view_map = array(
			'rfw-welcome' => 'overview',
			'updates'     => 'updates',
			'discover'    => 'discover',
			// rfw-general / protection / advanced share the settings form view.
		);
		$view      = isset( $view_map[ $active ] ) ? $view_map[ $active ] : 'settings-form';
		$view_path = RFB_PLUGIN_PATH . 'includes/admin/views/' . $view . '.php';
		$shell     = RFB_PLUGIN_PATH . 'includes/admin/views/shell.php';

		include $shell;
	}
}
