<?php
/**
 * Wbcom license page template.
 *
 * @package Recaptcha_For_BuddyPress
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap">
	<div class="wbcom-bb-plugins-offer-wrapper">
		<div id="wb_admin_logo">
		</div>
	</div>
	<div class="wbcom-wrap wbcom-plugin-wrapper">
		<div class="wbcom_admin_header-wrapper">
			<div id="wb_admin_plugin_name">
				<?php esc_html_e( 'Wbcom CAPTCHA Manager', 'buddypress-recaptcha' ); ?>
				<span>
				<?php
				/* translators: %s: */
				printf( esc_html__( 'Version %s', 'buddypress-recaptcha' ), esc_attr( RFB_PLUGIN_VERSION ) );
				?>
				</span>
			</div>
			<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
		</div>
		<div class="wbcom-all-addons-plugins-wrap">
		<h4 class="wbcom-support-section"><?php esc_html_e( 'Plugin License', 'buddypress-recaptcha' ); ?></h4>
		<div class="wb-plugins-license-tables-wrap">
			<div class="wbcom-license-support-wrapp">
			<table class="form-table wb-license-form-table desktop-license-headings">
				<thead>
					<tr>
						<th class="wb-product-th"><?php esc_html_e( 'Product', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-version-th"><?php esc_html_e( 'Version', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-key-th"><?php esc_html_e( 'Key', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-status-th"><?php esc_html_e( 'Status', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-action-th"><?php esc_html_e( 'Action', 'buddypress-recaptcha' ); ?></th>
					</tr>
				</thead>
			</table>
			<?php do_action( 'wbcom_add_plugin_license_code' ); ?>
			<table class="form-table wb-license-form-table">
				<tfoot>
					<tr>
						<th class="wb-product-th"><?php esc_html_e( 'Product', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-version-th"><?php esc_html_e( 'Version', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-key-th"><?php esc_html_e( 'Key', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-status-th"><?php esc_html_e( 'Status', 'buddypress-recaptcha' ); ?></th>
						<th class="wb-action-th"><?php esc_html_e( 'Action', 'buddypress-recaptcha' ); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	</div>
	</div><!-- .wbcom-wrap -->
</div><!-- .wrap -->
