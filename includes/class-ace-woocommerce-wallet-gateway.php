<?php

if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

/**
 * Gateway class
*/

class WC_Gateway_Ace_Woo_Wallet extends WC_Payment_Gateway{

    function __construct() {
        
        global $woocommerce;
      
        $this->id = 'ace_woo_wallet';
      //  $this->icon = '';
      $this->has_fields = true;
       
        $this->method_title = 'Ace Woo Wallet';
        $this->method_description = 'Wallet payment gateway';
        $this->supports = array(
            'products'
        );
        
        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option( 'enabled' );

        $this->init_form_fields();

        // This action hook saves the settings
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options'] );
     
    }

    public function init_form_fields(){
	 
        $this->form_fields = array(
            'enabled' => array(
                'title'       => 'Enable/Disable',
                'label'       => 'Ace Woo wallet',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'default'     => 'Ace Woo Wallet',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'default'     => 'Pay with your wallet.',
            ),
            'mode' => array(
                'title'       => 'Stripe mode',
                'type'        => 'select',
                'default'     => 'Pay with your wallet.',
                'options'     => array(
                    'wc-test' => ( 'Test'),
                    'wc-live'   => ( 'Live' ),
                   
                ),
            ),
            'live_publication_key' => array(
                'title'       => 'Stripe publication key',
                'type'        => 'password',
                'default'     => 'Pay with your wallet.',
            ),
            'live_secret_key' => array(
                'title'       => 'Stripe secret key',
                'type'        => 'password',
                'default'     => 'Pay with your wallet.',
            ),
            'test_publication_key' => array(
                'title'       => 'Stripe publication key (Test)',
                'type'        => 'password',
                'default'     => 'Pay with your wallet.',
            ),
            'test_secret_key' => array(
                'title'       => 'Stripe secret key (Test)',
                'type'        => 'password',
                'default'     => 'Pay with your wallet.',
            )
        );
        
    }
    
    

    public function process_payment( $orderId ) {
       
        global $woocommerce;
        global $wpdb;

        $currentUser  =  get_current_user_id();

        $tableName = ACE_WALLET_BALANCE_TABLE;
        $logResult = $wpdb->get_results("SELECT * FROM $tableName WHERE `user_id` = $currentUser");
        $firstRow = $logResult[0];
        $totalBalance = $firstRow->current_balance;
        print_r($logResult);
        echo "<script>alert(" . json_encode($logResult) . ");</script>";
        
        $order = wc_get_order( $orderId );
        $orderData = $order->get_data();
		$orderJson = json_encode($orderData);
        $cart_total =  intval($order->get_total());

        $cart_status =  $order->get_status();
        if ($cart_total <= $totalBalance) {
            $total_wallet_balance  =  $totalBalance-$cart_total;	
            if ($cart_status != 'completed') {
                $result = $wpdb->update(
                        $tableName, 
                        array( 
                            'current_balance' => $total_wallet_balance,
                            'last_update'=> date('Y-m-d H:i:s') 
                        ),
                        array('user_id' => $currentUser ) 
                    );
            

                $currency = get_option('woocommerce_currency');
                $logTableName = ACE_WALLET_LOG_TABLE;
                $transactiobsTableName = ACE_WALLET_TRANSACTIONS_TABLE;
                $wpdb->insert($logTableName , array(
                    'user_id'      => $currentUser,
                    'type'   => 'Payment',
                    'meta'     => $orderJson,
                    'created_at'	=> date("Y-m-d H:i:s")
                ));

                $wpdb->insert($transactiobsTableName , array(
                    'user_id'      => $currentUser,
                    'order_id'   => $orderId, 
                    'amount'     => $cart_total,
                    'meta'     => $orderJson,
                    'created_at'	=> date("Y-m-d H:i:s")
                ));
            }
            $woocommerce->cart->empty_cart();     
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );

        }else{
            $data = "Please add more money in your wallet   ".   json_encode( $logResult ) ."  ". $cart_total;
        }
        wc_add_notice($data, 'error');
        return;
    }

}