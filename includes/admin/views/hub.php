<?php
/**
 * WB Plugins hub — landing dashboard at ?page=wbcomplugins.
 *
 * Lists every Wbcom plugin that has registered a submenu under the shared
 * wbcomplugins parent. Peer plugins appear automatically once their admin
 * page is in the WordPress menu table. Legacy wbcom-wrapper helper pages
 * are filtered out so they don't read as duplicate tiles. See playbook
 * Part 16.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

$bprc_submenu_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] )
	? $GLOBALS['submenu']['wbcomplugins']
	: array();

// Slugs of the legacy wbcom-wrapper boilerplate helper pages. Filterable
// so Pro modules or future wrapper slugs can be added.
$bprc_wrapper_helper_slugs = apply_filters(
	'wbcom_hub_wrapper_helper_slugs',
	array(
		'wbcom-plugins-page',
		'wbcom-themes-page',
		'wbcom-support-page',
		'wbcom-license-page',
	)
);

$bprc_plugins = array();
foreach ( $bprc_submenu_entries as $bprc_entry ) {
	$bprc_slug = isset( $bprc_entry[2] ) ? (string) $bprc_entry[2] : '';
	if ( '' === $bprc_slug || 'wbcomplugins' === $bprc_slug ) {
		continue;
	}
	if ( in_array( $bprc_slug, $bprc_wrapper_helper_slugs, true ) ) {
		continue;
	}
	$bprc_plugins[] = array(
		'slug'       => $bprc_slug,
		'menu_title' => isset( $bprc_entry[0] ) ? wp_strip_all_tags( (string) $bprc_entry[0] ) : $bprc_slug,
		'page_title' => isset( $bprc_entry[3] ) ? wp_strip_all_tags( (string) $bprc_entry[3] ) : '',
		'url'        => admin_url( 'admin.php?page=' . rawurlencode( $bprc_slug ) ),
	);
}

$bprc_plugin_count = count( $bprc_plugins );
?>
<div class="wrap bprc-admin">
	<header class="bprc-page-header">
		<div class="bprc-page-header__title">
			<span class="dashicons dashicons-lightbulb" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'WB Plugins', 'buddypress-recaptcha' ); ?></h1>
				<p class="bprc-page-header__subtitle">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: active Wbcom plugin count */
							_n(
								'%d Wbcom plugin active on this site.',
								'%d Wbcom plugins active on this site.',
								$bprc_plugin_count,
								'buddypress-recaptcha'
							),
							$bprc_plugin_count
						)
					);
					?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 0 === $bprc_plugin_count ) : ?>
		<div class="bprc-empty-state">
			<span class="bprc-empty-state__icon" aria-hidden="true">
				<span class="dashicons dashicons-lightbulb"></span>
			</span>
			<p class="bprc-empty-state__title"><?php esc_html_e( 'No Wbcom plugins attached to this hub yet', 'buddypress-recaptcha' ); ?></p>
			<p class="bprc-empty-state__desc">
				<?php esc_html_e( 'Activate one or more Wbcom plugins and they will appear here automatically.', 'buddypress-recaptcha' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="bprc-hub-grid">
			<?php foreach ( $bprc_plugins as $bprc_p ) : ?>
				<a href="<?php echo esc_url( $bprc_p['url'] ); ?>" class="bprc-hub-card">
					<span class="bprc-hub-card__icon" aria-hidden="true">
						<span class="dashicons dashicons-admin-plugins"></span>
					</span>
					<span class="bprc-hub-card__title"><?php echo esc_html( $bprc_p['menu_title'] ); ?></span>
					<?php if ( ! empty( $bprc_p['page_title'] ) && $bprc_p['page_title'] !== $bprc_p['menu_title'] ) : ?>
						<span class="bprc-hub-card__subtitle"><?php echo esc_html( $bprc_p['page_title'] ); ?></span>
					<?php endif; ?>
					<span class="bprc-hub-card__cta">
						<?php esc_html_e( 'Open settings', 'buddypress-recaptcha' ); ?>
						<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="bprc-card">
		<div class="bprc-card__head">
			<p class="bprc-card__title"><?php esc_html_e( 'About WB Plugins', 'buddypress-recaptcha' ); ?></p>
		</div>
		<div class="bprc-card__body">
			<p style="margin: 0 0 8px;">
				<?php esc_html_e( 'This hub is the single entry point for every Wbcom Designs plugin installed on your site. Each plugin lives on its own page under this menu and keeps its own settings and data.', 'buddypress-recaptcha' ); ?>
			</p>
			<p style="margin: 0;">
				<a href="https://wbcomdesigns.com/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Visit wbcomdesigns.com for more plugins and themes →', 'buddypress-recaptcha' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
