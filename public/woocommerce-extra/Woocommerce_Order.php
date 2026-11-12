<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
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
class Woocommerce_Order {

	/**
	 * Display comment form captcha
	 *
	 * @param array $fields Comment form fields.
	 * @return array
	 */
	public function woo_comment_form_captcha_field( $fields ) {
		// Check if captcha is enabled for comments.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_comment' );
		if ( 'yes' !== $is_enabled ) {
			return $fields;
		}

		// Moderators always, other members per "Skip for Logged-in Users".
		if ( wbc_skip_comment_captcha_for_current_user() ) {
			return $fields;
		}

		// Add captcha field.
		ob_start();
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			wbc_captcha_service_manager()->render( 'comment' );
		}
		$captcha_html = ob_get_clean();

		$fields['captcha'] = $captcha_html;

		return $fields;
	}

	/**
	 * Add CAPTCHA to WooCommerce product review form
	 *
	 * WooCommerce builds the comment form fields manually and passes them to comment_form(),
	 * which bypasses the standard comment_form_fields filter. We need to hook into
	 * WooCommerce's specific filter to add CAPTCHA to the fields array.
	 *
	 * @param array $comment_form Comment form arguments.
	 * @return array Modified comment form arguments with CAPTCHA field.
	 */
	public function woo_product_review_captcha_field( $comment_form ) {
		// Check if captcha is enabled for comments.
		$is_enabled = get_option( 'wbc_recaptcha_enable_on_comment' );
		if ( 'yes' !== $is_enabled ) {
			return $comment_form;
		}

		// Moderators always, other members per "Skip for Logged-in Users".
		if ( wbc_skip_comment_captcha_for_current_user() ) {
			return $comment_form;
		}

		// Generate captcha HTML.
		ob_start();
		if ( function_exists( 'wbc_captcha_service_manager' ) ) {
			echo '<div class="woo-comment-captcha">';
			wbc_captcha_service_manager()->render( 'comment' );
			echo '</div>';
		}
		$captcha_html = ob_get_clean();

		// Add captcha to fields array (WooCommerce manually builds this array).
		if ( ! isset( $comment_form['fields'] ) ) {
			$comment_form['fields'] = array();
		}

		$comment_form['fields']['captcha'] = $captcha_html;

		return $comment_form;
	}

	/**
	 * Modify WooCommerce Blocks checkout payment block to include CAPTCHA
	 *
	 * This method is called by the 'render_block_woocommerce/checkout-payment-block' filter
	 * to inject CAPTCHA fields into the WooCommerce Blocks checkout experience.
	 *
	 * @param string $block_content The block content from WooCommerce Blocks.
	 * @return string Modified block content with CAPTCHA field appended.
	 * @since 2.0.0
	 */
	public function woo_recaptcha_alter_checkout_payment_block( $block_content ) {
		// Check if we should display CAPTCHA for checkout.
		// For guest checkout.
		$guest_checkout_enabled = get_option( 'wbc_recaptcha_enable_on_guestcheckout' );
		// For logged-in checkout.
		$login_checkout_enabled = get_option( 'wbc_recaptcha_enable_on_logincheckout' );

		// Determine which context to use based on user login status.
		$should_show = false;
		$context     = '';

		if ( is_user_logged_in() && 'yes' === $login_checkout_enabled ) {
			$should_show = true;
			$context     = 'woo_checkout_login';
		} elseif ( ! is_user_logged_in() && 'yes' === $guest_checkout_enabled ) {
			$should_show = true;
			$context     = 'woo_checkout_guest';
		}

		// If CAPTCHA is not enabled for this checkout type, return original content.
		if ( ! $should_show || empty( $context ) ) {
			return $block_content;
		}

		$manager = wbc_captcha_service_manager();
		$service = $manager->get_active_service();
		if ( ! $service ) {
			return $block_content;
		}

		ob_start();
		$manager->render( $context );
		$captcha_html = ob_get_clean();
		if ( '' === trim( $captcha_html ) ) {
			return $block_content; // Not rendered (allowlisted IP, kill switch, no keys).
		}

		wp_enqueue_script( 'wbc-checkout-block' );
		$captcha_html = '<div class="wbc-checkout-captcha-wrapper" style="margin: 20px 0;" data-wbc-field="' . esc_attr( $service->get_response_field_name() ) . '">' . $captcha_html . '</div>';

		// Append CAPTCHA to the block content.
		return $block_content . $captcha_html;
	}
}
