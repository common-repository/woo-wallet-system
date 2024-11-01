<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$tables_to_delete = [
    $wpdb->prefix . 'ace_wallet_balance',
    $wpdb->prefix . 'ace_wallet_log',
    $wpdb->prefix . 'ace_wallet_recharge',
    $wpdb->prefix . 'ace_wallet_transactions',
];

// Loop through and drop each table
foreach ($tables_to_delete as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");

    if (WP_DEBUG === true && $wpdb->last_error) {
        error_log("Error dropping table $table: " . $wpdb->last_error);
    }
}