<?php

/**
 * Fired during plugin activation
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/includes
 * @author     Acewebx  <developer@acewebx.com>
 */
class Ace_Woocommerce_Wallet_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$charset_collate = $wpdb->get_charset_collate();
			 $aceWalletBalanceSQL = "CREATE TABLE `" . ACE_WALLET_BALANCE_TABLE . "` (
				id int NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL,
				current_balance varchar(100) NOT NULL,
				last_update datetime NOT NULL,
				status varchar(20) NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta($aceWalletBalanceSQL);

			$aceWalletLog = "CREATE TABLE `" . ACE_WALLET_LOG_TABLE . "` (
				id int NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL,
                type varchar(50) NOT NULL,
                meta text NOT NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta($aceWalletLog);
			
            $aceRecharge = "CREATE TABLE `" . ACE_WALLET_RECHARGE_TABLE . "` (
            	id INT(9) NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                amount VARCHAR(20),
                transaction_id VARCHAR(150) NOT NULL,
                meta text,
                created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY(id)
            ) $charset_collate;";
          	dbDelta( $aceRecharge );

         	$aceTransaction = "CREATE TABLE `" . ACE_WALLET_TRANSACTIONS_TABLE . "` (
            	id INT(9) NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                order_id int NOT NULL,
                amount VARCHAR(20),
                meta text,
                created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY(id)
            ) $charset_collate;";
            dbDelta( $aceTransaction );
          
		}else{
			wp_die("Woocommerce plugin is required.");
		}
	}
}