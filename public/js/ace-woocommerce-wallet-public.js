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

	jQuery(document).ready( function() {

		var checkout_page = ace_global_object.checkout_url;

		jQuery('#aceAddMoney').click( function (){
			var action_url = ace_global_object.ajax_url;
			var ace_money_amount = jQuery('#ace_money_amount').val();
			jQuery.ajax({
				url: action_url,
				type: 'post',
				data: { action: 'ace_create_wallet_product',  ace_money_amount: ace_money_amount},
				success: function( data ){
				window.location.replace(checkout_page);
			},
				error: function( $error ) {
					console.log($error);
				} 	
			});
		});

		jQuery('.tabs-stage div').hide();
		jQuery('.tabs-stage div:first').show();
		jQuery('.tabs-nav li:first').addClass('tab-active');
		var target = jQuery('.tabs-nav a').attr('href');
		jQuery(target).show();
		jQuery(target).find('.pagination').show();
		jQuery('.tabs-nav a').on('click', function(event){
			event.preventDefault();
			jQuery('.tabs-nav li').removeClass('tab-active');
			jQuery('.tabs-stage div').hide();
			jQuery(this).parent().addClass('tab-active');
			var targetPagination = jQuery(this).attr('href');
			jQuery(targetPagination).show();
			jQuery(targetPagination).find('.pagination').show();
		});
		
	});

})( jQuery );
