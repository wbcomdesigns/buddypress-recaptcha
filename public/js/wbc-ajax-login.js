/**
 * AJAX Login Widget JavaScript
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

(function($) {
	'use strict';

	/**
	 * Give the login form a fresh CAPTCHA after a failed attempt. Only the widget
	 * inside this form is reset, never another form's CAPTCHA on the same page.
	 *
	 * @param {jQuery} $form The login form.
	 */
	function resetFormCaptcha( $form ) {
		if ( window.wbcCaptcha && typeof window.wbcCaptcha.refresh === 'function' ) {
			window.wbcCaptcha.refresh( $form );
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

							resetFormCaptcha( $form );
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

						resetFormCaptcha( $form );
					}
				});
			} );
		});
	});

})( jQuery );
