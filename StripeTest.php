<?php
	/** 
	 * Proof of Concept:
	 * Shows how to use the Stripe software.
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	require_once("DBOConnection.php");
	
	Stripe::setApiKey("sk_test_41JaqARDvT3P1OgOq6n1JD60");
	$token = $_POST["stripeToken"];
	
	try {
		
		$customer = Stripe_Customer::create(array(
				"card" => $token,
				"description" => "payinguser@example.com"
		));
		
		$charge = Stripe_Charge::create(array(
				"amount" => 100,
				"currency" => "usd",
				"customer" => $customer->id,
				"description" => "payinguser@jobsteria.com"
		));
		
		// From this point you can save customer id into the database to use for later use.
		echo "Successful Charge!";
	}
	catch(Stripe_CardError $e) {
		echo "The card has been declined! :(";
	}
?>