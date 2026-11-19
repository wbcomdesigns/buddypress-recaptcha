/**
 * Make the CAPTCHA widget follow the site's dark mode and fit narrow forms.
 *
 * The provider widgets (reCAPTCHA, hCaptcha, Turnstile) render inside a
 * third-party iframe that no stylesheet of ours can reach. The only supported
 * way to darken them is the `data-theme` attribute, which each provider reads
 * when it renders the widget.
 *
 * Dark mode here is the site owner's own toggle, and it is a client-side state
 * (`data-bx-mode="dark"` on <html>, or the BuddyX dark body class), so PHP
 * cannot know it at render time. This runs before the provider script gets to
 * the element and flips the attribute, so the widget renders dark on a dark page
 * instead of punching a white hole through it, and redraws it when the visitor
 * toggles the mode on an open page.
 *
 * Deliberately does NOT consult prefers-color-scheme: the widget follows the
 * site's dark mode, not the visitor's operating system.
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.1.0
 */
( function () {
	'use strict';

	var SELECTOR = '.g-recaptcha, .h-captcha, .cf-turnstile';

	function isDark() {
		var root = document.documentElement;

		return (
			'dark' === root.getAttribute( 'data-bx-mode' ) ||
			root.classList.contains( 'dark-mode' ) ||
			document.body && document.body.classList.contains( 'buddyx-dark-theme' )
		);
	}

	// A widget that already holds a solved response; redrawing would make the
	// visitor solve it again, so it keeps its look until the next page load.
	function isSolved( el ) {
		var field = el.querySelector( '[name="g-recaptcha-response"], [name="h-captcha-response"]' );
		return !! ( field && field.value );
	}

	function applyTheme( el, want ) {
		if ( ! el.hasAttribute( 'data-wbc-theme-auto' ) ) {
			// Never override an explicit dark choice the site owner made in the settings.
			if ( 'dark' === el.getAttribute( 'data-theme' ) ) {
				return;
			}
			el.setAttribute( 'data-wbc-theme-auto', '1' );
		}

		if ( want === el.getAttribute( 'data-theme' ) ) {
			return;
		}

		// Not drawn yet: the provider reads the new value when it draws.
		var drawn = !! el.querySelector( 'iframe, textarea, input' );
		if ( drawn && isSolved( el ) ) {
			return;
		}

		el.setAttribute( 'data-theme', want );

		// Already drawn (the visitor toggled the mode): redraw so it follows.
		if ( drawn && window.wbcCaptcha && 'function' === typeof window.wbcCaptcha.rerender ) {
			window.wbcCaptcha.rerender( el );
		}
	}

	// Width of the normal-size widget as drawn (304px, scaled to 0.89 by the
	// service markup). Where that would run past the form (a phone, a sidebar,
	// a themed login box) the widget gets the provider's own compact size.
	var NORMAL_WIDTH = 271;

	function fitWidth( el ) {
		if ( el.hasAttribute( 'data-wbc-fit' ) || el.querySelector( 'iframe' ) ) {
			return;
		}
		el.setAttribute( 'data-wbc-fit', '1' );

		if ( 'normal' !== ( el.getAttribute( 'data-size' ) || 'normal' ) ) {
			return;
		}

		// Clipped means past the form's edge or the screen's, not merely into the
		// form's padding, which the normal widget may use.
		var form  = el.closest( 'form' );
		var limit = document.documentElement.clientWidth;
		if ( form ) {
			limit = Math.min( limit, form.getBoundingClientRect().right );
		}
		// The wrapper's left edge: hCaptcha's wrapper centres an inline-block
		// widget that has no width until it draws.
		var left = ( el.parentElement || el ).getBoundingClientRect().left;

		if ( left + NORMAL_WIDTH > limit ) {
			el.setAttribute( 'data-size', 'compact' );
		}
	}

	function sweep() {
		var want  = isDark() ? 'dark' : 'light';
		var nodes = document.querySelectorAll( SELECTOR );
		for ( var i = 0; i < nodes.length; i++ ) {
			fitWidth( nodes[ i ] );
			applyTheme( nodes[ i ], want );
		}
	}

	// Catch widgets that are already in the markup, and any that arrive later
	// (the AJAX login widget re-renders its captcha after a failed attempt).
	// Run now for widgets already parsed (this loads in the footer, before the
	// provider draws), and again once the whole document is in.
	sweep();
	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', sweep );
	}

	if ( window.MutationObserver ) {
		new window.MutationObserver( sweep ).observe( document.documentElement, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [ 'data-bx-mode', 'class' ],
		} );
	}
} )();
