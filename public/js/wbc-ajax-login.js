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

		if ( type === 'recaptcha_v2_checkbox' && typeof grecaptcha !== 'undefined' ) {
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
		});
	});

})( jQuery );
