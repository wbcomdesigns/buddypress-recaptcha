<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
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
class Registrationbp {

	/**
	 * Render captcha on BuddyPress registration form
	 */
	public function woo_extra_bp_register_form() {
		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'bp_register' );
			do_action( 'bp_accept_tos_errors' ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}
	}

	/**
	 * Validate BuddyPress registration
	 *
	 * @return bool|WP_Error
	 */
	public function innovage_validate_user_registration() {
		// Verify captcha using the service manager.
		if ( function_exists( 'wbc_verify_captcha' ) ) {
			if ( ! wbc_verify_captcha( 'bp_register' ) ) {
				$error_message                                      = wbc_get_captcha_error_message( 'bp_register', 'invalid' );
				buddypress()->signup->errors['bp_register_captcha'] = $error_message;
				return new WP_Error( 'captcha_error', $error_message );
			}
		}
		return true;
	}

	/**
	 * Render captcha on BuddyPress group creation form
	 */
	public function render_bp_group_create_captcha() {
		// Check if CAPTCHA is enabled for group creation.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_bp_group_create' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Use the service manager to render captcha.
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="bp-group-captcha-wrap">';
			wbc_captcha_service_manager()->render( 'bp_group_create' );
			echo '</div>';
		}
	}

	/**
	 * Validate BuddyPress group creation
	 */
	public function validate_bp_group_create_captcha() {
		// Only guard the group-creation wizard, and only the group-details step
		// where the CAPTCHA is actually rendered. groups_group_before_save also
		// fires on later wizard steps and on admin edits of an existing group -
		// none of which render a CAPTCHA, so verifying there would falsely block
		// legitimate saves.
		if ( ! function_exists( 'bp_is_group_create' ) || ! bp_is_group_create() ) {
			return;
		}
		if ( function_exists( 'bp_is_group_creation_step' ) && ! bp_is_group_creation_step( 'group-details' ) ) {
			return;
		}

		// Check if CAPTCHA is enabled for group creation.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_bp_group_create' );
		if ( 'yes' !== $is_enabled ) {
			return;
		}

		// Verify captcha using the service manager. groups_group_before_save is a
		// do_action whose return value is ignored, so we must abort the request
		// ourselves: add a BuddyPress error message and redirect back to the
		// group-details step before the group row is written.
		if ( function_exists( 'wbc_verify_captcha' ) && ! wbc_verify_captcha( 'bp_group_create' ) ) {
			$error_message = wbc_get_captcha_error_message( 'bp_group_create', 'invalid' );

			if ( function_exists( 'bp_core_add_message' ) ) {
				bp_core_add_message( $error_message, 'error' );
			}

			if ( function_exists( 'bp_core_redirect' ) && function_exists( 'bp_get_groups_directory_permalink' ) ) {
				bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create/step/group-details' ) );
			}
		}
	}
}
