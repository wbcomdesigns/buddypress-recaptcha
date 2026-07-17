<?php
/**
 * Discover partial: ecosystem cross-promotion (read-only display view).
 *
 * Rendered inside shell.php. Pure presentation - product cards linking out
 * to other free Wbcom Designs tools. No forms, settings, options, or AJAX.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

$bprc_ecosystem_url = ( defined( 'RFB_PLUGIN_URL' ) ? RFB_PLUGIN_URL : plugins_url( '/', dirname( __DIR__, 2 ) ) ) . 'assets/images/ecosystem/';
$bprc_ecosystem_dir = ( defined( 'RFB_PLUGIN_PATH' ) ? RFB_PLUGIN_PATH : dirname( __DIR__, 3 ) . '/' ) . 'assets/images/ecosystem/';

$bprc_ecosystem = array(
	array(
		'name' => __( 'BuddyX', 'buddypress-recaptcha' ),
		'logo' => 'buddyx.png',
		'icon' => 'admin-appearance',
		'desc' => __( 'A free, fast community theme for BuddyPress, BuddyBoss and PeepSo with a modern layout and dark mode.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddyx-theme/',
	),
	array(
		'name' => __( 'BuddyNext', 'buddypress-recaptcha' ),
		'logo' => 'buddynext.svg',
		'icon' => 'groups',
		'desc' => __( 'The full community stack: activity feeds, member spaces, profiles, and private messaging on WordPress.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
	),
	array(
		'name' => __( 'Jetonomy', 'buddypress-recaptcha' ),
		'logo' => 'jetonomy.svg',
		'icon' => 'format-chat',
		'desc' => __( 'Self-moderating forums and Q&A boards that stay fast well beyond 100,000 topics.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
	),
	array(
		'name' => __( 'Mediaverse', 'buddypress-recaptcha' ),
		'logo' => 'mediaverse.svg',
		'icon' => 'format-gallery',
		'desc' => __( 'A photo and video hub with albums, reactions, following, and private chat.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
	),
	array(
		'name' => __( 'Eventonomy', 'buddypress-recaptcha' ),
		'logo' => 'eventonomy.svg',
		'icon' => 'calendar-alt',
		'desc' => __( 'Run community events with RSVPs, calendars, and front-end submissions.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/eventonomy/',
	),
	array(
		'name' => __( 'WB Gamification', 'buddypress-recaptcha' ),
		'logo' => 'wb-gamification.svg',
		'icon' => 'awards',
		'desc' => __( 'Reward members with points, badges, and leaderboards to keep engagement high.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/',
	),
	array(
		'name' => __( 'Listora', 'buddypress-recaptcha' ),
		'logo' => 'listora.svg',
		'icon' => 'list-view',
		'desc' => __( 'Searchable directories with ten listing types, ratings, maps, and front-end submissions.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/listora/',
	),
	array(
		'name' => __( 'WP Career Board', 'buddypress-recaptcha' ),
		'logo' => 'wp-career-board.svg',
		'icon' => 'businessman',
		'desc' => __( 'Add a job board with front-end listings, applications, and employer profiles.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wp-career-board/',
	),
	array(
		'name' => __( 'Learnomy', 'buddypress-recaptcha' ),
		'logo' => 'learnomy.svg',
		'icon' => 'welcome-learn-more',
		'desc' => __( 'Create, sell, and auto-grade online courses, then hand out certificates automatically.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
	),
	array(
		'name' => __( 'WP Sell Services', 'buddypress-recaptcha' ),
		'logo' => 'wp-sell-services.svg',
		'icon' => 'cart',
		'desc' => __( 'A free service marketplace with vendor dashboards, an 11-status order flow, and Stripe or PayPal built in.', 'buddypress-recaptcha' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wp-sell-services/',
	),
);
?>

<div class="bprc-card">
	<div class="bprc-card__head">
		<p class="bprc-card__title"><?php esc_html_e( 'More Free Tools from Wbcom Designs', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-card__desc"><?php esc_html_e( 'CAPTCHA Manager keeps your forms free of spam and bots. These free tools from Wbcom Designs build out the community behind those forms: the theme and network itself, forums, media, events, gamification, directories, jobs, courses, and services.', 'buddypress-recaptcha' ); ?></p>
	</div>
	<div class="bprc-card__body">
		<div class="bprc-discover-grid">
			<?php foreach ( $bprc_ecosystem as $bprc_product ) : ?>
				<div class="bprc-discover-card">
					<span class="bprc-discover-card__logo" aria-hidden="true">
						<?php if ( file_exists( $bprc_ecosystem_dir . $bprc_product['logo'] ) ) : ?>
							<img src="<?php echo esc_url( $bprc_ecosystem_url . $bprc_product['logo'] ); ?>" alt="<?php echo esc_attr( $bprc_product['name'] ); ?>" width="52" height="52" loading="lazy" />
						<?php else : ?>
							<span class="dashicons dashicons-<?php echo esc_attr( isset( $bprc_product['icon'] ) ? $bprc_product['icon'] : 'admin-plugins' ); ?>"></span>
						<?php endif; ?>
					</span>
					<h3 class="bprc-discover-card__title"><?php echo esc_html( $bprc_product['name'] ); ?></h3>
					<p class="bprc-discover-card__desc"><?php echo esc_html( $bprc_product['desc'] ); ?></p>
					<a class="bprc-btn bprc-btn-secondary bprc-discover-card__cta" href="<?php echo esc_url( $bprc_product['url'] ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Get it free', 'buddypress-recaptcha' ); ?>
						<span class="dashicons dashicons-external" aria-hidden="true"></span>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: product name. */
							echo esc_html( sprintf( __( '%s (opens in a new tab)', 'buddypress-recaptcha' ), $bprc_product['name'] ) );
							?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
