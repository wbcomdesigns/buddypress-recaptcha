<?php
/**
 * Setup Wizard for BuddyPress reCAPTCHA
 * Makes initial configuration simple and intuitive
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup Wizard Class
 */
class WBC_Setup_Wizard {

	/**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the wizard
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( apply_filters( 'wbc_enable_setup_wizard', true ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}
	}

	/**
	 * Add admin menus/screens
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wbc-setup', '' );
	}

	/**
	 * Show the setup wizard
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'wbc-setup' !== $_GET['page'] ) {
			return;
		}

		$this->steps = array(
			'welcome' => array(
				'name'    => __( 'Welcome', 'buddypress-recaptcha' ),
				'view'    => array( $this, 'wbc_setup_welcome' ),
				'handler' => '',
			),
			'service' => array(
				'name'    => __( 'Choose Service', 'buddypress-recaptcha' ),
				'view'    => array( $this, 'wbc_setup_service' ),
				'handler' => array( $this, 'wbc_setup_service_save' ),
			),
			'keys' => array(
				'name'    => __( 'API Keys', 'buddypress-recaptcha' ),
				'view'    => array( $this, 'wbc_setup_keys' ),
				'handler' => array( $this, 'wbc_setup_keys_save' ),
			),
			'forms' => array(
				'name'    => __( 'Protect Forms', 'buddypress-recaptcha' ),
				'view'    => array( $this, 'wbc_setup_forms' ),
				'handler' => array( $this, 'wbc_setup_forms_save' ),
			),
			'complete' => array(
				'name'    => __( 'Complete!', 'buddypress-recaptcha' ),
				'view'    => array( $this, 'wbc_setup_complete' ),
				'handler' => '',
			),
		);

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		// Save handler
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'] );
		}

		// Enqueue scripts
		wp_enqueue_style( 'wbc-setup', plugin_dir_url( __FILE__ ) . 'css/setup-wizard.css', array(), RFB_PLUGIN_VERSION );
		wp_enqueue_script( 'wbc-setup', plugin_dir_url( __FILE__ ) . 'js/setup-wizard.js', array( 'jquery' ), RFB_PLUGIN_VERSION, true );

		// Localize script
		wp_localize_script( 'wbc-setup', 'wbc_setup', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'wbc-setup' ),
			'testing'  => __( 'Testing connection...', 'buddypress-recaptcha' ),
			'success'  => __( 'Connection successful!', 'buddypress-recaptcha' ),
			'error'    => __( 'Connection failed. Please check your keys.', 'buddypress-recaptcha' ),
		) );

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get next step link
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$keys = array_keys( $this->steps );
			$step = $keys[ array_search( $this->step, $keys, true ) + 1 ];
		}
		return add_query_arg( 'step', $step, remove_query_arg( 'activate_error' ) );
	}

	/**
	 * Setup Wizard Header
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'BuddyPress reCAPTCHA &rsaquo; Setup Wizard', 'buddypress-recaptcha' ); ?></title>
			<?php wp_print_scripts( 'wbc-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<style>
				body {
					margin: 0;
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					min-height: 100vh;
					display: flex;
					align-items: center;
					justify-content: center;
				}
				.wbc-setup-wizard {
					background: white;
					border-radius: 12px;
					box-shadow: 0 20px 60px rgba(0,0,0,0.15);
					max-width: 750px;
					width: 90%;
					padding: 0;
					overflow: hidden;
					animation: slideUp 0.5s ease;
				}
				@keyframes slideUp {
					from { opacity: 0; transform: translateY(20px); }
					to { opacity: 1; transform: translateY(0); }
				}
				.wbc-setup-header {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
					padding: 40px;
					text-align: center;
				}
				.wbc-setup-header h1 {
					margin: 0;
					font-size: 28px;
					font-weight: 600;
				}
				.wbc-setup-header p {
					margin: 10px 0 0;
					opacity: 0.9;
					font-size: 16px;
				}
				.wbc-setup-steps {
					display: flex;
					padding: 30px 40px;
					border-bottom: 1px solid #e5e7eb;
					background: #f9fafb;
				}
				.wbc-setup-step {
					flex: 1;
					text-align: center;
					position: relative;
					color: #9ca3af;
				}
				.wbc-setup-step:not(:last-child):after {
					content: '';
					position: absolute;
					top: 15px;
					right: -50%;
					width: 100%;
					height: 2px;
					background: #e5e7eb;
				}
				.wbc-setup-step.active:not(:last-child):after,
				.wbc-setup-step.done:not(:last-child):after {
					background: #667eea;
				}
				.wbc-setup-step-number {
					width: 30px;
					height: 30px;
					border-radius: 50%;
					background: #e5e7eb;
					color: white;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					font-weight: 600;
					margin-bottom: 8px;
					position: relative;
					z-index: 1;
				}
				.wbc-setup-step.active .wbc-setup-step-number {
					background: #667eea;
				}
				.wbc-setup-step.done .wbc-setup-step-number {
					background: #10b981;
				}
				.wbc-setup-step.done .wbc-setup-step-number:before {
					content: '✓';
				}
				.wbc-setup-step-name {
					display: block;
					font-size: 14px;
				}
				.wbc-setup-step.active .wbc-setup-step-name,
				.wbc-setup-step.done .wbc-setup-step-name {
					color: #374151;
					font-weight: 500;
				}
				.wbc-setup-content {
					padding: 40px;
				}
				.wbc-setup-content h2 {
					margin: 0 0 20px;
					font-size: 24px;
					color: #111827;
				}
				.wbc-setup-content p {
					color: #6b7280;
					font-size: 16px;
					line-height: 1.6;
					margin: 0 0 30px;
				}
				.wbc-service-cards {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
					gap: 20px;
					margin: 30px 0;
				}
				.wbc-service-card {
					border: 2px solid #e5e7eb;
					border-radius: 8px;
					padding: 20px;
					text-align: center;
					cursor: pointer;
					transition: all 0.3s ease;
					position: relative;
				}
				.wbc-service-card:hover {
					border-color: #667eea;
					transform: translateY(-2px);
					box-shadow: 0 4px 12px rgba(102,126,234,0.15);
				}
				.wbc-service-card.selected {
					border-color: #667eea;
					background: #f0f4ff;
				}
				.wbc-service-card.recommended {
					border-color: #10b981;
				}
				.wbc-service-card.recommended:before {
					content: 'Recommended';
					position: absolute;
					top: -12px;
					left: 50%;
					transform: translateX(-50%);
					background: #10b981;
					color: white;
					padding: 2px 12px;
					border-radius: 12px;
					font-size: 11px;
					font-weight: 600;
				}
				.wbc-service-icon {
					font-size: 40px;
					margin-bottom: 10px;
				}
				.wbc-service-name {
					font-weight: 600;
					color: #111827;
					margin-bottom: 5px;
				}
				.wbc-service-desc {
					font-size: 13px;
					color: #6b7280;
				}
				.wbc-form-group {
					margin-bottom: 25px;
				}
				.wbc-form-group label {
					display: block;
					margin-bottom: 8px;
					font-weight: 500;
					color: #374151;
				}
				.wbc-form-group input[type="text"],
				.wbc-form-group input[type="password"] {
					width: 100%;
					padding: 12px 16px;
					border: 1px solid #d1d5db;
					border-radius: 6px;
					font-size: 15px;
					transition: all 0.3s ease;
				}
				.wbc-form-group input:focus {
					outline: none;
					border-color: #667eea;
					box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
				}
				.wbc-form-group .description {
					font-size: 13px;
					color: #6b7280;
					margin-top: 5px;
				}
				.wbc-form-checkbox {
					display: flex;
					align-items: flex-start;
					margin-bottom: 15px;
					padding: 15px;
					border: 1px solid #e5e7eb;
					border-radius: 6px;
					transition: all 0.3s ease;
				}
				.wbc-form-checkbox:hover {
					background: #f9fafb;
				}
				.wbc-form-checkbox input[type="checkbox"] {
					margin-right: 12px;
					margin-top: 2px;
				}
				.wbc-form-checkbox label {
					flex: 1;
					margin: 0;
				}
				.wbc-form-checkbox .label-title {
					display: block;
					font-weight: 500;
					color: #111827;
					margin-bottom: 3px;
				}
				.wbc-form-checkbox .label-desc {
					font-size: 13px;
					color: #6b7280;
				}
				.wbc-setup-footer {
					padding: 30px 40px;
					background: #f9fafb;
					border-top: 1px solid #e5e7eb;
					display: flex;
					justify-content: space-between;
					align-items: center;
				}
				.button {
					padding: 12px 24px;
					border-radius: 6px;
					font-size: 15px;
					font-weight: 500;
					text-decoration: none;
					transition: all 0.3s ease;
					border: none;
					cursor: pointer;
				}
				.button-primary {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
				}
				.button-primary:hover {
					transform: translateY(-1px);
					box-shadow: 0 4px 12px rgba(102,126,234,0.25);
				}
				.button-secondary {
					background: white;
					color: #6b7280;
					border: 1px solid #d1d5db;
				}
				.button-secondary:hover {
					background: #f9fafb;
					color: #374151;
				}
				.wbc-success-icon {
					font-size: 80px;
					color: #10b981;
					text-align: center;
					margin: 20px 0;
				}
				.wbc-features {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
					gap: 20px;
					margin: 30px 0;
				}
				.wbc-feature {
					text-align: center;
					padding: 20px;
				}
				.wbc-feature-icon {
					font-size: 40px;
					margin-bottom: 10px;
					color: #667eea;
				}
				.wbc-feature-title {
					font-weight: 600;
					margin-bottom: 5px;
					color: #111827;
				}
				.wbc-feature-desc {
					font-size: 14px;
					color: #6b7280;
				}
				.wbc-alert {
					padding: 12px 16px;
					border-radius: 6px;
					margin-bottom: 20px;
				}
				.wbc-alert-info {
					background: #dbeafe;
					color: #1e40af;
					border: 1px solid #93c5fd;
				}
				.wbc-alert-success {
					background: #d1fae5;
					color: #065f46;
					border: 1px solid #6ee7b7;
				}
				.wbc-test-connection {
					display: inline-block;
					margin-left: 10px;
					padding: 6px 12px;
					background: #f3f4f6;
					color: #374151;
					border-radius: 4px;
					text-decoration: none;
					font-size: 13px;
					font-weight: 500;
					transition: all 0.3s ease;
				}
				.wbc-test-connection:hover {
					background: #e5e7eb;
				}
			</style>
		</head>
		<body class="wbc-setup">
			<div class="wbc-setup-wizard">
				<div class="wbc-setup-header">
					<h1>🛡️ BuddyPress reCAPTCHA Setup</h1>
					<p><?php esc_html_e( 'Quick and easy spam protection for your site', 'buddypress-recaptcha' ); ?></p>
				</div>
		<?php
	}

	/**
	 * Setup Wizard Footer
	 */
	public function setup_wizard_footer() {
		?>
			</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Setup Wizard Steps
	 */
	public function setup_wizard_steps() {
		$output_steps = $this->steps;
		?>
		<div class="wbc-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) :
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );
				$is_active = $step_key === $this->step;
				?>
				<div class="wbc-setup-step <?php echo $is_completed ? 'done' : ''; ?> <?php echo $is_active ? 'active' : ''; ?>">
					<span class="wbc-setup-step-number"><?php echo array_search( $step_key, array_keys( $this->steps ), true ) + 1; ?></span>
					<span class="wbc-setup-step-name"><?php echo esc_html( $step['name'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Setup Wizard Content
	 */
	public function setup_wizard_content() {
		?>
		<div class="wbc-setup-content">
			<form method="post">
				<?php
				call_user_func( $this->steps[ $this->step ]['view'] );
				wp_nonce_field( 'wbc-setup' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Welcome step
	 */
	public function wbc_setup_welcome() {
		?>
		<h2><?php esc_html_e( 'Welcome to BuddyPress reCAPTCHA! 👋', 'buddypress-recaptcha' ); ?></h2>
		<p><?php esc_html_e( 'Thank you for choosing our plugin to protect your site from spam and bots. This quick setup wizard will help you get started in just a few minutes.', 'buddypress-recaptcha' ); ?></p>

		<div class="wbc-features">
			<div class="wbc-feature">
				<div class="wbc-feature-icon">🚀</div>
				<div class="wbc-feature-title"><?php esc_html_e( 'Quick Setup', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-feature-desc"><?php esc_html_e( '2-minute configuration', 'buddypress-recaptcha' ); ?></div>
			</div>
			<div class="wbc-feature">
				<div class="wbc-feature-icon">🛡️</div>
				<div class="wbc-feature-title"><?php esc_html_e( 'Multiple Services', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-feature-desc"><?php esc_html_e( '5 captcha providers', 'buddypress-recaptcha' ); ?></div>
			</div>
			<div class="wbc-feature">
				<div class="wbc-feature-icon">🎯</div>
				<div class="wbc-feature-title"><?php esc_html_e( 'Smart Protection', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-feature-desc"><?php esc_html_e( 'Automatic form detection', 'buddypress-recaptcha' ); ?></div>
			</div>
		</div>

		<p><?php esc_html_e( "Let's begin by choosing your preferred captcha service.", 'buddypress-recaptcha' ); ?></p>

		<div class="wbc-setup-footer">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=buddypress-recaptcha' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Skip Setup', 'buddypress-recaptcha' ); ?>
			</a>
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary">
				<?php esc_html_e( 'Let\'s Go!', 'buddypress-recaptcha' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Service selection step
	 */
	public function wbc_setup_service() {
		$selected_service = get_option( 'wbc_captcha_service', 'recaptcha_v2' );
		?>
		<h2><?php esc_html_e( 'Choose Your Captcha Service', 'buddypress-recaptcha' ); ?></h2>
		<p><?php esc_html_e( 'Select the captcha service that best fits your needs. Each service has its own advantages.', 'buddypress-recaptcha' ); ?></p>

		<div class="wbc-service-cards">
			<div class="wbc-service-card recommended" data-service="recaptcha_v2">
				<input type="radio" name="wbc_captcha_service" value="recaptcha_v2" id="service_recaptcha_v2" <?php checked( $selected_service, 'recaptcha_v2' ); ?> style="display:none">
				<div class="wbc-service-icon">✅</div>
				<div class="wbc-service-name"><?php esc_html_e( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-service-desc"><?php esc_html_e( 'Classic checkbox, most reliable', 'buddypress-recaptcha' ); ?></div>
			</div>

			<div class="wbc-service-card" data-service="recaptcha_v3">
				<input type="radio" name="wbc_captcha_service" value="recaptcha_v3" id="service_recaptcha_v3" <?php checked( $selected_service, 'recaptcha_v3' ); ?> style="display:none">
				<div class="wbc-service-icon">👻</div>
				<div class="wbc-service-name"><?php esc_html_e( 'Google reCAPTCHA v3', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-service-desc"><?php esc_html_e( 'Invisible, score-based', 'buddypress-recaptcha' ); ?></div>
			</div>

			<div class="wbc-service-card" data-service="turnstile">
				<input type="radio" name="wbc_captcha_service" value="turnstile" id="service_turnstile" <?php checked( $selected_service, 'turnstile' ); ?> style="display:none">
				<div class="wbc-service-icon">☁️</div>
				<div class="wbc-service-name"><?php esc_html_e( 'Cloudflare Turnstile', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-service-desc"><?php esc_html_e( 'Privacy-friendly alternative', 'buddypress-recaptcha' ); ?></div>
			</div>

			<div class="wbc-service-card" data-service="hcaptcha">
				<input type="radio" name="wbc_captcha_service" value="hcaptcha" id="service_hcaptcha" <?php checked( $selected_service, 'hcaptcha' ); ?> style="display:none">
				<div class="wbc-service-icon">🔐</div>
				<div class="wbc-service-name"><?php esc_html_e( 'hCaptcha', 'buddypress-recaptcha' ); ?></div>
				<div class="wbc-service-desc"><?php esc_html_e( 'Privacy-focused, rewards', 'buddypress-recaptcha' ); ?></div>
			</div>
		</div>

		<div class="wbc-alert wbc-alert-info">
			<strong><?php esc_html_e( 'Not sure which to choose?', 'buddypress-recaptcha' ); ?></strong><br>
			<?php esc_html_e( 'We recommend Google reCAPTCHA v2 for the best balance of security and user experience.', 'buddypress-recaptcha' ); ?>
		</div>

		<div class="wbc-setup-footer">
			<a href="<?php echo esc_url( $this->get_next_step_link( 'welcome' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Back', 'buddypress-recaptcha' ); ?>
			</a>
			<button type="submit" class="button button-primary" name="save_step" value="1">
				<?php esc_html_e( 'Continue', 'buddypress-recaptcha' ); ?>
			</button>
		</div>

		<script>
		jQuery('.wbc-service-card').click(function() {
			jQuery('.wbc-service-card').removeClass('selected');
			jQuery(this).addClass('selected');
			jQuery(this).find('input[type="radio"]').prop('checked', true);
		});
		jQuery('.wbc-service-card input:checked').parent().addClass('selected');
		</script>
		<?php
	}

	/**
	 * Save service selection
	 */
	public function wbc_setup_service_save() {
		check_admin_referer( 'wbc-setup' );

		$service = isset( $_POST['wbc_captcha_service'] ) ? sanitize_text_field( $_POST['wbc_captcha_service'] ) : 'recaptcha_v2';
		update_option( 'wbc_captcha_service', $service );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * API Keys step
	 */
	public function wbc_setup_keys() {
		$service = get_option( 'wbc_captcha_service', 'recaptcha_v2' );
		$service_names = array(
			'recaptcha_v2' => 'Google reCAPTCHA v2',
			'recaptcha_v3' => 'Google reCAPTCHA v3',
			'turnstile' => 'Cloudflare Turnstile',
			'hcaptcha' => 'hCaptcha',
			'altcha' => 'ALTCHA',
		);
		$service_name = isset( $service_names[$service] ) ? $service_names[$service] : $service;

		$signup_urls = array(
			'recaptcha_v2' => 'https://www.google.com/recaptcha/admin',
			'recaptcha_v3' => 'https://www.google.com/recaptcha/admin',
			'turnstile' => 'https://dash.cloudflare.com/sign-up?to=/:account/turnstile',
			'hcaptcha' => 'https://www.hcaptcha.com/signup-interstitial',
			'altcha' => '#',
		);
		?>
		<h2><?php echo sprintf( esc_html__( 'Configure %s', 'buddypress-recaptcha' ), esc_html( $service_name ) ); ?></h2>

		<?php if ( 'altcha' === $service ) : ?>
			<p><?php esc_html_e( 'ALTCHA is a self-hosted solution that doesn\'t require external API keys. Just generate a secret key below.', 'buddypress-recaptcha' ); ?></p>

			<div class="wbc-form-group">
				<label for="wbc_altcha_hmac_key"><?php esc_html_e( 'HMAC Secret Key', 'buddypress-recaptcha' ); ?></label>
				<input type="text" name="wbc_altcha_hmac_key" id="wbc_altcha_hmac_key" value="<?php echo esc_attr( get_option( 'wbc_altcha_hmac_key' ) ); ?>">
				<button type="button" class="button" onclick="generateHMACKey()"><?php esc_html_e( 'Generate Key', 'buddypress-recaptcha' ); ?></button>
				<p class="description"><?php esc_html_e( 'Click "Generate Key" to create a secure secret key.', 'buddypress-recaptcha' ); ?></p>
			</div>

			<script>
			function generateHMACKey() {
				const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				let key = '';
				for (let i = 0; i < 32; i++) {
					key += chars.charAt(Math.floor(Math.random() * chars.length));
				}
				document.getElementById('wbc_altcha_hmac_key').value = key;
			}
			</script>
		<?php else : ?>
			<p><?php
			echo sprintf(
				esc_html__( 'Enter your API keys for %s. Don\'t have keys yet? %sGet them here%s', 'buddypress-recaptcha' ),
				esc_html( $service_name ),
				'<a href="' . esc_url( $signup_urls[$service] ) . '" target="_blank">',
				'</a>'
			);
			?></p>

			<div class="wbc-form-group">
				<label for="site_key"><?php esc_html_e( 'Site Key', 'buddypress-recaptcha' ); ?></label>
				<input type="text" name="site_key" id="site_key" value="<?php echo esc_attr( $this->get_site_key( $service ) ); ?>">
				<p class="description"><?php esc_html_e( 'Your public site key', 'buddypress-recaptcha' ); ?></p>
			</div>

			<div class="wbc-form-group">
				<label for="secret_key"><?php esc_html_e( 'Secret Key', 'buddypress-recaptcha' ); ?></label>
				<input type="password" name="secret_key" id="secret_key" value="<?php echo esc_attr( $this->get_secret_key( $service ) ); ?>">
				<p class="description"><?php esc_html_e( 'Your private secret key', 'buddypress-recaptcha' ); ?></p>
			</div>

			<a href="#" class="wbc-test-connection" onclick="testConnection(); return false;">
				<?php esc_html_e( 'Test Connection', 'buddypress-recaptcha' ); ?>
			</a>

			<div id="test-result"></div>
		<?php endif; ?>

		<div class="wbc-setup-footer">
			<a href="<?php echo esc_url( $this->get_next_step_link( 'service' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Back', 'buddypress-recaptcha' ); ?>
			</a>
			<button type="submit" class="button button-primary" name="save_step" value="1">
				<?php esc_html_e( 'Continue', 'buddypress-recaptcha' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Get site key for service
	 */
	private function get_site_key( $service ) {
		$key_map = array(
			'recaptcha_v2' => 'wbc_recaptcha_v2_site_key',
			'recaptcha_v3' => 'wbc_recaptcha_v3_site_key',
			'turnstile' => 'wbc_turnstile_site_key',
			'hcaptcha' => 'wbc_hcaptcha_site_key',
		);
		return isset( $key_map[$service] ) ? get_option( $key_map[$service] ) : '';
	}

	/**
	 * Get secret key for service
	 */
	private function get_secret_key( $service ) {
		$key_map = array(
			'recaptcha_v2' => 'wbc_recaptcha_v2_secret_key',
			'recaptcha_v3' => 'wbc_recaptcha_v3_secret_key',
			'turnstile' => 'wbc_turnstile_secret_key',
			'hcaptcha' => 'wbc_hcaptcha_secret_key',
		);
		return isset( $key_map[$service] ) ? get_option( $key_map[$service] ) : '';
	}

	/**
	 * Save API keys
	 */
	public function wbc_setup_keys_save() {
		check_admin_referer( 'wbc-setup' );

		$service = get_option( 'wbc_captcha_service', 'recaptcha_v2' );

		if ( 'altcha' === $service ) {
			if ( isset( $_POST['wbc_altcha_hmac_key'] ) ) {
				update_option( 'wbc_altcha_hmac_key', sanitize_text_field( $_POST['wbc_altcha_hmac_key'] ) );
			}
		} else {
			$site_key = isset( $_POST['site_key'] ) ? sanitize_text_field( $_POST['site_key'] ) : '';
			$secret_key = isset( $_POST['secret_key'] ) ? sanitize_text_field( $_POST['secret_key'] ) : '';

			$key_map = array(
				'recaptcha_v2' => array( 'wbc_recaptcha_v2_site_key', 'wbc_recaptcha_v2_secret_key' ),
				'recaptcha_v3' => array( 'wbc_recaptcha_v3_site_key', 'wbc_recaptcha_v3_secret_key' ),
				'turnstile' => array( 'wbc_turnstile_site_key', 'wbc_turnstile_secret_key' ),
				'hcaptcha' => array( 'wbc_hcaptcha_site_key', 'wbc_hcaptcha_secret_key' ),
			);

			if ( isset( $key_map[$service] ) ) {
				update_option( $key_map[$service][0], $site_key );
				update_option( $key_map[$service][1], $secret_key );
			}
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Forms protection step
	 */
	public function wbc_setup_forms() {
		?>
		<h2><?php esc_html_e( 'Choose Forms to Protect', 'buddypress-recaptcha' ); ?></h2>
		<p><?php esc_html_e( 'Select which forms you want to protect from spam and bots. You can always change these settings later.', 'buddypress-recaptcha' ); ?></p>

		<h3><?php esc_html_e( 'Essential Protection', 'buddypress-recaptcha' ); ?></h3>

		<div class="wbc-form-checkbox">
			<input type="checkbox" name="forms[wplogin]" id="form_wplogin" value="1" checked>
			<label for="form_wplogin">
				<span class="label-title"><?php esc_html_e( 'Login Forms', 'buddypress-recaptcha' ); ?></span>
				<span class="label-desc"><?php esc_html_e( 'Protect against brute force login attacks', 'buddypress-recaptcha' ); ?></span>
			</label>
		</div>

		<div class="wbc-form-checkbox">
			<input type="checkbox" name="forms[wpregister]" id="form_wpregister" value="1" checked>
			<label for="form_wpregister">
				<span class="label-title"><?php esc_html_e( 'Registration Forms', 'buddypress-recaptcha' ); ?></span>
				<span class="label-desc"><?php esc_html_e( 'Prevent spam account registrations', 'buddypress-recaptcha' ); ?></span>
			</label>
		</div>

		<div class="wbc-form-checkbox">
			<input type="checkbox" name="forms[comment]" id="form_comment" value="1" checked>
			<label for="form_comment">
				<span class="label-title"><?php esc_html_e( 'Comment Forms', 'buddypress-recaptcha' ); ?></span>
				<span class="label-desc"><?php esc_html_e( 'Stop spam comments on your posts', 'buddypress-recaptcha' ); ?></span>
			</label>
		</div>

		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<h3><?php esc_html_e( 'WooCommerce', 'buddypress-recaptcha' ); ?></h3>

		<div class="wbc-form-checkbox">
			<input type="checkbox" name="forms[woo_checkout]" id="form_woo_checkout" value="1">
			<label for="form_woo_checkout">
				<span class="label-title"><?php esc_html_e( 'Checkout Form', 'buddypress-recaptcha' ); ?></span>
				<span class="label-desc"><?php esc_html_e( 'Protect checkout from automated bots', 'buddypress-recaptcha' ); ?></span>
			</label>
		</div>
		<?php endif; ?>

		<?php if ( class_exists( 'BuddyPress' ) ) : ?>
		<h3><?php esc_html_e( 'BuddyPress', 'buddypress-recaptcha' ); ?></h3>

		<div class="wbc-form-checkbox">
			<input type="checkbox" name="forms[bp_register]" id="form_bp_register" value="1">
			<label for="form_bp_register">
				<span class="label-title"><?php esc_html_e( 'Member Registration', 'buddypress-recaptcha' ); ?></span>
				<span class="label-desc"><?php esc_html_e( 'Protect community registration', 'buddypress-recaptcha' ); ?></span>
			</label>
		</div>
		<?php endif; ?>

		<div class="wbc-setup-footer">
			<a href="<?php echo esc_url( $this->get_next_step_link( 'keys' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Back', 'buddypress-recaptcha' ); ?>
			</a>
			<button type="submit" class="button button-primary" name="save_step" value="1">
				<?php esc_html_e( 'Complete Setup', 'buddypress-recaptcha' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Save forms selection
	 */
	public function wbc_setup_forms_save() {
		check_admin_referer( 'wbc-setup' );

		$forms = isset( $_POST['forms'] ) ? array_map( 'sanitize_text_field', $_POST['forms'] ) : array();

		// WordPress forms
		update_option( 'wbc_recaptcha_enable_on_wplogin', isset( $forms['wplogin'] ) ? 'yes' : 'no' );
		update_option( 'wbc_recaptcha_enable_on_wpregister', isset( $forms['wpregister'] ) ? 'yes' : 'no' );
		update_option( 'wbc_recaptcha_enable_on_comment', isset( $forms['comment'] ) ? 'yes' : 'no' );

		// WooCommerce
		update_option( 'wbc_recaptcha_enable_on_guestcheckout', isset( $forms['woo_checkout'] ) ? 'yes' : 'no' );

		// BuddyPress
		update_option( 'wbc_recaptcha_enable_on_buddypress', isset( $forms['bp_register'] ) ? 'yes' : 'no' );

		// Mark setup as complete
		update_option( 'wbc_setup_complete', 'yes' );
		update_option( 'wbc_setup_version', RFB_PLUGIN_VERSION );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Setup complete
	 */
	public function wbc_setup_complete() {
		?>
		<div class="wbc-success-icon">✅</div>
		<h2><?php esc_html_e( 'Setup Complete! 🎉', 'buddypress-recaptcha' ); ?></h2>
		<p><?php esc_html_e( 'Congratulations! Your site is now protected from spam and bots. Your selected forms are secured with captcha protection.', 'buddypress-recaptcha' ); ?></p>

		<div class="wbc-alert wbc-alert-success">
			<strong><?php esc_html_e( 'What\'s next?', 'buddypress-recaptcha' ); ?></strong><br>
			<?php esc_html_e( '• Test your forms to ensure captcha is working', 'buddypress-recaptcha' ); ?><br>
			<?php esc_html_e( '• Customize appearance settings if needed', 'buddypress-recaptcha' ); ?><br>
			<?php esc_html_e( '• Monitor spam reduction in your dashboard', 'buddypress-recaptcha' ); ?>
		</div>

		<div class="wbc-setup-footer">
			<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Return to Dashboard', 'buddypress-recaptcha' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=buddypress-recaptcha' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Go to Settings', 'buddypress-recaptcha' ); ?>
			</a>
		</div>
		<?php
	}
}

// Initialize the setup wizard
new WBC_Setup_Wizard();