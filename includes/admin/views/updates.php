<?php
/**
 * Updates tab — automatic-update status for this free plugin.
 *
 * This plugin ships the official EDD SL SDK in KEYLESS mode (registered
 * in the main plugin file, item_id 1246648). There is no license key to
 * enter: updates flow automatically from wbcomdesigns.com. This tab is a
 * read-only status card replacing the legacy wbcom-license-page, which
 * was vestigial for a keyless free plugin.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

$bprc_version  = defined( 'RFB_PLUGIN_VERSION' ) ? RFB_PLUGIN_VERSION : '';
$bprc_sdk_live = class_exists( '\\EDD\\SoftwareLicensing\\SDK\\SDK' )
	|| function_exists( 'edd_sl_sdk_register_1_0_2' );
?>
<div class="bprc-card">
	<div class="bprc-card__head">
		<p class="bprc-card__title"><?php esc_html_e( 'Plugin Updates', 'buddypress-recaptcha' ); ?></p>
		<p class="bprc-card__desc"><?php esc_html_e( 'Wbcom CAPTCHA Manager is a free plugin. Updates are delivered automatically — no license key required.', 'buddypress-recaptcha' ); ?></p>
	</div>
	<div class="bprc-card__body">
		<?php if ( $bprc_sdk_live ) : ?>
			<div class="bprc-notice bprc-notice--success" style="margin-bottom:16px;">
				<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
				<span><?php esc_html_e( 'Automatic updates are active. New versions appear under Dashboard → Updates and Plugins like any other plugin.', 'buddypress-recaptcha' ); ?></span>
			</div>
		<?php else : ?>
			<div class="bprc-notice bprc-notice--warn" style="margin-bottom:16px;">
				<span class="dashicons dashicons-warning" aria-hidden="true"></span>
				<span><?php esc_html_e( 'The update service could not be detected. Re-install the plugin from wbcomdesigns.com if updates stop appearing.', 'buddypress-recaptcha' ); ?></span>
			</div>
		<?php endif; ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Installed version', 'buddypress-recaptcha' ); ?></th>
				<td><?php echo $bprc_version ? esc_html( 'v' . $bprc_version ) : esc_html__( 'Unknown', 'buddypress-recaptcha' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Update channel', 'buddypress-recaptcha' ); ?></th>
				<td>
					<?php esc_html_e( 'wbcomdesigns.com (automatic)', 'buddypress-recaptcha' ); ?>
					<p class="description">
						<?php
						printf(
							wp_kses(
								/* translators: %s: URL to the Wbcom Designs account page. */
								__( 'Manage your downloads and account at <a href="%s" target="_blank" rel="noopener noreferrer">wbcomdesigns.com</a>.', 'buddypress-recaptcha' ),
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
										'rel'    => array(),
									),
								)
							),
							esc_url( 'https://wbcomdesigns.com/profile/' )
						);
						?>
					</p>
				</td>
			</tr>
		</table>
	</div>
</div>
