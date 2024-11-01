<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/public
 * @author     Acewebx  <developer@acewebx.com>
 */
class Ace_Woocommerce_Wallet_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_Woocommerce_Wallet_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_Woocommerce_Wallet_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace-woocommerce-wallet-public.css', array(), time(), 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_Woocommerce_Wallet_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_Woocommerce_Wallet_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$keys = $this->aceCheckModeofPaymentGateways();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ace-woocommerce-wallet-public.js', array( 'jquery' ), time(), false );

		wp_enqueue_script( $this->plugin_name );
		
		wp_localize_script( 
			$this->plugin_name, 
			'ace_global_object', 
			array(
				'ajax_url' 		=> admin_url( 'admin-ajax.php' ), 
				'checkout_url' 	=> site_url('checkout')  ,
				'published_key'  => $keys['publication_key'],
			), 
			$this->version, true 
		);

	}

	public function aceOnInitLoadHandler(){
		add_rewrite_endpoint( 'ace-wallet', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	public function templateRedirectHandler(){
		global $wpdb;
		global $woocommerce;

		if ( !is_page( 'checkout' ) && !is_checkout() ) {
		//	echo "hi";
		}
	}

	public function aceCustomQueryVars( $vars ) {
	    $vars[] = 'ace-wallet';
	   	return $vars;
	}

	public function aceCustomAddMenuItem( $items ) {
		$items['ace-wallet'] = 'Ace Wallet'; 
		return $items;
	}

	public function aceWooWalletQueryHandler() {
		global $wpdb;
      
		include plugin_dir_path( __FILE__ ) . 'partials/ace-woocommerce-wallet-public-display.php';
		echo "<h5>Total spends payments from wallets</h5>";
		
		$userId = get_current_user_id();
      
		$transactionTable = ACE_WALLET_TRANSACTIONS_TABLE;
		$ordersTable = $wpdb->prefix . 'wc_orders';
		$rechargeTable = ACE_WALLET_RECHARGE_TABLE;

		$transactionTotal = $wpdb->get_var("SELECT COUNT(*) FROM $transactionTable WHERE user_id = $userId");
		$rechargeTotal = $wpdb->get_var("SELECT COUNT(*) FROM $rechargeTable WHERE user_id = $userId");
		
		$post_per_page = 10;
		$transactionPage = isset( $_GET['tpage'] ) ? abs( (int) $_GET['tpage'] ) : 1;
		$transactionOffset = ( $transactionPage * $post_per_page ) - $post_per_page;
		//$results = $wpdb->get_results( "SELECT * FROM $transactionTable LIMIT $post_per_page OFFSET $offset" );
		$transactionResults = $wpdb->get_results("
			SELECT t.*, o.status 
			FROM $transactionTable t
			LEFT JOIN $ordersTable o ON t.order_id = o.id
			WHERE t.user_id = $userId
			ORDER BY t.id desc
			LIMIT $post_per_page OFFSET $transactionOffset
		");

		$rechargePage = isset( $_GET['rpage'] ) ? abs( (int) $_GET['rpage'] ) : 1;
		$rechargeOffset = ( $rechargePage * $post_per_page ) - $post_per_page;
		$rechargeResults = $wpdb->get_results( "SELECT * FROM $rechargeTable WHERE user_id = $userId ORDER BY id desc LIMIT $post_per_page OFFSET $rechargeOffset" );


		echo '<div class="tabs">';
			echo '<ul class="tabs-nav">';
				echo '<li><a href="#tab-1">Transactions</a></li>';
				echo '<li><a href="#tab-2">Recharge</a></li>';
			echo '</ul>';
			echo '<div class="tabs-stage">';
				echo '<div id="tab-1">';
					if (!empty($transactionResults)) {
						echo '<table style="width:100%; border-spacing: 0;">';
						echo '<tr><th>Order Id</th><th>Amount</th><th>Status</th><th>Date</th></tr>';
						foreach ($transactionResults as $result) {
							$orderID = $result->order_id;
							$status = str_replace('wc-', '', $result->status);
							$orderViewUrl = esc_url( home_url( '/my-account/view-order/' . $orderID ) );
							$rowClass = ($status == 'refunded') ? 'refund' : 'debit';
							$formattedDate = date('F j, Y', strtotime($result->created_at));
							echo '<tr class="' . esc_attr($rowClass) . '">';
							echo '<td><a href="'.$orderViewUrl.'"> #' . $orderID . '</a></td>';
							echo '<td> $' . $result->amount . '</td>';
							echo '<td class="status">' . $status . '</td>';
							echo '<td>' . $formattedDate . '</td>';
							echo '</tr>';
						}
						echo '</table>';
						echo '<div class="pagination ace-wallet-pagination">';
						echo paginate_links( array(
						'base' => add_query_arg( 'tpage', '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => ceil($transactionTotal / $post_per_page),
						'current' => $transactionPage,
						'type' => 'list'
						));
						echo '</div>';
					} else {
						echo "No data found.";
					}
				echo '</div>';
				echo '<div id="tab-2">';
					if (!empty($rechargeResults)) {
						echo '<table style="width:100%; border-spacing: 0;">';
						echo '<tr><th>Transaction Id</th><th>Amount</th><th>Date</th></tr>';
						foreach ($rechargeResults as $rechargeResult) {
							$transactionId = str_replace('txn_', '', $rechargeResult->transaction_id);
							$formattedDate = date('F j, Y', strtotime($rechargeResult->created_at));
							echo '<tr>';
							echo '<td> #' . $transactionId . '</td>';
							echo '<td> $' . $rechargeResult->amount . '</td>';
							echo '<td>' . $formattedDate . '</td>';
							echo '</tr>';
						}
						echo '</table>';
						echo '<div class="pagination ace-wallet-pagination">';
						echo paginate_links( array(
						'base' => add_query_arg( 'rpage', '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => ceil($rechargeTotal / $post_per_page),
						'current' => $rechargePage,
						'type' => 'list'
						));
						echo '</div>';
					} else {
						echo "No data found.";
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';

	}


	function aceDisableBillingShippingOnWallet( $checkout ){
		// 	$checkout['billing']  = [];
		// 	$checkout['shipping'] = [];
		return $checkout;
	}

	public function aceInsertLog( $log, $type, $userId = '' ){
		global $wpdb;
		$userID = ($userId != '') ? $userId : get_current_user_id();
		$type = sanitize_text_field($type);
		$tableName = ACE_WALLET_LOG_TABLE;
		$wpdb->insert($tableName , [
          'user_id' 	=> $userID,
          'type'     	=> $type,
          'meta'		=> $log,
          'created_at'	=> date("Y-m-d H:i:s")
        ]);		
	}

	public function aceInsertTransaction( $orderId, $meta, $price, $userId = '' ){
		global $wpdb;
		$userID = ($userId != '') ? $userId : get_current_user_id();
		$tableName = ACE_WALLET_TRANSACTIONS_TABLE;
		$wpdb->insert($tableName , [
          	'created_at' => date("Y-m-d H:i:s"),
			'user_id' => $userID,
			'order_id' => $orderId,
			'amount' => $price,
			'meta'   => $meta
        ]);		
	}

	public function aceGetWalletBalance( $userId = false ){
		global $wpdb;
		$userId =  ( $userId ) ? $userId : get_current_user_id();
		$tableName = ACE_WALLET_BALANCE_TABLE;
		return $wpdb->get_row( "SELECT * FROM $tableName WHERE `user_id` = {$userId}" );
	}

	public function aceUpdateWalletBalance( $args ){
		global $wpdb;

		//$args['type'] = "credit"; // Add Money
		// $args['type'] = "debit"; // Deduct Money
		
		$oldBalance = $this->aceGetWalletBalance();
		$userId = (isset($args['user_id']) && $args['user_id'] != '') ? $args['user_id'] : get_current_user_id();
		$args['type'] = isset($args['type']) ? sanitize_text_field($args['type']) : '';
		$tableName = ACE_WALLET_BALANCE_TABLE;
		if( ! $oldBalance ){
			$result = $wpdb->insert($tableName , array(
				'user_id'         => $userId,
				'current_balance' => $args['price'],
				'last_update'     => date("Y-m-d H:i:s"),
				'status'          => $args['order_status']
			));
			return ['status' => $result, 'action' => 'insert'];
		}else{
			$walletAmount = $oldBalance->current_balance;

			if($args['type'] == "debit"){
				$totalWalletAmount = $walletAmount - $args['price'];	    	
				$result = $wpdb->update($tableName, 
					array(
						'current_balance' => $totalWalletAmount,
						'last_update' => date('Y-m-d H:i:s') 
					), 
					array(
						'user_id' => $userId
					)
				);
			}else{
				$totalWalletAmount = $walletAmount + $args['price'];	    	
				$result = $wpdb->update($tableName, 
					array(
						'current_balance' => $totalWalletAmount,
						'last_update' => date('Y-m-d H:i:s') 
					), 
					array(
						'user_id' => $userId
					)
				);
			}
			
			return ['status' => $result, 'action' => 'update'];
		}
		return ['status' => false, 'action' => null];
	}
    public function aceCheckModeofPaymentGateways(){
		$checkmode = get_option('woocommerce_ace_woo_wallet_settings');

		if($checkmode['mode']=='wc-test'){
			if (strpos($checkmode['test_publication_key'], 'live') == false) {
				$testpublishedkey = $checkmode['test_publication_key'];
				$testsecretkey = $checkmode['test_secret_key'];
			} else{
				$testpublishedkey = $checkmode['test_publication_key']."notvalid";
				$testsecretkey = $checkmode['test_secret_key']."notvalid";
			}
			return ['publication_key'=>$testpublishedkey,'secret_key'=>$testsecretkey];
		}elseif($checkmode['mode']=='wc-live'){
			if (strpos($checkmode['live_publication_key'], 'test') == false) {
				$livepublishedkey = $checkmode['live_publication_key'];
				$livesecretkey = $checkmode['live_secret_key'];
			} else{
				$livepublishedkey = $checkmode['live_publication_key']."notvalid";
				$livesecretkey = $checkmode['live_secret_key']."notvalid";
			}
			return ['publication_key'=>$livepublishedkey,'secret_key'=>$livesecretkey];
		}

	}

	public function aceWalletProcessAddMoney(){
		// error_reporting(E_ALL);
		// ini_set('display_errors', true);
		global $woocommerce;
        global $wpdb;
      	$userID = get_current_user_id(); 
		require_once(dirname(__FILE__) .'/library/vendor/autoload.php');
		
		$keys = $this->aceCheckModeofPaymentGateways();
		$stripeSecretKey = $keys['secret_key'];
		\Stripe\Stripe::setApiKey($stripeSecretKey);

		$token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
		$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
		$customerEmail = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
		$customerName = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';

		// Add customer to stripe 
		$customer = \Stripe\Customer::create(array( 
		  'email' => $customerEmail, 
		  'source'  => $token,
		  'name' => $customerName,
		  'description'=> 'Add money into wallet'
		));
		
		// Generate Unique order ID 
		$orderID = strtoupper(str_replace('.','',uniqid('', true)));
		
		// Convert price to cents 
		$itemPrice = ($amount * 100);
		$currency = get_woocommerce_currency();
		$itemName = 'Add money into wallet';
		
		// Charge a credit or a debit card 
        $charge = \Stripe\Charge::create(array( 
			'customer' => $customer->id, 
			'amount'   => $itemPrice, 
			'currency' => $currency, 
			'description' => $itemName, 
			'metadata' => ['order_id' => $orderID] 
		));
      
		// Retrieve charge details 
		$chargeJson = $charge->jsonSerialize();
		if($chargeJson){
		  $txn_id = $chargeJson['balance_transaction'];
		  $paymentStatus = $chargeJson['paid'];
                    
		  if($paymentStatus == 1){
			$paymentStatus = 'success';
			$aceRechargeTable = ACE_WALLET_RECHARGE_TABLE;
            $dateStr = strtotime(str_replace("/", "-", exec('date /T') . " " . exec('time /T')));

			$wpdb->insert(
				$aceRechargeTable,
				array(
					'created_at' => date('Y-m-d H:i:s',$dateStr) ,
					'user_id' => $userID,
					'amount' => $itemPrice/100,
                    'transaction_id' => $txn_id,
					'meta' => json_encode($chargeJson)
				)
			);
			
			$lastId = $wpdb->insert_id;
			$this->aceInsertLog(json_encode($chargeJson), 'Recharge');
            
            // Create post object
            $paymentPost = array(
              'post_title'    => "# ".$userID . " " . $customerName ." | Log : " . $lastId,
              'post_content'  => date('Y-m-d H:i:s',$dateStr) ,
              'post_status'   => 'publish',
              'post_type'     => 'payments',
            );
	        $post_id = wp_insert_post( $paymentPost );
            $payments = ($itemPrice/100)." ".$currency ;
            update_post_meta($post_id, 'payment_amount',$payments);   
			update_post_meta($post_id, 'payment_status',$paymentStatus); 
			update_post_meta($post_id, 'payment_date',date('Y-m-d H:i:s',$dateStr) );
            $this->aceUpdateWalletBalance(['type' => 'credit', 'price' => $itemPrice/100, 'order_status' => $paymentStatus]);
		  }
		die;
	}}

	function aceFilterPaymentGateway( $available_gateways ) {
		global $woocommerce;
      	// unset( $available_gateways['cod'] );
      	// unset( $available_gateways['cheque'] );
      	// unset( $available_gateways['ace_woo_wallet'] );
	    return $available_gateways;
	}

	function aceWalletThankyouHandler( $orderId ) {
		global $woocommerce;
		global $wpdb;
		$tableName = ACE_WALLET_TRANSACTIONS_TABLE;
		$orderIDExists = $wpdb->get_row( "SELECT * FROM $tableName WHERE `order_id` = {$orderId}" );
		if ( $orderIDExists ) return;
		$order = wc_get_order( $orderId );
		$orderData = $order->get_data();
		$orderJson = json_encode($orderData);
		
		$paymentMethod = $order->get_payment_method();
		$price = $order->get_total();
		$orderStatus = $order->get_status();
		if( $paymentMethod == 'ace_woo_wallet' ){
			$this->aceInsertLog($orderJson, 'Payment');
			$this->aceInsertTransaction($orderId, $orderJson, $price);
			if( $orderStatus == 'processing' ){
				$this->aceUpdateWalletBalance(['price' => $price, 'order_status' => $orderStatus]);
			}
		}
	}

}