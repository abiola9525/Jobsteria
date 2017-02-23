<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once('DBOConnection.php');
	
	Stripe::setApiKey(FreeConfiguration::getInstance()->getProperty("stripe_sk"));
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not authenticated.");
	}
	$card_id = null;
	
	if(isset($_GET["card_id"])) {
		$card_id = $_GET["card_id"];
	}
	$uid = $_SESSION["uid"];
	$logger->debug("Retrieving Credit Cards for user [$uid]");

	
	
	$dbh =DBOConnection::getConnection();
	$exUsrDao = new ExUserDAO($dbh);
	
	$user = $exUsrDao->getUserByUid($uid);
	
	$cu = Stripe_Customer::retrieve($user->customer_id);
	
	if($card_id != null) {
		$card = $cu->cards->retrieve($card_id);
		die($card->__toJson());
	}
	
	$logger->debug("Customer Credit Cards: " . $cu->cards->__toJson());
	
	echo $cu->cards->__toJson();
?>