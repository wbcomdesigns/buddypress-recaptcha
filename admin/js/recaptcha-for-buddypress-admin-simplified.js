(function ($) {
	'use strict';

	/**
	 * Admin JavaScript for simplified BuddyPress reCAPTCHA settings
	 *
	 * Handles dynamic show/hide of service-specific settings based on
	 * the selected captcha service.
	 */

	$(document).ready(function () {
		// Initialize on page load
		initServiceSettings();
		
		// Handle service selection change
		$('#wbc_captcha_service').on('change', function() {
			updateServiceSettings($(this).val());
		});
		
		/**
		 * Initialize service settings visibility
		 */
		function initServiceSettings() {
			var activeService = $('#wbc_captcha_service').val();
			if (activeService) {
				updateServiceSettings(activeService);
			}
		}
		
		/**
		 * Update visibility of service-specific settings
		 * @param {string} service The selected service ID
		 */
		function updateServiceSettings(service) {
			// Handle traditional settings
			$('.wbc-service-settings').closest('tr').hide();
			$('.wbc-service-settings').closest('h2').hide();
			$('.wbc-service-field').closest('tr').hide();
			$('.wbc-service-specific').closest('tr').hide();
			
			// Show settings for the selected service
			$('.wbc-service-' + service).closest('tr').show();
			$('.wbc-service-' + service).closest('h2').show();
			
			// Handle card-based appearance settings
			if ($('.wbc-appearance-cards').length) {
				updateAppearanceCards(service);
			}
			
			// Update informational message based on service
			updateServiceInfo(service);
		}
		
		/**
		 * Update visibility of appearance cards based on selected service
		 * @param {string} service The selected service ID
		 */
		function updateAppearanceCards(service) {
			// Remove active class from all cards
			$('.wbc-settings-card').removeClass('active-service').addClass('inactive');
			
			// Show and activate the card for selected service
			$('.wbc-service-card-' + service).removeClass('inactive').addClass('active-service');
			
			// Animate the transition
			$('.wbc-service-card-' + service).hide().fadeIn(300);
		}
		
		/**
		 * Display service-specific information
		 * @param {string} service The selected service ID
		 */
		function updateServiceInfo(service) {
			// Remove any existing info messages
			$('.wbc-service-info').remove();
			
			var infoMessage = '';
			var infoClass = 'notice notice-info wbc-service-info';
			
			switch(service) {
				case 'recaptcha_v3':
					infoMessage = '<div class="' + infoClass + '"><p><strong>Google reCAPTCHA v3 Information:</strong></p>' +
						'<p>reCAPTCHA v3 runs in the background without user interaction. It uses a scoring system (0.0 to 1.0) to detect bots. ' +
						'Users won\'t see any challenge or checkbox - verification happens automatically.</p>' +
						'<p>Recommended score threshold: 0.5 (lower scores = stricter validation)</p></div>';
					break;
					
				case 'recaptcha_v2':
					infoMessage = '<div class="' + infoClass + '"><p><strong>Google reCAPTCHA v2 Information:</strong></p>' +
						'<p>reCAPTCHA v2 displays the familiar "I\'m not a robot" checkbox. Users may need to solve image challenges if suspicious activity is detected.</p></div>';
					break;
					
				case 'turnstile':
					infoMessage = '<div class="' + infoClass + '"><p><strong>Cloudflare Turnstile Information:</strong></p>' +
						'<p>Turnstile is Cloudflare\'s privacy-first CAPTCHA alternative. It provides strong bot protection without requiring users to solve puzzles.</p>' +
						'<p>Get your keys from the <a href="https://dash.cloudflare.com/sign-up?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a></p></div>';
					break;
			}
			
			if (infoMessage) {
				$('#wbc_captcha_service').closest('tr').after(infoMessage);
			}
		}
		
		/**
		 * Handle form validation
		 */
		$('form').on('submit', function(e) {
			var activeService = $('#wbc_captcha_service').val();
			var isValid = true;
			var errors = [];
			
			// Validate service-specific required fields
			switch(activeService) {
				case 'recaptcha_v2':
					if (!$('#wc_settings_tab_recapcha_site_key').val()) {
						errors.push('Google reCAPTCHA v2 Site Key is required');
						isValid = false;
					}
					if (!$('#wc_settings_tab_recapcha_secret_key').val()) {
						errors.push('Google reCAPTCHA v2 Secret Key is required');
						isValid = false;
					}
					break;
					
				case 'recaptcha_v3':
					if (!$('#wc_settings_tab_recapcha_site_key_v3').val()) {
						errors.push('Google reCAPTCHA v3 Site Key is required');
						isValid = false;
					}
					if (!$('#wc_settings_tab_recapcha_secret_key_v3').val()) {
						errors.push('Google reCAPTCHA v3 Secret Key is required');
						isValid = false;
					}
					break;
					
				case 'turnstile':
					if (!$('#wbc_turnstile_site_key').val()) {
						errors.push('Cloudflare Turnstile Site Key is required');
						isValid = false;
					}
					if (!$('#wbc_turnstile_secret_key').val()) {
						errors.push('Cloudflare Turnstile Secret Key is required');
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
				var errorHtml = '<div class="notice notice-error wbc-validation-error"><p><strong>Please fix the following errors:</strong></p><ul>';
				errors.forEach(function(error) {
					errorHtml += '<li>' + error + '</li>';
				});
				errorHtml += '</ul></div>';
				
				$('h1').first().after(errorHtml);
				
				// Scroll to top to show errors
				$('html, body').animate({
					scrollTop: 0
				}, 'fast');
			}
		});
		
		/**
		 * Add helper links for getting API keys
		 */
		function addHelperLinks() {
			// reCAPTCHA v2 helper
			$('#wc_settings_tab_recapcha_site_key').closest('tr').find('.description').append(
				' <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Get your keys here</a>'
			);
			
			// reCAPTCHA v3 helper
			$('#wc_settings_tab_recapcha_site_key_v3').closest('tr').find('.description').append(
				' <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Get your keys here</a>'
			);
			
			// Turnstile helper
			$('#wbc_turnstile_site_key').closest('tr').find('.description').append(
				' <a href="https://dash.cloudflare.com/sign-up?to=/:account/turnstile" target="_blank">Get your keys here</a>'
			);
		}
		
		// Add helper links on page load
		addHelperLinks();
		
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
			if (activeTab && $(activeTab).length) {
				$('a[href="' + activeTab + '"]').click();
			}
		}
		
		/**
		 * Add copy to clipboard functionality for keys
		 */
		$('input[type="text"][id*="site_key"], input[type="password"][id*="secret_key"]').each(function() {
			var $input = $(this);
			var $wrapper = $input.closest('td');
			
			// Add show/hide password toggle for secret keys
			if ($input.attr('type') === 'password') {
				var $toggle = $('<button type="button" class="button button-secondary wbc-toggle-password" style="margin-left: 5px;">Show</button>');
				$input.after($toggle);
				
				$toggle.on('click', function() {
					if ($input.attr('type') === 'password') {
						$input.attr('type', 'text');
						$toggle.text('Hide');
					} else {
						$input.attr('type', 'password');
						$toggle.text('Show');
					}
				});
			}
			
			// Add copy button for all key fields
			var $copyBtn = $('<button type="button" class="button button-secondary wbc-copy-key" style="margin-left: 5px;">Copy</button>');
			$wrapper.find('.wbc-toggle-password').length ? 
				$wrapper.find('.wbc-toggle-password').after($copyBtn) : 
				$input.after($copyBtn);
			
			$copyBtn.on('click', function() {
				// Temporarily show password if hidden
				var wasPassword = $input.attr('type') === 'password';
				if (wasPassword) {
					$input.attr('type', 'text');
				}
				
				// Copy to clipboard
				$input.select();
				document.execCommand('copy');
				
				// Restore password field
				if (wasPassword) {
					$input.attr('type', 'password');
				}
				
				// Show feedback
				$copyBtn.text('Copied!');
				setTimeout(function() {
					$copyBtn.text('Copy');
				}, 2000);
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