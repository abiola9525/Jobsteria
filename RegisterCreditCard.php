<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	require_once("DBOConnection.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("Register Credit Card Service has been invoked.");
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not logged in");
	}
	
	Stripe::setApiKey(FreeConfiguration::getInstance()->getProperty("stripe_sk"));
	
	
	if(isset($_POST["stripeToken"])) {
		$token = $_POST["stripeToken"];
	}
	
	$uid = $_SESSION["uid"];
	
	if(!isset($_POST["cvc"]) || !isset($_POST["cardType"]) || !isset($_POST["cardNumber"]) || !isset($_POST["expireMonth"]) || !isset($_POST["expireYear"]) || !isset($_POST["cardName"])) {
		die("Error: Invalid Invocation");
	}

	if(!isset($_POST["stripeToken"]) && !isset($_POST["card_id"])) {
		die("Error: Invalid Invocation");
	}
	
	$dbh = DBOConnection::getConnection();
	
	$exUserDao = new ExUserDAO($dbh);
	$user = $exUserDao->getUserByUid($uid);
	$customerId = $user->customer_id;
	
	try {
		if($customerId == null) {
			$customer = Stripe_Customer::create(array(
					"card" => $token,
					"description" => "Uid: [$uid]"
			));
			$exUserDao->updateUser(array("customer_id" => $customer->id), array("uid" => $uid));
		}
		else if(isset($_POST["card_id"]) && $_POST["card_id"] != null) {
			$cardId = $_POST["card_id"];
			$logger->debug("Looking up $cardId");
			$cu = Stripe_Customer::retrieve($customerId);
			$card= $cu->cards->retrieve($cardId);
			$card->exp_month = $_POST["expireMonth"];
			$card->exp_year = $_POST["expireYear"];
			$card->name = $_POST["cardName"];
			$card->save();
		}
		else {
			$cu = Stripe_Customer::retrieve($customerId);
			$cu->cards->create(array("card" => $token));
		}
	}
	catch(Stripe_CardError $e) {
		die("Error: " . e);
	}
	
	
	
	
	
?>