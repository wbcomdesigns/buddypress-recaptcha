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
		 * Generate random HMAC key for ALTCHA
		 */
		$('.wbc-generate-hmac-key').on('click', function(e) {
			e.preventDefault();

			// Generate a random 64-character hex string (32 bytes)
			var bytes = new Uint8Array(32);
			window.crypto.getRandomValues(bytes);
			var key = Array.prototype.map.call(bytes, function(b) { return ('0' + b.toString(16)).slice(-2); }).join('');

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
		 * Copy-to-clipboard button for every site key field (secrets are never printed back)
		 */
		$('input[type="text"][id*="site_key"]').each(function() {
			var $input = $(this);

			// Add copy button for all key fields
			var $copyBtn = $('<button type="button" class="button button-secondary wbc-copy-key">📋</button>')
				.attr( 'title', t( 'copyToClipboard', 'Copy to Clipboard' ) );

			$input.after($copyBtn);

			$copyBtn.on('click', function(e) {
				e.preventDefault();
				var valueToCopy = $input.val();

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