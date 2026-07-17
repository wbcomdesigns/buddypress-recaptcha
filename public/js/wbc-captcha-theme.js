/**
 * Make the CAPTCHA widget follow the site's dark mode.
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
 * instead of punching a white hole through it.
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

	function applyTheme( el ) {
		if ( ! el || el.getAttribute( 'data-wbc-theme-set' ) ) {
			return;
		}

		// Never override an explicit choice the site owner made in the settings.
		var existing = el.getAttribute( 'data-theme' );
		if ( 'dark' === existing ) {
			return;
		}

		el.setAttribute( 'data-theme', 'dark' );
		el.setAttribute( 'data-wbc-theme-set', '1' );
	}

	function sweep() {
		if ( ! isDark() ) {
			return;
		}

		var nodes = document.querySelectorAll( SELECTOR );
		for ( var i = 0; i < nodes.length; i++ ) {
			applyTheme( nodes[ i ] );
		}
	}

	// Catch widgets that are already in the markup, and any that arrive later
	// (the AJAX login widget re-renders its captcha after a failed attempt).
	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', sweep );
	} else {
		sweep();
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
