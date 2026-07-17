<?php
/**
 * Overview tab — status snapshot + help resources.
 *
 * Read-only dashboard. Reads the same wbc_* options the settings tabs
 * write; it never persists anything itself.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

$bprc_service = get_option( 'wbc_captcha_service', 'recaptcha-v2' );

$bprc_service_names = array(
	'recaptcha-v2' => __( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ),
	'recaptcha-v3' => __( 'Google reCAPTCHA v3', 'buddypress-recaptcha' ),
	'turnstile'    => 'Cloudflare Turnstile',
	'hcaptcha'     => 'hCaptcha',
	'altcha'       => 'ALTCHA',
);
$bprc_service_label = isset( $bprc_service_names[ $bprc_service ] )
	? $bprc_service_names[ $bprc_service ]
	: __( 'Not selected', 'buddypress-recaptcha' );

// Determine whether the active service has its required keys configured.

$bprc_key_map    = array(
	'recaptcha-v2' => array( 'wbc_recaptcha_v2_site_key', 'wbc_recaptcha_v2_secret_key' ),
	'recaptcha-v3' => array( 'wbc_recaptcha_v3_site_key', 'wbc_recaptcha_v3_secret_key' ),
	'turnstile'    => array( 'wbc_turnstile_site_key', 'wbc_turnstile_secret_key' ),
	'hcaptcha'     => array( 'wbc_hcaptcha_site_key', 'wbc_hcaptcha_secret_key' ),
	'altcha'       => array( 'wbc_altcha_hmac_key' ),
);
$bprc_configured = true;
if ( isset( $bprc_key_map[ $bprc_service ] ) ) {
	foreach ( $bprc_key_map[ $bprc_service ] as $bprc_key_opt ) {
		if ( '' === trim( (string) get_option( $bprc_key_opt, '' ) ) ) {
			$bprc_configured = false;
			break;
		}
	}
}

$bprc_setup_url   = admin_url( 'admin.php?page=' . BPRC_Admin::MENU_SLUG . '&tab=rfw-general' );
$bprc_protect_url = admin_url( 'admin.php?page=' . BPRC_Admin::MENU_SLUG . '&tab=protection' );
?>

<?php if ( ! $bprc_configured ) : ?>
	<div class="bprc-notice bprc-notice--warn">
		<span class="dashicons dashicons-warning" aria-hidden="true"></span>
		<span>
			<?php
			printf(
				wp_kses(
					/* translators: %s: URL to the Quick Setup tab. */
					__( 'Your active CAPTCHA service is missing its API keys. <a href="%s">Finish setup</a> so spam protection actually runs.', 'buddypress-recaptcha' ),
					array( 'a' => array( 'href' => array() ) )
				),
				esc_url( $bprc_setup_url )
			);
			?>
		</span>
	</div>
<?php else : ?>
	<div class="bprc-notice bprc-notice--success">
		<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
		<span><?php esc_html_e( 'Your CAPTCHA service is configured and ready to protect your forms.', 'buddypress-recaptcha' ); ?></span>
	</div>
<?php endif; ?>

<div class="bprc-stats-grid">
	<div class="bprc-stat">
		<p class="bprc-stat__label"><?php esc_html_e( 'Active Service', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-stat__value" style="font-size:16px;"><?php echo esc_html( $bprc_service_label ); ?></p>
	</div>
	<div class="bprc-stat">
		<p class="bprc-stat__label"><?php esc_html_e( 'Keys Status', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-stat__value" style="font-size:16px;">
			<?php echo $bprc_configured ? esc_html__( 'Configured', 'buddypress-recaptcha' ) : esc_html__( 'Incomplete', 'buddypress-recaptcha' ); ?>
		</p>
	</div>
	<div class="bprc-stat">
		<p class="bprc-stat__label"><?php esc_html_e( 'Updates', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-stat__value" style="font-size:16px;"><?php esc_html_e( 'Automatic', 'buddypress-recaptcha' ); ?></p>
	</div>
</div>

<div class="bprc-card">
	<div class="bprc-card__head">
		<p class="bprc-card__title"><?php esc_html_e( 'Get Started', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-card__desc"><?php esc_html_e( 'Choose a CAPTCHA service, add your keys, then turn on protection for the forms you want to guard.', 'buddypress-recaptcha' ); ?></p>
	</div>
	<div class="bprc-card__body">
		<div class="bprc-save-bar" style="padding-top:0;">
			<a href="<?php echo esc_url( $bprc_setup_url ); ?>" class="bprc-btn bprc-btn-primary">
				<span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
				<?php esc_html_e( 'Quick Setup', 'buddypress-recaptcha' ); ?>
			</a>
			<a href="<?php echo esc_url( $bprc_protect_url ); ?>" class="bprc-btn bprc-btn-secondary">
				<span class="dashicons dashicons-shield" aria-hidden="true"></span>
				<?php esc_html_e( 'Choose Forms to Protect', 'buddypress-recaptcha' ); ?>
			</a>
		</div>
	</div>
</div>

<div class="bprc-card">
	<div class="bprc-card__head">
		<p class="bprc-card__title"><?php esc_html_e( 'Help &amp; Support', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-card__desc"><?php esc_html_e( 'Everything you need to set up, troubleshoot, and get the most out of the plugin.', 'buddypress-recaptcha' ); ?></p>
	</div>
	<div class="bprc-card__body">
		<div class="bprc-link-grid">
			<div class="bprc-link-card">
				<h3><span class="dashicons dashicons-book" aria-hidden="true"></span><?php esc_html_e( 'Documentation', 'buddypress-recaptcha' ); ?></h3>
				<p><?php esc_html_e( 'Step-by-step setup guides and best practices for every supported CAPTCHA service.', 'buddypress-recaptcha' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/docs/buddypress-recaptcha/" class="bprc-btn bprc-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read Documentation', 'buddypress-recaptcha' ); ?></a>
			</div>
			<div class="bprc-link-card">
				<h3><span class="dashicons dashicons-sos" aria-hidden="true"></span><?php esc_html_e( 'Support Center', 'buddypress-recaptcha' ); ?></h3>
				<p><?php esc_html_e( 'Get expert help with setup, configuration, and troubleshooting from our team.', 'buddypress-recaptcha' ); ?></p>
				<a href="https://wbcomdesigns.com/support/" class="bprc-btn bprc-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get Support', 'buddypress-recaptcha' ); ?></a>
			</div>
			<div class="bprc-link-card">
				<h3><span class="dashicons dashicons-admin-comments" aria-hidden="true"></span><?php esc_html_e( 'Send Feedback', 'buddypress-recaptcha' ); ?></h3>
				<p><?php esc_html_e( 'Share your experience and suggestions to help us make the plugin even better.', 'buddypress-recaptcha' ); ?></p>
				<a href="https://wbcomdesigns.com/submit-review/" class="bprc-btn bprc-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Send Feedback', 'buddypress-recaptcha' ); ?></a>
			</div>
		</div>
	</div>
</div>
