<?php
?>

<!DOCTYPE html>
<html>
    <head>
        <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <script src="https://js.stripe.com/v1/"></script>
        
        <script>
        Stripe.setPublishableKey('pk_test_GM9nrfcdptEpMQIg9Q1VFtSV');
        </script>
    </head>
     
    <body>
        <h2>Payment Form</h2>
     
        <form id="buy-form" method="post" action="javascript:">
             
            <p class="form-label">First Name:</p>
            <input class="text" id="first-name" spellcheck="false"></input>
             
            <p class="form-label">Last Name:</p>
            <input class="text" id="last-name" spellcheck="false"></input>
             
            <p class="form-label">Email Address:</p>
            <input class="text" id="email" spellcheck="false"></input>
             
            <p class="form-label">Credit Card Number:</p>
            <input class="text" id="card-number" autocomplete="off"></input>
             
            <p class="form-label">Expiration Date:</p>
            <select id="expiration-month">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
            </select>
             
            <select id="expiration-year">
                <?php 
                    $yearRange = 20;
                    $thisYear = date('Y');
                    $startYear = ($thisYear + $yearRange);
                 
                    foreach (range($thisYear, $startYear) as $year) 
                    {
                        if ( $year == $thisYear) {
                            print '<option value="'.$year.'" selected="selected">' . $year . '</option>';
                        } else {
                            print '<option value="'.$year.'">' . $year . '</option>';
                        }
                    }
                ?>
            </select>
             
            <p class="form-label">CVC:</p>
            <input class="text" id="card-security-code" autocomplete="off"></input>
             
            <input id="buy-submit-button" type="button" onclick="createToken()" value="Place This Order »"></input>
        </form>
    </body>
    <script type="text/javascript">

    	function stripeHandlerResponse(status, response) {
        	var stripeToken = response.id;
			$.post("./StripeTest.php", {stripeToken: stripeToken}, function(data) { alert(data);});
        }
        
		function createToken() {
			var cardNumber = document.getElementById("card-number").value;
			var cvc = document.getElementById("card-security-code").value;
			var expMon = document.getElementById("expiration-month").value;
			var expYear = document.getElementById("expiration-year").value;
			
			Stripe.createToken({number: cardNumber, cvc: cvc, exp_month: expMon, exp_year: expYear}, stripeHandlerResponse);
		}


    </script>
</html>