/**
 * Keep CAPTCHA widgets usable when a form is submitted or re-rendered without a
 * page load.
 *
 * Provider tokens are single-use. After a failed AJAX submit the widget still
 * looks solved but its token is spent, so the retry fails. And when a plugin
 * replaces the form's HTML (WooCommerce checkout refresh, Gravity Forms AJAX
 * pages), the new widget container is empty because providers only auto-render
 * once, on page load.
 *
 * window.wbcCaptcha.refresh( root ) fixes both for every provider: widgets inside
 * root that were already rendered are reset, empty ones are rendered from their
 * data-* attributes. Only widgets inside root are touched, never a different
 * form's CAPTCHA elsewhere on the page.
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.2.3
 */
( function () {
	'use strict';

	// Accepts an element, a jQuery object or a selector; anything else means
	// the whole document.
	function toElement( root ) {
		if ( root && root.jquery ) {
			root = root.get( 0 );
		} else if ( 'string' === typeof root ) {
			root = document.querySelector( root );
		}
		return root && typeof root.querySelectorAll === 'function' ? root : document;
	}

	function callbackFor( el ) {
		var name = el.getAttribute( 'data-callback' );
		return name && typeof window[ name ] === 'function' ? window[ name ] : undefined;
	}

	function params( el ) {
		var p = { sitekey: el.getAttribute( 'data-sitekey' ) };
		var cb = callbackFor( el );
		if ( cb ) {
			p.callback = cb;
		}
		[ 'theme', 'size' ].forEach( function ( key ) {
			var value = el.getAttribute( 'data-' + key );
			if ( value ) {
				p[ key ] = value;
			}
		} );
		return p;
	}

	// reCAPTCHA v2: the response textarea id carries the widget id
	// ("g-recaptcha-response" is widget 0, "g-recaptcha-response-3" is widget 3).
	function refreshRecaptcha( scope ) {
		if ( typeof window.grecaptcha === 'undefined' || typeof window.grecaptcha.render !== 'function' ) {
			return;
		}
		scope.querySelectorAll( '.g-recaptcha' ).forEach( function ( el ) {
			var field = el.querySelector( 'textarea[name="g-recaptcha-response"]' );
			try {
				if ( field ) {
					var match = /-(\d+)$/.exec( field.id );
					window.grecaptcha.reset( match ? parseInt( match[ 1 ], 10 ) : 0 );
				} else if ( el.getAttribute( 'data-sitekey' ) ) {
					window.grecaptcha.render( el, params( el ) );
				}
			} catch ( e ) {} // Provider not ready yet; it renders on its own load.
		} );
	}

	// hCaptcha: the iframe carries the widget id.
	function refreshHcaptcha( scope ) {
		if ( typeof window.hcaptcha === 'undefined' ) {
			return;
		}
		scope.querySelectorAll( '.h-captcha' ).forEach( function ( el ) {
			var frame = el.querySelector( 'iframe[data-hcaptcha-widget-id]' );
			try {
				if ( frame ) {
					window.hcaptcha.reset( frame.getAttribute( 'data-hcaptcha-widget-id' ) );
				} else if ( el.getAttribute( 'data-sitekey' ) ) {
					window.hcaptcha.render( el, params( el ) );
				}
			} catch ( e ) {}
		} );
	}

	// Turnstile accepts the container element itself.
	function refreshTurnstile( scope ) {
		if ( typeof window.turnstile === 'undefined' ) {
			return;
		}
		scope.querySelectorAll( '.cf-turnstile' ).forEach( function ( el ) {
			try {
				if ( el.querySelector( 'iframe' ) || el.querySelector( 'input[name="cf-turnstile-response"]' ) ) {
					window.turnstile.reset( el );
				} else if ( el.getAttribute( 'data-sitekey' ) ) {
					window.turnstile.render( el, params( el ) );
				}
			} catch ( e ) {}
		} );
	}

	// ALTCHA is a custom element and renders itself; only reset spent solutions.
	function refreshAltcha( scope ) {
		scope.querySelectorAll( 'altcha-widget' ).forEach( function ( el ) {
			if ( typeof el.reset === 'function' ) {
				try {
					el.reset();
				} catch ( e ) {}
			}
		} );
	}

	// reCAPTCHA v3 keeps a token in a hidden field; fetch a fresh one.
	function refreshRecaptchaV3( scope ) {
		if ( ! window.wbcRecaptchaV3 || typeof window.wbcRecaptchaV3.refresh !== 'function' ) {
			return;
		}
		scope.querySelectorAll( 'input[id^="wbc_recaptcha_"][id$="_token"]' ).forEach( function ( field ) {
			try {
				window.wbcRecaptchaV3.refresh( field.id );
			} catch ( e ) {}
		} );
	}

	function refresh( root ) {
		var scope = toElement( root );
		refreshRecaptcha( scope );
		refreshHcaptcha( scope );
		refreshTurnstile( scope );
		refreshAltcha( scope );
		refreshRecaptchaV3( scope );
	}

	window.wbcCaptcha = window.wbcCaptcha || {};
	window.wbcCaptcha.refresh = refresh;

	// Form plugins that submit over AJAX and report failure with a DOM event.
	// Each event's target is (or is inside) the form that failed.
	function closestForm( target ) {
		if ( ! target || ! target.closest ) {
			return target;
		}
		return target.closest( 'form' ) || target.closest( '.wpcf7' ) || target;
	}

	// Contact Form 7.
	[ 'wpcf7invalid', 'wpcf7spam', 'wpcf7mailfailed' ].forEach( function ( type ) {
		document.addEventListener( type, function ( event ) {
			refresh( closestForm( event.target ) );
		} );
	} );

	// jQuery-triggered events: WPForms, Forminator, Elementor Pro, Gravity Forms,
	// WooCommerce checkout.
	if ( window.jQuery ) {
		var $ = window.jQuery;

		$( document ).on( 'wpformsAjaxSubmitFailed wpformsAjaxSubmitActionRequired', 'form', function () {
			refresh( this );
		} );
		$( document ).on( 'forminator:form:submit:failed', function ( event, form ) {
			refresh( form || closestForm( event.target ) );
		} );
		$( document ).on( 'error', '.elementor-form', function () {
			refresh( this );
		} );
		$( document ).on( 'gform_post_render', function ( event, formId ) {
			refresh( document.getElementById( 'gform_wrapper_' + formId ) || document );
		} );
		$( document.body ).on( 'updated_checkout checkout_error', function () {
			refresh( document.querySelector( 'form.checkout' ) || document );
		} );

		// Ninja Forms draws the form (and our widget) in JavaScript after page
		// load and posts only its own fields, so: render the widget once the
		// form is ready, send the token as submission extra data, and reset the
		// widget when the server answers with errors.
		$( document ).on( 'nfFormReady', function ( event, layoutView ) {
			if ( ! window.nfRadio || ! layoutView || ! layoutView.model ) {
				return;
			}
			var el = toElement( layoutView.$el || layoutView.el );
			var channel = window.nfRadio.channel( 'form-' + layoutView.model.get( 'id' ) );

			refresh( el );
			channel.on( 'before:submit', function () {
				var box = el.querySelector( '.wbc-nf-captcha' );
				if ( ! box ) {
					return;
				}
				var name = box.getAttribute( 'data-wbc-field' );
				var field = box.querySelector( 'input[name="' + name + '"], textarea[name="' + name + '"]' );
				channel.request( 'add:extra', 'wbc_captcha', field ? field.value : '' );
			} );
			channel.on( 'submit:response', function ( response ) {
				if ( response && response.errors && Object.keys( response.errors ).length ) {
					refresh( el );
				}
			} );
		} );
	}
}() );
