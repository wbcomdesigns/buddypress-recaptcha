<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * WooCommerce checkout CAPTCHA verification.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 */

/**
 * Verifies the CAPTCHA on both WooCommerce checkouts: the classic shortcode
 * checkout (woocommerce_after_checkout_validation) and the block checkout,
 * which places orders through the Store API and never fires the classic hook.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Woocommerce_After_Checkout_Validation {

	/**
	 * Store API extension namespace the block checkout sends the token under.
	 */
	const STORE_API_NAMESPACE = 'wbc-captcha';

	/**
	 * Express-pay request types (Apple Pay / Google Pay / Payment Request
	 * buttons). Those flows never show the checkout form, so the widget is
	 * never solved there.
	 */
	const EXPRESS_TYPES = array( 'apple_pay', 'google_pay', 'payment_request_api' );

	/**
	 * CAPTCHA context for the current shopper.
	 *
	 * @return string
	 */
	private function checkout_context() {
		return is_user_logged_in() ? 'woo_checkout_login' : 'woo_checkout_guest';
	}

	/**
	 * Whether this order comes from an express-pay button. Filterable so a
	 * site that wants every order checked can return false.
	 *
	 * @param string $type Express type reported by the payment gateway.
	 * @return bool
	 */
	private function is_express_payment( $type ) {
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return in_array( $type, self::EXPRESS_TYPES, true ) && (bool) apply_filters( 'wbc_captcha_skip_express_checkout', true, $type );
	}

	/**
	 * Gateways that really offer express buttons. Only these may claim an
	 * express order; anyone can add the flag to a request.
	 *
	 * @return array
	 */
	private function express_gateways() {
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return (array) apply_filters( 'wbc_captcha_express_gateways', array( 'stripe', 'woocommerce_payments' ) );
	}

	/**
	 * The wc-ajax handlers express buttons place their orders through. Checked
	 * with doing_action(), so the skip holds only while WooCommerce is really
	 * running that handler, never because a request says so.
	 *
	 * @return array
	 */
	private function express_endpoints() {
		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return (array) apply_filters( 'wbc_captcha_express_endpoints', array( 'wc_stripe_create_order', 'wcpay_create_order' ) );
	}

	/**
	 * Classic checkout.
	 *
	 * @param array    $fields            Checkout fields.
	 * @param WP_Error $validation_errors Validation errors.
	 * @return WP_Error
	 */
	public function woocomm_validate_checkout_captcha( $fields, $validation_errors ) {
		// WooCommerce checked the checkout nonce before this hook runs.
		// Express buttons place the order from inside their own wc-ajax handler
		// (e.g. wc_ajax_wc_stripe_create_order). WooCommerce processes a posted
		// checkout whatever ?wc-ajax= says, so the endpoint name and the type
		// field are client input; only a handler that is really running counts.
		// phpcs:disable WordPress.Security.NonceVerification
		$type    = isset( $_POST['payment_request_type'] ) ? sanitize_key( wp_unslash( $_POST['payment_request_type'] ) ) : '';
		$gateway = isset( $_POST['payment_method'] ) ? sanitize_key( wp_unslash( $_POST['payment_method'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification
		if ( $this->is_express_payment( $type ) && in_array( $gateway, $this->express_gateways(), true ) ) {
			foreach ( $this->express_endpoints() as $endpoint ) {
				if ( doing_action( 'wc_ajax_' . $endpoint ) ) {
					return $validation_errors;
				}
			}
		}

		$context = $this->checkout_context();
		if ( ! wbc_captcha_service_manager()->verify( $context ) ) {
			$validation_errors->add( 'g-recaptcha_error', wbc_get_captcha_error_message( $context, 'invalid' ) );
		}

		return $validation_errors;
	}

	/**
	 * Let the block checkout send the token with the order.
	 */
	public function register_store_api_data() {
		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}
		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => 'checkout',
				'namespace'       => self::STORE_API_NAMESPACE,
				'schema_callback' => function () {
					return array(
						'token' => array(
							'description' => __( 'CAPTCHA response token.', 'buddypress-recaptcha' ),
							'type'        => 'string',
							'context'     => array(),
						),
					);
				},
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Block checkout: verify before the order is placed.
	 *
	 * Fires on the Store API checkout POST (place order) and also on PUT
	 * (address/field updates while the shopper types), which never carries a
	 * token, so only POST is checked.
	 *
	 * @param WC_Order        $order   Order being placed.
	 * @param WP_REST_Request $request Store API request.
	 * @throws \Automattic\WooCommerce\StoreApi\Exceptions\RouteException When the CAPTCHA fails.
	 */
	public function validate_store_api_checkout( $order, $request ) {
		if ( 'POST' !== $request->get_method() ) {
			return;
		}

		// Pay-for-order (POST /checkout/{id}) pays an order that already exists;
		// that page shows no widget, so checking it would fail every payment.
		if ( $request->get_param( 'id' ) ) {
			return;
		}

		// Express orders: only gateways that really have express buttons, and
		// those gateways still need their own payment token to charge.
		$gateway = (string) $request->get_param( 'payment_method' );
		foreach ( in_array( $gateway, $this->express_gateways(), true ) ? (array) $request->get_param( 'payment_data' ) : array() as $item ) {
			if ( is_array( $item ) && isset( $item['key'], $item['value'] )
				&& in_array( $item['key'], array( 'payment_request_type', 'express_payment_type' ), true )
				&& $this->is_express_payment( sanitize_key( $item['value'] ) ) ) {
				return;
			}
		}

		$extensions = (array) $request->get_param( 'extensions' );
		$token      = isset( $extensions[ self::STORE_API_NAMESPACE ]['token'] ) ? sanitize_text_field( (string) $extensions[ self::STORE_API_NAMESPACE ]['token'] ) : '';
		$context    = $this->checkout_context();

		if ( ! wbc_captcha_service_manager()->verify( $context, $token ) ) {
			// The Store API returns this as JSON and the checkout renders it as text,
			// so it is stripped to plain text; HTML-escaping would show entities.
			throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
				'wbc_captcha_invalid',
				wp_strip_all_tags( wbc_get_captcha_error_message( $context, 'invalid' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				400
			);
		}
	}
}
