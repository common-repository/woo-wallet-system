// jQuery(document).ready(function(){
    jQuery(document).on('click', '.time_container label', function(){
        jQuery('.time_container label').removeClass('active');
        jQuery(this).addClass('active');
    });
  // });
  
  function toggleDivVisibility(fav_language) {
    
  }
    var stripe = Stripe(ace_global_object.published_key);// your publisher key
   var elements = stripe.elements({
    fonts: [
      {
        family: 'Open Sans',
        weight: 400,
        src: 'local("Open Sans"), local("OpenSans"), url(https://fonts.gstatic.com/s/opensans/v13/cJZKeOuBrn4kERxqtaUH3ZBw1xU1rKptJj_0jans920.woff2) format("woff2")',
        unicodeRange: 'U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215',
      },
    ]
  });
  
  var card = elements.create('card', {
    hidePostalCode: true,
    style: {
      base: {
        iconColor: '#000',
        color: '#32315E',
        lineHeight: '48px',
        fontWeight: 400,
        fontFamily: '"Open Sans", "Helvetica Neue", "Helvetica", sans-serif',
        fontSize: '15px',
  
        '::placeholder': {
          color: '#CFD7DF',
        }
      },
    }
  });
  card.mount('#card-element');
  
  function setOutcome(result) {
    var successElement = document.querySelector('.success');
    var errorElement = document.querySelector('.error');
    successElement.classList.remove('visible');
    errorElement.classList.remove('visible');
    if (result.token) {
      $(".fa-spin").show();
      successElement.textContent = result.token.id;
      // successElement.classList.add('visible');
  
      // send payment request
      var token = result.token.id;
      var element = document.getElementsByClassName("stipe-amount-fields")[0];
      if(element.style.display == "none") {
       var amount = jQuery("input[name='amounts']:checked").val();
        
      }else{
       var amount = document.getElementById('stripe-amount').value;
       
      }
      console.log(amount);   
      var customerName = document.querySelector('.customer_name').value;
      var customerEmail = document.querySelector('.customer_email').value;
    
  
      sendTokenAndAmountToServer(token, amount, customerName, customerEmail);
      // console.log(token);
  
  
      // alert(token);
    } else if (result.error) {
      errorElement.textContent = result.error.message;
      errorElement.classList.add('visible');
    }
    
  
  
  }
  
  card.on('change', function(event) {
    setOutcome(event);
  });
  
  document.getElementById('paymentFrm').addEventListener('submit', function(e) {
    e.preventDefault();
    $(".fa-spin").show();
    var form = document.getElementById('paymentFrm');
    var extraDetails = {
      name: form.querySelector('input[name=cardholder-name]').value,
    
    };
    stripe.createToken(card, extraDetails).then(setOutcome);
  });
  
  function sendTokenAndAmountToServer(token, amount, customerName, customerEmail) {      
    // Make an AJAX request to your server
   
  
    $.ajax({
      type: "POST",
      url: ace_global_object.ajax_url,
      data: {
          action: 'ace_wallet_process_add_money',
          token: token,
          amount: amount,
          customer_email: customerEmail,
          customer_name: customerName,
         
      },
      cache: false,
      success: function(data) {
        document.querySelector('form').reset();
        $(".fa-spin").hide();
  
        // const startIndex = data.indexOf('{');
  
        // // Extract the JSON portion
        // const jsonData = data.substring(startIndex);
  
        // // Parse the JSON data and access the order_id
        // const parsedData = JSON.parse(jsonData);
        // const orderID = parsedData.metadata.order_id;
  
        // // console.log("order id is "+orderID);
        // const url = 'https://wordpress.web-xperts.xyz/stripe-checkout/success.php?orderid=' + orderID;
        console.log("success");
        if (window.location.hash) {
            let newUrl = window.location.href.split('#')[0];
            window.location.href = newUrl;
        }

        document.getElementById("paymentResponse").innerHTMl="Payments completed";
        
      },
      error: function(xhr, status, error) {
        console.error(xhr);
       
         document.getElementById("paymentResponse").innerHTMl="Payments failed";
      }
    });
  }

  jQuery(".custom-amounts").click(function(){
    jQuery(".stipe-amount-fields").toggle();
});