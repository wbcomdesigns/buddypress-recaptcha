(function ($) {
	'use strict';

	/**
	 * Admin JavaScript for simplified BuddyPress reCAPTCHA settings
	 *
	 * Handles dynamic show/hide of service-specific settings based on
	 * the selected captcha service.
	 */

	/**
	 * Translated strings injected by PHP via wp_localize_script() on the
	 * 'recaptcha-for-buddypress-admin' handle (BPRC_Admin::enqueue_assets).
	 *
	 * Every literal passed to t() below has a matching __() twin in that
	 * localize array -- the literal is only a fallback for when the script is
	 * loaded without its localized data. Never add a user-facing string here
	 * without adding the same English text to the PHP array, or the POT
	 * scanner will never see it and it can never be translated.
	 */
	var strings = ( window.wbcAdminFields && window.wbcAdminFields.strings ) || {};

	/**
	 * Read a translated string, falling back to the English source.
	 *
	 * @param {string} key      Key in the localized strings array.
	 * @param {string} fallback English text mirroring the PHP __() value.
	 * @return {string} Translated string.
	 */
	function t( key, fallback ) {
		return strings[ key ] || fallback;
	}

	/**
	 * Fill a single %s placeholder.
	 *
	 * Keeps the whole phrase in one translatable string so translators control
	 * word order (never build a sentence by concatenating fragments).
	 *
	 * @param {string} template String containing one %s.
	 * @param {string} value    Replacement value.
	 * @return {string} Interpolated string.
	 */
	function sprintf1( template, value ) {
		return String( template ).replace( '%s', value );
	}

	$(document).ready(function () {
		// Initialize on page load
		wbcInitializeServiceSettings();

		// Add visual class to selected service option
		wbcUpdateSelectedVisual();

		// Handle service selection change - for radio buttons
		$('input[name="wbc_captcha_service"]').on('change', function() {
			if ($(this).is(':checked')) {
				wbcUpdateServiceSettings($(this).val());
				wbcUpdateSelectedVisual();
			}
		});

		// Handle service selection change - for select dropdown (backward compatibility)
		$('#wbc_captcha_service').on('change', function() {
			wbcUpdateServiceSettings($(this).val());
		});

		/**
		 * Initialize service settings visibility
		 */
		function wbcInitializeServiceSettings() {
			// Check for radio button first (new structure)
			var activeService = $('input[name="wbc_captcha_service"]:checked').val();

			// Fall back to select dropdown if no radio button is checked
			if (!activeService) {
				activeService = $('#wbc_captcha_service').val();
			}

			if (activeService) {
				wbcUpdateServiceSettings(activeService);
			}
		}

		/**
		 * Update visibility of service-specific settings
		 * @param {string} service The selected service ID
		 */
		function wbcUpdateServiceSettings(service) {
			// Hide all service key containers and show only the selected one
			$('.wbc-service-keys').addClass('wbc-hidden').removeClass('wbc-active');
			$('.wbc-service-keys-' + service).removeClass('wbc-hidden').addClass('wbc-active');

			// Update documentation section visibility
			$('.wbc-service-docs').addClass('wbc-hidden').removeClass('wbc-active');
			$('.wbc-service-docs-' + service).removeClass('wbc-hidden').addClass('wbc-active');
		}

		/**
		 * Update visual styling to show which service is selected
		 */
		function wbcUpdateSelectedVisual() {
			// Remove selected class from all service option labels
			$('.wbc-service-option').removeClass('wbc-selected');

			// Find the checked radio button and add selected class to its parent label
			var $checkedRadio = $('input[name="wbc_captcha_service"]:checked');
			if ($checkedRadio.length) {
				$checkedRadio.closest('.wbc-service-option').addClass('wbc-selected');
			}
		}

		/**
		 * Handle form validation
		 */
		$('form').on('submit', function(e) {
			var activeService = $('#wbc_captcha_service').val();
			var isValid = true;
			var errors = [];
			
			var siteKeyRequired   = t( 'siteKeyRequired', '%s Site Key is required' );
			var secretKeyRequired = t( 'secretKeyRequired', '%s Secret Key is required' );

			// Validate service-specific required fields
			switch(activeService) {
				case 'recaptcha_v2':
					var providerV2 = t( 'providerV2', 'Google reCAPTCHA v2' );
					if (!$('#wc_settings_tab_recapcha_site_key').val()) {
						errors.push( sprintf1( siteKeyRequired, providerV2 ) );
						isValid = false;
					}
					if (!$('#wc_settings_tab_recapcha_secret_key').val()) {
						errors.push( sprintf1( secretKeyRequired, providerV2 ) );
						isValid = false;
					}
					break;

				case 'recaptcha_v3':
					var providerV3 = t( 'providerV3', 'Google reCAPTCHA v3' );
					if (!$('#wc_settings_tab_recapcha_site_key_v3').val()) {
						errors.push( sprintf1( siteKeyRequired, providerV3 ) );
						isValid = false;
					}
					if (!$('#wc_settings_tab_recapcha_secret_key_v3').val()) {
						errors.push( sprintf1( secretKeyRequired, providerV3 ) );
						isValid = false;
					}
					break;

				case 'turnstile':
					var providerTurnstile = t( 'providerTurnstile', 'Cloudflare Turnstile' );
					if (!$('#wbc_turnstile_site_key').val()) {
						errors.push( sprintf1( siteKeyRequired, providerTurnstile ) );
						isValid = false;
					}
					if (!$('#wbc_turnstile_secret_key').val()) {
						errors.push( sprintf1( secretKeyRequired, providerTurnstile ) );
						isValid = false;
					}
					break;
			}
			
			// Show validation errors if any
			if (!isValid) {
				e.preventDefault();
				
				// Remove existing error messages
				$('.wbc-validation-error').remove();
				
				// Display error message
				var $errorNotice = $( '<div class="notice notice-error wbc-validation-error"><p><strong></strong></p><ul></ul></div>' );
				$errorNotice.find( 'strong' ).text( t( 'fixErrors', 'Please fix the following errors:' ) );
				var $errorList = $errorNotice.find( 'ul' );
				errors.forEach(function(error) {
					$errorList.append( $( '<li></li>' ).text( error ) );
				});

				$('h1').first().after($errorNotice);
				
				// Scroll to top to show errors
				$('html, body').animate({
					scrollTop: 0
				}, 'fast');
			}
		});
		
		/**
		 * Add helper links for getting API keys
		 */
		function wbcAddHelperLinks() {
			/**
			 * Build a "Get your keys here" link for a provider's console.
			 *
			 * @param {string} url Provider console URL.
			 * @return {jQuery} Link element with translated text.
			 */
			function wbcKeysLink( url ) {
				return $( '<a target="_blank"></a>' )
					.attr( 'href', url )
					.text( t( 'getKeysHere', 'Get your keys here' ) );
			}

			// reCAPTCHA v2 helper
			$('#wc_settings_tab_recapcha_site_key').closest('tr').find('.description').append(
				' ', wbcKeysLink( 'https://www.google.com/recaptcha/admin/create' )
			);

			// reCAPTCHA v3 helper
			$('#wc_settings_tab_recapcha_site_key_v3').closest('tr').find('.description').append(
				' ', wbcKeysLink( 'https://www.google.com/recaptcha/admin/create' )
			);

			// Turnstile helper
			$('#wbc_turnstile_site_key').closest('tr').find('.description').append(
				' ', wbcKeysLink( 'https://dash.cloudflare.com/sign-up?to=/:account/turnstile' )
			);
		}

		// Add helper links on page load
		wbcAddHelperLinks();

		/**
		 * Generate random HMAC key for ALTCHA
		 */
		$('.wbc-generate-hmac-key').on('click', function(e) {
			e.preventDefault();

			// Generate a random 64-character hex string (32 bytes)
			var key = '';
			var chars = '0123456789abcdef';
			for (var i = 0; i < 64; i++) {
				key += chars.charAt(Math.floor(Math.random() * chars.length));
			}

			// Set the generated key
			$('#wbc_altcha_hmac_key').val(key);

			// Show feedback
			var $btn = $(this);
			var originalText = $btn.text();
			$btn.text( t( 'keyGenerated', 'Key Generated!' ) ).prop('disabled', true);
			setTimeout(function() {
				$btn.text(originalText).prop('disabled', false);
			}, 2000);
		});
		
		/**
		 * Handle tab switching for better UX
		 */
		$('.nav-tab').on('click', function() {
			// Save a flag to remember the active tab
			if (window.localStorage) {
				localStorage.setItem('wbc_active_tab', $(this).attr('href'));
			}
		});
		
		// Restore active tab on page load
		if (window.localStorage) {
			var activeTab = localStorage.getItem('wbc_active_tab');
			if (activeTab) {
				// Properly escape the selector
				try {
					var escapedTab = activeTab.replace(/([!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~])/g, '\\$1');
					var $tabLink = $('a[href="' + escapedTab + '"]');
					if ($tabLink.length) {
						$tabLink.click();
					}
				} catch(e) {
					// If selector fails, just ignore
					console.log('Could not restore tab:', e);
				}
			}
		}
		
		/**
		 * Add masked display and copy to clipboard functionality for keys
		 */
		$('input[type="text"][id*="site_key"], input[type="password"][id*="secret_key"]').each(function() {
			var $input = $(this);
			var $wrapper = $input.closest('td');
			var originalValue = $input.val();
			var isMasked = true;
			var realValue = '';

			// For secret keys, create a masked display system
			if ($input.attr('type') === 'password') {
				// Change to text input for consistent styling
				$input.attr('type', 'text');
				$input.addClass('wbc-secret-key-input');

				// The CSS handles styling - no need for inline styles

				// Create masked value showing only last 4-6 characters
				function wbcGetMaskedValue(value) {
					if (!value || value === '') return '';

					// Show last 4-6 characters based on length
					var visibleChars = 4;
					if (value.length >= 40) {
						visibleChars = 6;
					} else if (value.length >= 20) {
						visibleChars = 5;
					}

					if (value.length <= visibleChars) {
						return value;
					}

					// Use bullet character for masking
					var masked = '';
					for (var i = 0; i < value.length - visibleChars; i++) {
						masked += '•';
					}
					masked += value.slice(-visibleChars);
					return masked;
				}

				// Set initial state based on whether field has value
				if (originalValue) {
					realValue = originalValue;
					$input.val(wbcGetMaskedValue(originalValue));
					$input.data('real-value', originalValue);
					isMasked = true;
				} else {
					// No initial value - allow direct typing
					isMasked = false;
				}

				// Handle focus - unmask for editing if field has value
				$input.on('focus', function() {
					if (isMasked && realValue) {
						// Unmask for editing
						$input.val(realValue);
						isMasked = false;
					}
				});

				// Handle blur - mask value if present
				$input.on('blur', function() {
					var currentValue = $(this).val();
					if (currentValue) {
						realValue = currentValue;
						$input.data('real-value', realValue);
						$input.val(wbcGetMaskedValue(realValue));
						isMasked = true;
					}
				});

				// Handle input changes - track real value
				$input.on('input paste', function() {
					realValue = $(this).val();
					$input.data('real-value', realValue);
				});

				// Add toggle visibility button
				var $toggleBtn = $('<button type="button" class="button button-secondary wbc-toggle-visibility">👁</button>')
					.attr( 'title', t( 'toggleVisibility', 'Toggle visibility' ) );
				$input.after($toggleBtn);

				$toggleBtn.on('click', function(e) {
					e.preventDefault();

					if (isMasked && realValue) {
						// Show real value
						$input.val(realValue);
						isMasked = false;
						$(this).attr( 'title', t( 'hideSecretKey', 'Hide Secret Key' ) );
						$(this).html('🙈');
					} else if (!isMasked && realValue) {
						// Hide value
						$input.val(wbcGetMaskedValue(realValue));
						isMasked = true;
						$(this).attr( 'title', t( 'showSecretKey', 'Show Secret Key' ) );
						$(this).html('👁');
					}

					// Focus the input after toggling
					$input.focus();
				});

				// Store the real value before form submission
				$input.closest('form').on('submit', function() {
					if (isMasked && realValue) {
						$input.val(realValue);
					}
				});
			}

			// Add copy button for all key fields
			var $copyBtn = $('<button type="button" class="button button-secondary wbc-copy-key">📋</button>')
				.attr( 'title', t( 'copyToClipboard', 'Copy to Clipboard' ) );

			$wrapper.find('.wbc-toggle-visibility').length ?
				$wrapper.find('.wbc-toggle-visibility').after($copyBtn) :
				$input.after($copyBtn);

			$copyBtn.on('click', function(e) {
				e.preventDefault();
				var valueToCopy;

				// For masked fields, copy the real value
				if ($input.hasClass('wbc-secret-key-input')) {
					valueToCopy = $input.data('real-value') || realValue || $input.val();
				} else {
					valueToCopy = $input.val();
				}

				// Use modern clipboard API if available
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(valueToCopy).then(function() {
						// Success feedback
						var originalTitle = $copyBtn.attr('title');
						$copyBtn.attr( 'title', t( 'copied', 'Copied!' ) );
						$copyBtn.html('✅');
						setTimeout(function() {
							$copyBtn.html('📋');
							$copyBtn.attr('title', originalTitle);
						}, 2000);
					});
				} else {
					// Fallback for older browsers
					var $tempInput = $('<textarea style="position:absolute;left:-9999px;">');
					$tempInput.val(valueToCopy);
					$('body').append($tempInput);
					$tempInput.select();
					document.execCommand('copy');
					$tempInput.remove();

					// Success feedback
					var originalTitle = $copyBtn.attr('title');
					$copyBtn.attr( 'title', t( 'copied', 'Copied!' ) );
					$copyBtn.html('✅');
					setTimeout(function() {
						$copyBtn.html('📋');
						$copyBtn.attr('title', originalTitle);
					}, 2000);
				}
			});
		});
		
		/**
		 * Add collapsible sections for better organization
		 */
		$('.form-table').each(function() {
			var $table = $(this);
			var $title = $table.prev('h2');
			
			if ($title.length && !$title.hasClass('wbc-collapsible')) {
				$title.addClass('wbc-collapsible');
				$title.css({
					'cursor': 'pointer',
					'user-select': 'none'
				});
				
				// Add toggle indicator
				$title.prepend('<span class="dashicons dashicons-arrow-down-alt2" style="margin-right: 5px;"></span>');
				
				// Handle click to toggle
				$title.on('click', function() {
					var $icon = $(this).find('.dashicons');
					if ($table.is(':visible')) {
						$table.slideUp();
						$icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
					} else {
						$table.slideDown();
						$icon.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
					}
				});
			}
		});
	});

})(jQuery);