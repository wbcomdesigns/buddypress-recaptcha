<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/
 * @since 1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/bp-classes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid, Squiz.Commenting.ClassComment.Missing
class Recaptcha_bbPress_Reply {
	// phpcs:enable PEAR.NamingConventions.ValidClassName.Invalid, Squiz.Commenting.ClassComment.Missing

	/**
	 * Render captcha on bbPress reply form
	 */
	public function wbr_bbpress_reply_form_field_reply() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'bbpress_reply' );
		}
	}

	/**
	 * Verify captcha when creating a bbPress reply
	 *
	 * @param array $reply_data Reply data.
	 * @return array|WP_Error
	 */
	public function wbr_bbpress_reply_recaptcha_verify( $reply_data ) {
		// Verify captcha using the service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'bbpress_reply' ) ) {
				$error_message = wbc_get_captcha_error_message( 'bbpress_reply', 'invalid' );
				bbp_add_error( 'bbp_reply_captcha', $error_message );
				return new WP_Error( 'captcha_error', $error_message );
			}
		}

		return $reply_data;
	}
}
