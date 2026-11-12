/**
 * WooCommerce block checkout: send the CAPTCHA token with the order.
 *
 * The block checkout places orders through the Store API (no form post), so
 * the token is copied into the checkout's extension data the moment the
 * shopper presses Place order. The server verifies it before the order is
 * created. After a failed attempt the widget is reset so the retry gets a
 * fresh token.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.2.3
 */
( function () {
	'use strict';

	var STORE = 'wc/store/checkout';
	var NAMESPACE = 'wbc-captcha';

	if ( ! window.wp || ! window.wp.data ) {
		return;
	}

	var data = window.wp.data;
	var wasBeforeProcessing = false;
	var hadError = false;

	function wrapper() {
		return document.querySelector( '.wbc-checkout-captcha-wrapper' );
	}

	function token() {
		var el = wrapper();
		if ( ! el ) {
			return '';
		}
		// input/textarea only: the ALTCHA custom element carries the same name.
		var name = el.getAttribute( 'data-wbc-field' );
		var field = el.querySelector( 'input[name="' + name + '"], textarea[name="' + name + '"]' );
		return field ? field.value : '';
	}

	data.subscribe( function () {
		var checkout = data.select( STORE );
		if ( ! checkout ) {
			return;
		}

		// Record state BEFORE dispatching: the dispatch re-runs this subscriber.
		var beforeProcessing = checkout.isBeforeProcessing();
		var enteredProcessing = beforeProcessing && ! wasBeforeProcessing;
		wasBeforeProcessing = beforeProcessing;
		if ( enteredProcessing ) {
			data.dispatch( STORE ).setExtensionData( NAMESPACE, { token: token() } );
		}

		var hasError = checkout.hasError();
		var newError = hasError && ! hadError;
		hadError = hasError;
		if ( newError && window.wbcCaptcha && wrapper() ) {
			window.wbcCaptcha.refresh( wrapper() );
		}
	}, STORE );
}() );
