/**
 * Wbcom CAPTCHA Manager: Admin card-panel JS (toast + confirm helpers).
 *
 * Skill Part 6 rules 10 + 12: no browser alert()/confirm() anywhere.
 * Exposes window.bprcToast() and window.bprcConfirm() so this file and
 * future extensions share one feedback surface.
 *
 * The settings forms themselves are driven by the retained content
 * scripts (recaptcha-for-buddypress-admin.js + wbc-admin-dynamic.js) —
 * this file only owns the chrome-level feedback helpers.
 *
 * @since 2.1.0
 */
( function ( $ ) {
	'use strict';

	var i18n = ( window.bprcAdmin && window.bprcAdmin.i18n ) || {};

	/* ─── Toast ─────────────────────────────────────────────── */

	function getToastHost() {
		var host = document.querySelector( '.bprc-toast-host' );
		if ( ! host ) {
			host = document.createElement( 'div' );
			host.className = 'bprc-toast-host';
			document.body.appendChild( host );
		}
		return host;
	}

	function toast( message, tone ) {
		tone = tone || 'info';
		var host = getToastHost();
		var el   = document.createElement( 'div' );
		el.className   = 'bprc-toast bprc-toast--' + tone;
		el.setAttribute( 'role', 'status' );
		el.textContent = String( message );
		host.appendChild( el );

		requestAnimationFrame( function () {
			el.classList.add( 'bprc-toast--visible' );
		} );

		window.setTimeout( function () {
			el.classList.remove( 'bprc-toast--visible' );
			window.setTimeout( function () {
				if ( el.parentNode ) {
					el.parentNode.removeChild( el );
				}
			}, 250 );
		}, 3600 );
	}

	window.bprcToast = toast;

	/* ─── Confirm modal (returns a Promise) ──────────────────── */

	function confirmModal( opts ) {
		opts = opts || {};
		return new Promise( function ( resolve ) {
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'bprc-confirm-backdrop';

			var card = document.createElement( 'div' );
			card.className = 'bprc-confirm';
			card.setAttribute( 'role', 'dialog' );
			card.setAttribute( 'aria-modal', 'true' );

			var title = document.createElement( 'h2' );
			title.className   = 'bprc-confirm__title';
			title.textContent = opts.title || '';
			if ( opts.title ) { card.appendChild( title ); }

			var desc = document.createElement( 'p' );
			desc.className   = 'bprc-confirm__desc';
			desc.textContent = opts.message || i18n.confirmDanger || '';
			if ( opts.message || i18n.confirmDanger ) { card.appendChild( desc ); }

			var actions = document.createElement( 'div' );
			actions.className = 'bprc-confirm__actions';

			var cancelBtn = document.createElement( 'button' );
			cancelBtn.type        = 'button';
			cancelBtn.className    = 'bprc-btn bprc-btn-secondary';
			cancelBtn.textContent = opts.cancelLabel || i18n.confirmCancel || 'Cancel';

			var confirmBtn = document.createElement( 'button' );
			confirmBtn.type        = 'button';
			confirmBtn.className    = 'bprc-btn ' + ( 'danger' === opts.tone ? 'bprc-btn-danger' : 'bprc-btn-primary' );
			confirmBtn.textContent = opts.confirmLabel || i18n.confirmContinue || 'Continue';

			actions.appendChild( cancelBtn );
			actions.appendChild( confirmBtn );
			card.appendChild( actions );
			backdrop.appendChild( card );
			document.body.appendChild( backdrop );

			function cleanup( result ) {
				document.removeEventListener( 'keydown', onKey );
				if ( backdrop.parentNode ) {
					backdrop.parentNode.removeChild( backdrop );
				}
				resolve( result );
			}

			function onKey( e ) {
				if ( 'Escape' === e.key ) { cleanup( false ); }
				if ( 'Enter' === e.key ) { cleanup( true ); }
			}

			cancelBtn.addEventListener( 'click', function () { cleanup( false ); } );
			confirmBtn.addEventListener( 'click', function () { cleanup( true ); } );
			backdrop.addEventListener( 'click', function ( e ) {
				if ( e.target === backdrop ) { cleanup( false ); }
			} );
			document.addEventListener( 'keydown', onKey );
			confirmBtn.focus();
		} );
	}

	window.bprcConfirm = confirmModal;

	/* ─── Generic destructive-intent confirm interceptor ─────── */

	$( function () {
		$( document ).on( 'click', '[data-bprc-confirm]', function ( e ) {
			var $el = $( this );
			// Leave elements that own their own handler (data-action) alone.
			if ( $el.attr( 'data-action' ) ) { return; }
			if ( $el.data( 'bprc-confirm-ok' ) ) { return; }
			e.preventDefault();
			var message = $el.data( 'bprc-confirm' ) || i18n.confirmDanger;
			var tone    = $el.data( 'bprc-confirm-tone' ) || 'danger';
			confirmModal( { message: message, tone: tone } ).then( function ( ok ) {
				if ( ! ok ) { return; }
				$el.data( 'bprc-confirm-ok', true );
				if ( $el.is( 'a' ) ) {
					window.location.href = $el.attr( 'href' );
				} else if ( $el.is( 'button' ) || $el.is( 'input' ) ) {
					var form = $el.closest( 'form' ).get( 0 );
					if ( ! form ) { return; }
					var btnName  = $el.attr( 'name' ) || '';
					var btnValue = $el.attr( 'value' ) || '1';
					if ( btnName ) {
						var hidden   = document.createElement( 'input' );
						hidden.type  = 'hidden';
						hidden.name  = btnName;
						hidden.value = btnValue;
						form.appendChild( hidden );
					}
					form.submit();
				}
			} );
		} );
	} );

} )( jQuery );
