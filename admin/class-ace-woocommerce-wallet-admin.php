
<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/admin
 * @author     Acewebx  <developer@acewebx.com>
 */
class Ace_Woocommerce_Wallet_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
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

        //wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace-woocommerce-wallet-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace-woocommerce-wallet-admin.css', array(), strtotime('now'), 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
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

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ace-woocommerce-wallet-admin.js', array( 'jquery' ), $this->version, false );

    }

    public function aceWoocommerceGatewayInit(){
        // Include Wallet Class
        require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-ace-woocommerce-wallet-gateway.php";
    }
    
    public function aceWoocommerceGatewayInsert($methods) {
        $methods[] = 'WC_Gateway_Ace_Woo_Wallet';
        return $methods;
    }
    


    public function declare_cart_checkout_blocks_compatibility() {
    
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }



    public function oawoo_register_order_approval_payment_method_type() {
        
            if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
                return;
            }

            
            require_once plugin_dir_path(__FILE__) . 'class-block.php';

            
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                    
                    $payment_method_registry->register( new My_Custom_Gateway_Blocks );
                }
            );
    }

    public  function walletes_submenu() {
        global $submenu;
        
        add_submenu_page( 'woocommerce', 'Wallet Payments History', 'Wallet Payments History', 'manage_options', 'wallet-payments-history', array( $this, 'my_custom_submenu_page_callback' ) ); 
        // echo "<pre>";

        // print_r($submenu['woocommerce']);

        // echo "</pre>";
        // remove_submenu_page('woocommerce', 'edit.php?post_type=team');
        
        
        //  $new_submenu_item = array(
        //     'Payments History of Wallets',                // Submenu item title
        //     'manage_options',          // Capability required to access
        //     'edit.php?post_type=payments' // URL of the submenu item
        // );

        // Append the new submenu item to the WooCommerce submenu
       // array_push($submenu['woocommerce'], $new_submenu_item);   
    }

    function create_teams_post_type_submenu() {
        global $submenu;
    }

    public function my_custom_submenu_page_callback() {
        global $wpdb;
        echo "<h1 class='wallet-history'>Walllet Payments History</h1>";
        if (!in_array('ace_wallet_transactions', $wpdb->tables)) {
            $table_name = ACE_WALLET_TRANSACTIONS_TABLE;
            $ordersTable = $wpdb->prefix . 'wc_orders';
            $userId = get_current_user_id();

            $total = $wpdb->get_var("SELECT COUNT(*) FROM (SELECT * FROM $table_name LIMIT 0,431) AS a");
            $post_per_page = 10;
            $page = isset( $_GET['tpage'] ) ? abs( (int) $_GET['tpage'] ) : 1;
            $offset = ( $page * $post_per_page ) - $post_per_page;
            //$results = $wpdb->get_results( "SELECT * FROM $table_name LIMIT $post_per_page OFFSET $offset" );
            $results = $wpdb->get_results("
                SELECT t.*, o.status 
                FROM $table_name t
                LEFT JOIN $ordersTable o ON t.order_id = o.id
                WHERE t.user_id = $userId
                ORDER BY t.id desc
                LIMIT $post_per_page OFFSET $offset
            ");

            if (!empty($results)) {
                echo ' <table class="wallet-data">
                <thead>
                    <tr>
                        <th class="column-wallet_order_id" id="wallet_order_id">Order ID</th>
                        <th class="column-wallet_order_amount" id="wallet_order_amount">Amount</th>
                        <th class="column-wallet_order_status" id="wallet_order_status">Status</th>
                        <th class="column-wallet_order_date column-primary" id="wallet_order_date">Date</th>
                        <th class="column-wallet_order column-primary" id="wallet_order">View Order</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($results as $result) {
                    $status = str_replace('wc-', '', $result->status);
                    $formattedDate = date('M j, Y', strtotime($result->created_at));
                    echo '<tr class="type-wallet_order">';
                    echo '<td> #' . $result->order_id . '</td>';
                    echo '<td>' . $result->amount . '</td>';
                    echo '<td class="wallet-status">' . $status . '</td>';
                    echo '<td>' . $formattedDate . '</td>';
                    echo '<td><a href="'.admin_url().'/admin.php?page=wc-orders&action=edit&id='.$result->order_id.'">View</a></td>';
                    echo '</tr>';
                }
                
                echo '</tbody> </table>';
                echo '<div class="pagination ace-wallet-pagination">';
                echo '<span class="wallet-count">'. $total .' items </span>';
                echo paginate_links( array(
                'base' => add_query_arg( 'tpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => ceil($total / $post_per_page),
                'current' => $page,
                'type' => 'list'
                ));
                echo '</div>';
                ?>
            

            <?php
            } else {
                echo "No any data founds";
            }
        }
    }

    public function aceInsertLog( $log, $type, $userId = '' ){
        global $wpdb;
        $userID = ($userId != '') ? $userId : get_current_user_id();
        $type = sanitize_text_field($type);
        $logTableName = ACE_WALLET_LOG_TABLE;
        $wpdb->insert($logTableName , [
        'user_id'   => $userID,
        'type'      => $type,
        'meta'      => $log,
        'created_at'    => date("Y-m-d H:i:s")
        ]);     
    }
    public function aceInsertTransaction( $orderId, $meta, $price, $userId = '' ){
        global $wpdb;
        $userID = ($userId != '') ? $userId : get_current_user_id();
        $transactiobsTableName = ACE_WALLET_TRANSACTIONS_TABLE;
        $wpdb->insert($transactiobsTableName , [
            'created_at' => date("Y-m-d H:i:s"),
            'user_id' => $userID,
            'order_id' => $orderId,
            'amount' => $price,
            'meta'   => $meta
        ]);     
    }


    public function aceUpdateWalletBalance( $args ){
        global $wpdb;
        
        $userId = (isset($args['user_id']) && $args['user_id'] != '') ? $args['user_id'] : get_current_user_id();
        $tableName = ACE_WALLET_BALANCE_TABLE;
        $oldBalance = $wpdb->get_row( "SELECT * FROM $tableName WHERE `user_id` = {$userId}" );
        $args['type'] = isset($args['type']) ? sanitize_text_field($args['type']) : '';

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

    public function aceWalletRefundHandler( $orderId, $refundId  ) {
        $restock_items = isset($_POST['restock_refunded_items']) ? $_POST['restock_refunded_items'] : false;
        $refund = wc_get_order( $refundId );
        $refundData = $refund->get_data();
        $refundJson = json_encode($refundData);
        $refundAmount = $refund->get_amount();
        $refundStatus = $refund->get_status();
        if($restock_items == 'true'){
            $this->aceInsertLog($refundJson, 'Refund');
            $this->aceInsertTransaction($orderId, $refundJson, $refundAmount);
            $this->aceUpdateWalletBalance(['type' => 'credit', 'price' => $refundAmount, 'refund_status' => $refundStatus]);
        }
    }

    public function create_teams_post_type() {
        $labels = array(
            'name'               => 'Payments',
            'singular_name'      => 'payments',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New payments',
            'edit_item'          => 'Edit payments',
            'new_item'           => 'New payments',
            'view_item'          => 'View payments',
            'search_items'       => 'Search payments',
            'not_found'          => 'No payments found',
            'not_found_in_trash' => 'No payments found in trash',
            'parent_item_colon'  => '',
            'menu_name'          => 'payments',
        );
    
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'payments' ),
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => 'do_not_allow', // Removes support for the "Add New" function, including Super Admin's
               ),
            'map_meta_cap' => true,  
        
            'hierarchical'        => false,
            'show_in_menu'        => true, // Show from the main menu
            'show_in_rest'        => true,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ,'custom-fields'),
        );
    
        register_post_type( 'payments', $args );
       
    }

    public function custom_payments_meta_box() {
        add_meta_box(
            'custom_payments_meta_box',
            'Payment Details',
            'custom_payments_meta_box_callback',
            'payments',
            'normal',
            'high'
        );
    }

    public function custom_payments_meta_box_callback($post) {
   
        wp_nonce_field(basename(__FILE__), 'custom_payments_nonce');
        ?>
        <label for="payment_amount">Payment Amount:</label>
        
        <input type="text" id="payment_amount" name="payment_amount" value="<?php echo esc_attr(get_post_meta($post->ID, 'payment_amount', true)); ?>" disabled/>
        <br/><label for="payment_amount">Payment Status:</label>
        
        <input type="text" id="payment_status" name="payment_status" value="<?php echo esc_attr(get_post_meta($post->ID, 'payment_status', true)); ?>" disabled/>
        
        
        <br/><label for="payment_amount">Date:</label>
        <input type="text" id="payment_date" name="payment_date" value="<?php echo esc_attr(get_post_meta($post->ID, 'payment_date', true)); ?>" disabled />
        
       <?php
    } 

    public function my_add_new_columns($columns) {
        $post_type = get_post_type();
        if ( $post_type == 'payments' ) {
            unset( $columns['author'] );
            unset( $columns['comments'] );
            unset( $columns['date'] );
            
            $new_columns = array(
                'my_payments' => esc_html__( 'Added Payment', 'text_domain' ),
                'my_date' => esc_html__( 'Date', 'text_domain' ),
            );
            return array_merge($columns, $new_columns);
        }
    }

    public function render_column( string $column_id ) {
        if ( $column_id !== "my_payments" ) {
            return;
        }
    
        $post = get_post();
        echo esc_html( get_post_meta( $post->ID, 'payment_amount', true ) );
    }

    public function render_column2( string $column_id ) {
        if ( $column_id !== "my_date" ) {
            return;
        }
    
        $post = get_post();
        echo esc_html( get_post_meta( $post->ID, 'payment_date', true ) );
    }
    
    public function save_custom_payments_meta_data($post_id) {
    
        if (!isset($_POST['custom_payments_nonce'])) {
            return;
        }
    
        if (!wp_verify_nonce($_POST['custom_payments_nonce'], basename(__FILE__))) {
            return;
        }
    
    
    
        if ('payments' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {
            
          
            update_post_meta($post_id, 'payment_email', sanitize_text_field($_POST['payment_email']));
            
        }
    }

}

