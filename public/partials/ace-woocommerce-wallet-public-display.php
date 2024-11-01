<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Woocommerce_Wallet
 * @subpackage Ace_Woocommerce_Wallet/public/partials
 */

    global $wpdb;

    //  echo "Money has been added, thanks for adding Money for Ace wallet";
    $tableName = ACE_WALLET_BALANCE_TABLE;
    $logResult = $wpdb->get_row( "SELECT * FROM $tableName WHERE `user_id` = ".get_current_user_id()." order by id DESC LIMIT 1" );
    $totalAmount = ( $logResult ) ? $logResult->current_balance : 0.00;
    $userdetails=wp_get_current_user();



    ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://js.stripe.com/v3/"></script>


<h3>Your Total Balance</h3>
<p class="wallet_balance"><?php echo get_option('woocommerce_currency').' ( '.$totalAmount.' )'; ?></p>
	




  <div class="wrapper">
    <a href="#addpayments">Add Payments to Wallets</a>
</div>

<div id="addpayments" class="modal">
    <div class="modal__content">
    <div class="ace_wallet_money">
	<div id="paymentResponse"></div>
  <form action ="" method="post" id="paymentFrm">
   

      <div>
        <div class="radio-buttons">
            <input type="radio" id="html" name="amounts" value="10" >
              <label for="html">10</label><br>
              <input type="radio" id="css" name="amounts" value="20">
              <label for="css">20</label><br>
              <input type="radio" id="javascript" name="amounts" value="30">
              <label for="javascript">30</label>
            <br>  
        </div>
      </div>
        <div class="add-to-card">
          <p class="custom-amounts">Add custom amounts</p>
        </div>

        <div>
            
            <input type="hidden" name="cardholder-name" class="field customer_name" placeholder="Name" value="<?php echo $userdetails->user_nicename; ?>"/>
        </div>

        <div>
          
          <input type="hidden" type="email" class="field customer_email" placeholder="Email Address" value="<?php echo $userdetails->user_email; ?>"  />
        </div>

      
        <div class="stipe-amount-fields" style="display: none;">
          <p class="field_title"> Amounts </p>
          <input type="number" id="stripe-amount" value="">
        </div>
      
        <div>
          <p class="field_title">Card *</p>
          <div id="card-element" class="field"></div>
        </div>
        <button type="submit" class="payment field_title">
          <i class="fa fa-spinner fa-spin" style="display:none;"></i>Add to Wallet </button>
        
          <input type="hidden" id="stripe-currency" value="$">
        <div class="outcome">
          <div class="error"></div>
          <div class="success"><span class="token"></span>
          </div>
        </div>
  </form>

        <a href="#" class="modal__close">&times;</a>
    </div>
</div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php   wp_enqueue_script( "stripe", plugin_url . 'public/js/ace-stripe.js', array( 'jquery' ),time(), true );
 ?>