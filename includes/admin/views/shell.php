<?php
/**
 * Admin page shell: page header, sidebar nav, body slot.
 *
 * Receives from BPRC_Admin::render_page():
 *
 * @var array                       $bprc_tabs  Tab registry keyed by slug.
 * @var string                      $active     Active tab slug.
 * @var string                      $page_url   admin.php?page=buddypress-recaptcha.
 * @var bool                        $is_form    True when the active tab is a settings form.
 * @var WBC_BuddyPress_Settings_Page $settings  Settings page instance (or null).
 * @var string                      $view       View slug (overview / updates / settings-form).
 * @var string                      $view_path  Absolute path to the partial.
 * @var string                      $plugin_ver Plugin version for the header pill.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap bprc-admin">

	<header class="bprc-page-header">
		<div class="bprc-page-header__title">
			<span class="dashicons dashicons-shield-alt" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'Wbcom CAPTCHA Manager', 'buddypress-recaptcha' ); ?></h1>
				<p class="bprc-page-header__subtitle"><?php esc_html_e( 'Protect WordPress, BuddyPress, WooCommerce, bbPress, and 10+ form builders from spam and bots with reCAPTCHA, Turnstile, hCaptcha, or ALTCHA.', 'buddypress-recaptcha' ); ?></p>
			</div>
		</div>
		<div class="bprc-page-header__actions">
			<?php if ( $plugin_ver ) : ?>
				<span class="bprc-version-pill">v<?php echo esc_html( $plugin_ver ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<?php
	/*
	 * Without this marker, core's common.js re-parents every .notice to
	 * sit right after the first <h1> it finds, which slots the "Settings
	 * saved" banner between our title and its subtitle instead of below
	 * the whole header.
	 */
	?>
	<hr class="wp-header-end">

	<div class="bprc-settings-layout">

		<aside class="bprc-settings-sidebar">
			<div class="bprc-settings-sidebar-brand">
				<span class="bprc-settings-brand-icon" aria-hidden="true">
					<span class="dashicons dashicons-shield-alt"></span>
				</span>
				<div class="bprc-settings-brand-text">
					<p class="bprc-settings-brand-name"><?php esc_html_e( 'CAPTCHA Manager', 'buddypress-recaptcha' ); ?></p>
					<p class="bprc-settings-brand-sub"><?php esc_html_e( 'Plugin', 'buddypress-recaptcha' ); ?></p>
				</div>
			</div>
			<nav class="bprc-settings-sidebar-nav" aria-label="<?php esc_attr_e( 'CAPTCHA Manager navigation', 'buddypress-recaptcha' ); ?>">
				<?php
				$bprc_printed_groups = array();
				$bprc_group_labels   = array(
					'settings' => esc_html__( 'Settings', 'buddypress-recaptcha' ),
					'account'  => esc_html__( 'Account', 'buddypress-recaptcha' ),
				);
				foreach ( $bprc_tabs as $bprc_slug => $bprc_tab ) {
					$bprc_group = isset( $bprc_tab['group'] ) ? $bprc_tab['group'] : 'main';
					if ( 'main' !== $bprc_group && ! in_array( $bprc_group, $bprc_printed_groups, true ) ) {
						echo '<div class="bprc-snav-divider" role="separator"></div>';
						if ( isset( $bprc_group_labels[ $bprc_group ] ) ) {
							echo '<p class="bprc-snav-section-label">' . esc_html( $bprc_group_labels[ $bprc_group ] ) . '</p>';
						}
						$bprc_printed_groups[] = $bprc_group;
					}
					$bprc_classes  = 'bprc-snav-link';
					$bprc_classes .= $active === $bprc_slug ? ' bprc-snav-link--active' : '';
					echo '<a href="' . esc_url( $page_url . '&tab=' . $bprc_slug ) . '" class="' . esc_attr( $bprc_classes ) . '">';
					echo '<span class="dashicons ' . esc_attr( $bprc_tab['icon'] ) . '" aria-hidden="true"></span>';
					echo esc_html( $bprc_tab['label'] );
					echo '</a>';
				}
				?>

				<div class="bprc-snav-divider" role="separator"></div>
				<p class="bprc-snav-section-label"><?php esc_html_e( 'Resources', 'buddypress-recaptcha' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/docs/buddypress-recaptcha/" class="bprc-snav-link" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-book" aria-hidden="true"></span>
					<?php esc_html_e( 'Documentation', 'buddypress-recaptcha' ); ?>
					<span class="dashicons dashicons-external bprc-snav-link__ext" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-recaptcha' ); ?></span>
				</a>
				<a href="https://wbcomdesigns.com/support/" class="bprc-snav-link" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-sos" aria-hidden="true"></span>
					<?php esc_html_e( 'Support', 'buddypress-recaptcha' ); ?>
					<span class="dashicons dashicons-external bprc-snav-link__ext" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-recaptcha' ); ?></span>
				</a>
			</nav>
		</aside>

		<div class="bprc-settings-main">
			<?php settings_errors( BPRC_Admin::ERROR_GROUP ); ?>

			<?php if ( $is_form && $settings ) : ?>
				<?php
				/*
				 * Settings form — same contract as the legacy admin:
				 * posts to itself with the bp_recaptcha_submit_fields_nonce
				 * field; BPRC_Admin::render_page() verifies the nonce and
				 * calls wbc_save( $active ) before this render runs.
				 */
				?>
				<form method="post" id="wb-recaptcha" action="" enctype="multipart/form-data">
					<div class="bprc-card">
						<div class="bprc-card__body">
							<?php $settings->wbc_output( $active ); ?>
						</div>
						<div class="bprc-save-bar">
							<?php wp_nonce_field( BPRC_Admin::NONCE_ACTION, BPRC_Admin::NONCE_FIELD ); ?>
							<?php
							$bprc_btn_text = ( 'rfw-general' === $active )
								? __( 'Save Selection', 'buddypress-recaptcha' )
								: __( 'Save Changes', 'buddypress-recaptcha' );
							?>
							<button name="save" class="bprc-btn bprc-btn-primary" type="submit" value="Save changes"><?php echo esc_html( $bprc_btn_text ); ?></button>
						</div>
					</div>
				</form>
			<?php else : ?>
				<?php
				if ( file_exists( $view_path ) ) {
					include $view_path;
				}
				?>
			<?php endif; ?>
		</div>

	</div>
</div>
