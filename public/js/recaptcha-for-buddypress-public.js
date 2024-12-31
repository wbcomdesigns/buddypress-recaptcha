(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	jQuery( document ).ready(function() {
		if (typeof (grecaptcha.render) !== 'undefined' && ( myCaptcha === undefined || myCaptcha === null ) ) {
			try{
			var myCaptcha=grecaptcha.render('g-recaptcha-checkout-wbc', {
			'sitekey': bpRecaptcha.site_key,
			'callback' : verifyCallback_add_guestcheckout
			});
			}catch(error){
				console.log(error);
			}
			}
			
			
			setTimeout(() => {
				if ( 'yes' == bpRecaptcha.disable_submit_btn ){
					jQuery(".wc-block-components-checkout-place-order-button").attr("disabled", true);
					jQuery(".wc-block-components-checkout-place-order-button").removeAttr("title");
				}
				if ( 'yes' == bpRecaptcha.disable_submit_btn_login_checkout ){
					jQuery(".wc-block-components-checkout-place-order-button").attr("disabled", true);
				}
			}, 200);
		

			
	});
	

	var verifyCallback_add_guestcheckout = function(response) {

		if(response.length!==0){

				window.recap_val= response;
				if (typeof woo_guest_checkout_recaptcha_verified === "function") {

							woo_guest_checkout_recaptcha_verified(response);
					}

					if( 'yes' == wbc_recapcha_guest_recpacha_refersh_on_error ){
						jQuery('body').on('checkout_error', function(){
							grecaptcha.reset(window.myCaptcha); 
							if ( 'yes' == bpRecaptcha.disable_submit_btn ){
								jQuery(".wc-block-components-checkout-place-order-button").attr("disabled", true);
							}
							if ( 'yes' == bpRecaptcha.disable_submit_btn_login_checkout ){
								jQuery(".wc-block-components-checkout-place-order-button").attr("disabled", true);
							}
					})                                                                                                    
				}
		}
	};

})( jQuery );
