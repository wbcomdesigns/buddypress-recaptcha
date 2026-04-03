<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://wbcomdesigns.com/
 * @since 1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public/bbPress
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
class Recaptcha_bbPress_Topic {
	// phpcs:enable PEAR.NamingConventions.ValidClassName.Invalid, Squiz.Commenting.ClassComment.Missing

	/**
	 * Render captcha on bbPress topic form
	 */
	public function wbr_bbpress_topic_form_field() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'bbpress_topic' );
		}
	}

	/**
	 * Verify captcha when creating a bbPress topic
	 *
	 * @param array $topic_data Topic data.
	 * @return array|WP_Error
	 */
	public function wbr_bbpress_topic_recaptcha_verify( $topic_data ) {
		// Verify captcha using the service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'bbpress_topic' ) ) {
				$error_message = wbc_get_captcha_error_message( 'bbpress_topic', 'invalid' );
				bbp_add_error( 'bbp_topic_captcha', $error_message );
				return new WP_Error( 'captcha_error', $error_message );
			}
		}

		return $topic_data;
	}
}
