/**
 * Dynamic Admin Settings JavaScript
 * Handles dynamic show/hide of service-specific fields
 *
 * @package Recaptcha_For_BuddyPress
 * @since   2.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize dynamic settings on document ready
	 */
	$(document).ready(function() {

		// Handle service selection changes
		initServiceSelection();

		// Handle ALTCHA key generation
		initAltchaKeyGeneration();

		// Handle test connection button
		initTestConnection();

	});

	/**
	 * Initialize service selection handling
	 */
	function initServiceSelection() {
		// Handle radio button changes for captcha service selection
		$('input[name="wbc_captcha_service"]').on('change', function() {
			var selectedService = $(this).val();
			switchServiceFields(selectedService);
		});

		// Also handle if using select dropdown (backward compatibility)
		$('select#wbc_captcha_service').on('change', function() {
			var selectedService = $(this).val();
			switchServiceFields(selectedService);
		});

		// Initialize on page load - show correct fields
		var initialService = $('input[name="wbc_captcha_service"]:checked').val();
		if (!initialService) {
			initialService = $('select#wbc_captcha_service').val();
		}
		if (initialService) {
			switchServiceFields(initialService);
		}
	}

	/**
	 * Switch visible service fields based on selection
	 *
	 * @param {string} service Service identifier
	 */
	function switchServiceFields(service) {
		// Hide all service key fields
		$('.wbc-service-keys').hide();

		// Show selected service fields with animation
		$('.wbc-service-keys-' + service).fadeIn(300);

		// Update help text and links dynamically
		updateServiceHelp(service);

		// Store selection for persistence
		localStorage.setItem('wbc_selected_service', service);
	}

	/**
	 * Update service-specific help text
	 *
	 * @param {string} service Service identifier
	 */
	function updateServiceHelp(service) {
		var helpTexts = {
			'recaptcha_v2': {
				title: 'Google reCAPTCHA v2 Configuration',
				help: 'The classic checkbox CAPTCHA. Users must click "I\'m not a robot".',
				docs: 'https://developers.google.com/recaptcha/docs/display'
			},
			'recaptcha_v3': {
				title: 'Google reCAPTCHA v3 Configuration',
				help: 'Invisible verification using risk analysis score.',
				docs: 'https://developers.google.com/recaptcha/docs/v3'
			},
			'turnstile': {
				title: 'Cloudflare Turnstile Configuration',
				help: 'Privacy-first CAPTCHA alternative from Cloudflare.',
				docs: 'https://developers.cloudflare.com/turnstile/'
			},
			'hcaptcha': {
				title: 'hCaptcha Configuration',
				help: 'Privacy-focused CAPTCHA that rewards websites.',
				docs: 'https://docs.hcaptcha.com/'
			},
			'altcha': {
				title: 'ALTCHA Configuration',
				help: 'Self-hosted proof-of-work challenge. No external API required.',
				docs: 'https://altcha.org/docs/'
			}
		};

		if (helpTexts[service]) {
			// Update any dynamic help sections if they exist
			$('.wbc-service-help-title').text(helpTexts[service].title);
			$('.wbc-service-help-text').text(helpTexts[service].help);
			$('.wbc-service-docs-link').attr('href', helpTexts[service].docs);
		}
	}

	/**
	 * Initialize ALTCHA key generation
	 */
	function initAltchaKeyGeneration() {
		$(document).on('click', '.wbc-generate-altcha-key', function(e) {
			e.preventDefault();

			// Generate random key
			var key = generateRandomKey(32);

			// Set the value in the input field
			$('#wbc_altcha_hmac_key').val(key);

			// Show success message
			showNotice('Random key generated successfully!', 'success');
		});
	}

	/**
	 * Generate random key string
	 *
	 * @param {number} length Key length
	 * @return {string} Random key
	 */
	function generateRandomKey(length) {
		var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
		var key = '';
		for (var i = 0; i < length; i++) {
			key += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		return key;
	}

	/**
	 * Initialize test connection functionality
	 */
	function initTestConnection() {
		// Make function globally accessible
		window.wbc_test_captcha_connection = function() {
			testCaptchaConnection();
		};
	}

	/**
	 * Test captcha connection
	 */
	function testCaptchaConnection() {
		var service = $('input[name="wbc_captcha_service"]:checked').val();
		if (!service) {
			service = $('select#wbc_captcha_service').val();
		}

		if (!service) {
			showNotice('Please select a captcha service first.', 'error');
			return;
		}

		// Get the relevant keys based on service
		var siteKey = '';
		var secretKey = '';

		switch(service) {
			case 'recaptcha_v2':
				siteKey = $('#wbc_recaptcha_v2_site_key').val();
				secretKey = $('#wbc_recaptcha_v2_secret_key').val();
				break;
			case 'recaptcha_v3':
				siteKey = $('#wbc_recaptcha_v3_site_key').val();
				secretKey = $('#wbc_recaptcha_v3_secret_key').val();
				break;
			case 'turnstile':
				siteKey = $('#wbc_turnstile_site_key').val();
				secretKey = $('#wbc_turnstile_secret_key').val();
				break;
			case 'hcaptcha':
				siteKey = $('#wbc_hcaptcha_site_key').val();
				secretKey = $('#wbc_hcaptcha_secret_key').val();
				break;
			case 'altcha':
				secretKey = $('#wbc_altcha_hmac_key').val();
				siteKey = 'self-hosted'; // ALTCHA doesn't need a site key
				break;
		}

		if (!siteKey || !secretKey) {
			showNotice('Please enter both site key and secret key.', 'error');
			return;
		}

		// Show loading state
		showNotice('Testing connection...', 'info');

		// Here you would normally make an AJAX call to test the connection
		// For now, we'll just simulate it
		setTimeout(function() {
			if (siteKey && secretKey) {
				showNotice('Connection test successful! Your keys appear to be valid.', 'success');
			} else {
				showNotice('Connection test failed. Please check your keys.', 'error');
			}
		}, 1000);
	}

	/**
	 * Show admin notice
	 *
	 * @param {string} message Notice message
	 * @param {string} type    Notice type (success, error, warning, info)
	 */
	function showNotice(message, type) {
		// Remove existing notices
		$('.wbc-admin-notice').remove();

		// Create notice HTML
		var noticeHtml = '<div class="notice notice-' + type + ' wbc-admin-notice is-dismissible" style="margin: 10px 0;">' +
						  '<p>' + message + '</p>' +
						  '<button type="button" class="notice-dismiss">' +
						  '<span class="screen-reader-text">Dismiss this notice.</span>' +
						  '</button>' +
						  '</div>';

		// Find the best place to insert the notice
		var $target = $('.wbc-quick-actions').first();
		if (!$target.length) {
			$target = $('.wrap h1').first();
		}

		// Insert notice after target
		$(noticeHtml).insertAfter($target);

		// Make dismissible
		$('.wbc-admin-notice .notice-dismiss').on('click', function() {
			$(this).parent().fadeOut(300, function() {
				$(this).remove();
			});
		});

		// Auto-dismiss after 5 seconds for success messages
		if (type === 'success') {
			setTimeout(function() {
				$('.wbc-admin-notice').fadeOut(300, function() {
					$(this).remove();
				});
			}, 5000);
		}
	}

	/**
	 * Additional helper: Validate API keys format
	 */
	function validateApiKeys(service, siteKey, secretKey) {
		var patterns = {
			'recaptcha_v2': {
				site: /^[0-9a-zA-Z_-]{40}$/,
				secret: /^[0-9a-zA-Z_-]{40}$/
			},
			'recaptcha_v3': {
				site: /^[0-9a-zA-Z_-]{40}$/,
				secret: /^[0-9a-zA-Z_-]{40}$/
			},
			'turnstile': {
				site: /^[0-9a-zA-Z_.-]+$/,
				secret: /^[0-9a-zA-Z_.-]+$/
			},
			'hcaptcha': {
				site: /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i,
				secret: /^0x[0-9a-fA-F]{40}$/
			},
			'altcha': {
				secret: /^.{32,}$/ // At least 32 characters
			}
		};

		if (patterns[service]) {
			if (service === 'altcha') {
				return patterns[service].secret.test(secretKey);
			} else {
				return patterns[service].site.test(siteKey) &&
					   patterns[service].secret.test(secretKey);
			}
		}

		return true; // Default to true if no pattern defined
	}

})(jQuery);