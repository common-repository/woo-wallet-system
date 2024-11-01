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

})( jQuery );
jQuery(document).ready(function(){
var checked_mode=jQuery("#woocommerce_ace_woo_wallet_mode").val();
if (checked_mode == "wc-test") {
	jQuery("#woocommerce_ace_woo_wallet_live_publication_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_live_secret_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_test_publication_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_test_secret_key").closest('tr').css("display", "table-row");
} else if (checked_mode == "wc-live") {
	jQuery("#woocommerce_ace_woo_wallet_live_publication_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_live_secret_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_test_publication_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_test_secret_key").closest('tr').css("display", "none");
}

jQuery("#woocommerce_ace_woo_wallet_mode").change(function(){
var l = jQuery(this).val();
if (l == "wc-test") {
	jQuery("#woocommerce_ace_woo_wallet_live_publication_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_live_secret_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_test_publication_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_test_secret_key").closest('tr').css("display", "table-row");
} else if (l == "wc-live") {
	jQuery("#woocommerce_ace_woo_wallet_live_publication_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_live_secret_key").closest('tr').css("display", "table-row");
	jQuery("#woocommerce_ace_woo_wallet_test_publication_key").closest('tr').css("display", "none");
	jQuery("#woocommerce_ace_woo_wallet_test_secret_key").closest('tr').css("display", "none");
}
}); });