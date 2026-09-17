/**
 * AJAX Login Widget JavaScript
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

(function($) {
	'use strict';

	/**
	 * Reset the active CAPTCHA provider's widget so a fresh challenge is shown
	 * after a failed AJAX login attempt. Each provider exposes a different
	 * global, so we dispatch on wbcAjaxLogin.recaptchaType.
	 */
	function resetActiveCaptcha() {
		var type = ( window.wbcAjaxLogin && wbcAjaxLogin.recaptchaType ) || '';

		if ( type === 'recaptcha-v2' && typeof grecaptcha !== 'undefined' ) {
			try {
				grecaptcha.reset();
			} catch ( e ) {
				// Provider not yet ready; safe to ignore.
			}
			return;
		}
		if ( type === 'hcaptcha' && typeof hcaptcha !== 'undefined' ) {
			try {
				hcaptcha.reset();
			} catch ( e ) {
				// Provider not yet ready; safe to ignore.
			}
			return;
		}
		if ( type === 'turnstile' && typeof turnstile !== 'undefined' ) {
			try {
				turnstile.reset();
			} catch ( e ) {
				// Provider not yet ready; safe to ignore.
			}
		}
	}

	/**
	 * Resolve once the form carries a fresh reCAPTCHA v3 token.
	 *
	 * A v3 token is single-use and the form is sent over AJAX, so the generic v3
	 * submit handler (which holds a normal form until its token lands) does not
	 * apply here: fetch a new token for every attempt, including a retry after a
	 * failed login. Other providers put their response in the form themselves.
	 *
	 * @param {jQuery} $form The login form.
	 * @return {Promise} Resolves when the form is ready to serialize.
	 */
	function withFreshV3Token( $form ) {
		var field = $form.find( 'input[id^="wbc_recaptcha_"][id$="_token"]' ).get( 0 );
		if ( ! field || ! window.wbcRecaptchaV3 || typeof window.wbcRecaptchaV3.refresh !== 'function' ) {
			return Promise.resolve();
		}
		return window.wbcRecaptchaV3.refresh( field.id );
	}

	/**
	 * Handle AJAX login form submission
	 */
	$( document ).ready( function() {
		$( document ).on( 'submit', '#wbc-ajax-login-form', function( e ) {
			e.preventDefault();

			var $form = $( this );
			var $button = $form.find( '.wbc-login-button' );
			var $buttonText = $button.find( '.wbc-button-text' );
			var $buttonLoader = $button.find( '.wbc-button-loader' );
			var $messages = $form.find( '.wbc-form-messages' );

			// Disable button and show loader
			$button.prop( 'disabled', true );
			$buttonText.hide();
			$buttonLoader.show();
			$messages.html( '' ).removeClass( 'wbc-error wbc-success' );

			withFreshV3Token( $form ).then( function() {
				// Prepare form data
				var formData = $form.serialize();

				// Make AJAX request
				$.ajax({
					url: wbcAjaxLogin.ajaxurl,
					type: 'POST',
					data: formData,
					success: function( response ) {
						if ( response.success ) {
							// Show success message
							$messages
								.addClass( 'wbc-success' )
								.html( '<p>' + response.data.message + '</p>' );

							// Redirect after a short delay
							setTimeout( function() {
								window.location.href = response.data.redirect_to;
							}, 1000 );
						} else {
							// Show error message
							$messages
								.addClass( 'wbc-error' )
								.html( '<p>' + response.data.message + '</p>' );

							// Re-enable button
							$button.prop( 'disabled', false );
							$buttonText.show();
							$buttonLoader.hide();

							// Reset CAPTCHA widget for whichever provider is active.
							resetActiveCaptcha();
						}
					},
					error: function( xhr, status, error ) {
						// Show error message
						$messages
							.addClass( 'wbc-error' )
							.html( '<p>' + wbcAjaxLogin.errorMessage + '</p>' );

						// Re-enable button
						$button.prop( 'disabled', false );
						$buttonText.show();
						$buttonLoader.hide();

						// Reset CAPTCHA widget for whichever provider is active.
						resetActiveCaptcha();
					}
				});
			} );
		});
	});

})( jQuery );
