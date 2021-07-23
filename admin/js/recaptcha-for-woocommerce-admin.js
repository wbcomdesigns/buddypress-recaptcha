(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready(
		function(){
			var version = $( 'input[type=radio][name=wbc_recapcha_version][checked=checked]' ).val();
			hideshowfield( version );
			function hideshowfield(version)
			{
				if (version == 'v2' || version == 'undefined') {
					$( '#wc_settings_tab_recapcha_site_key_v3' ).closest( "tr" ).hide();
					$( '#wc_settings_tab_recapcha_secret_key_v3' ).closest( "tr" ).hide();
					$( '#wbc_recapcha_error_msg_v3_invalid_captcha' ).closest( "tr" ).hide();
					$( '#wbc_recapcha_no_conflict_v3' ).closest( "tr" ).hide();
					$( '#wbc_recapcha_error_msg_captcha_blank_v3' ).closest( "tr" ).hide();
					$( '#wbc_recapcha_error_msg_captcha_no_response_v3' ).closest( "tr" ).hide();

					$( '#wc_settings_tab_recapcha_site_key' ).closest( "tr" ).show();
					$( '#wc_settings_tab_recapcha_secret_key' ).closest( "tr" ).show();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_invalid' ).closest( "tr" ).show();
					$( '#wbc_recapcha_no_conflict' ).closest( "tr" ).show();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_blank' ).closest( "tr" ).show();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_no_response' ).closest( "tr" ).show();
				} else {
					$( '#wc_settings_tab_recapcha_site_key_v3' ).closest( "tr" ).show();
					$( '#wc_settings_tab_recapcha_secret_key_v3' ).closest( "tr" ).show();
					$( '#wbc_recapcha_error_msg_v3_invalid_captcha' ).closest( "tr" ).show();
					$( '#wbc_recapcha_no_conflict_v3' ).closest( "tr" ).show();
					$( '#wbc_recapcha_error_msg_captcha_blank_v3' ).closest( "tr" ).show();
					$( '#wbc_recapcha_error_msg_captcha_no_response_v3' ).closest( "tr" ).show();

					$( '#wc_settings_tab_recapcha_site_key' ).closest( "tr" ).hide();
					$( '#wc_settings_tab_recapcha_secret_key' ).closest( "tr" ).hide();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_invalid' ).closest( "tr" ).hide();
					$( '#wbc_recapcha_no_conflict' ).closest( "tr" ).hide();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_blank' ).closest( "tr" ).hide();
					$( '#wc_settings_tab_recapcha_error_msg_captcha_no_response' ).closest( "tr" ).hide();
				}
			}
			$( 'input[type=radio][name=wbc_recapcha_version]' ).change(
				function() {
					var vals   = $( this ).val();
					var txtmsg = 'reCaptcha V3 does not show any challenge like I am not robot etc. reCaptcha V3 uses a behind-the-scenes scoring system to detect abusive traffic, and lets you decide the minimum passing score. Please note that there is no user interaction shown in reRecapcha V3 meaning that no recaptcha challenge is shown to solve.';
					hideshowfield( vals );
					if (vals == 'v3') {
						alert( txtmsg );
					}
				}
			);
		}
	);
})( jQuery );
