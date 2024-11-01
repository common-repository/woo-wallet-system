<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://acewebx.com
 * @since             1.0.0
 * @package           Ace_Woocommerce_Wallet
 *
 * @wordpress-plugin
 * Plugin Name:       Ace Woocommerce Wallet
 * Plugin URI:        https://wordpress.org/plugins/woo-wallet-system
 * Description:       This plugin help us for online payment we can add money WooCommece payment method to the wallet. WooCommerce plugin is required for this plugin.
 * Version:           2.5
 * Author:            Acewebx 
 * Author URI:        https://acewebx.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ace-woocommerce-wallet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACE_WOOCOMMERCE_WALLET_VERSION', '1.0.0' );
define('plugin_url',plugin_dir_url(__FILE__));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ace-woocommerce-wallet-activator.php
 */
function activate_ace_woocommerce_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace-woocommerce-wallet-activator.php';
	Ace_Woocommerce_Wallet_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ace-woocommerce-wallet-deactivator.php
 */
function deactivate_ace_woocommerce_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace-woocommerce-wallet-deactivator.php';
	Ace_Woocommerce_Wallet_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ace_woocommerce_wallet' );
register_deactivation_hook( __FILE__, 'deactivate_ace_woocommerce_wallet' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ace-woocommerce-wallet.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ace_woocommerce_wallet() {

	$plugin = new Ace_Woocommerce_Wallet();
	$plugin->run();

}


define('ACE_WALLET_BALANCE_TABLE', $wpdb->prefix . 'ace_wallet_balance');
define('ACE_WALLET_LOG_TABLE', $wpdb->prefix . 'ace_wallet_log');
define('ACE_WALLET_RECHARGE_TABLE', $wpdb->prefix . 'ace_wallet_recharge');
define('ACE_WALLET_TRANSACTIONS_TABLE', $wpdb->prefix . 'ace_wallet_transactions');

run_ace_woocommerce_wallet();
