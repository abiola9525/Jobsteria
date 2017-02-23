<?php
	/**
	 * @author Gary Drocella
	 * @date 09/04/2014
	 * Time: 09:09pm
	 */
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUser.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "TransactionDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	session_start();
	Stripe::setApiKey(FreeConfiguration::getInstance()->getProperty("stripe_sk"));
	date_default_timezone_set('UTC');
	
	$logger->debug("AcceptMilestoneRequest Invoked");
	
	if(!isset($_POST["pid"])) {
		$logger->error("Error: Incomplete Request Invocation.");
		die("Error: Incomplete Request Invocation.");
	}
	
	if(!isset($_SESSION["uid"])) {
		$logger->error("Error: User is not Authenticated.");
		die("Error: User is not Authenticated.");
	}
	
	$pid = $_POST["pid"];
	$uid = $_SESSION["uid"];
	
	$logger->debug("Ensure that milestone request was indeed made for project $pid");
	
	$dbh = DBOConnection::getConnection();
	$exUpaDao = new ExUserProjectAwardDAO($dbh);
	$exUsrProjDao = new ExUserProjectDAO($dbh);
	$transactionDao = new TransactionDAO($dbh);
	$exBidDao = new ExBidDAO($dbh);
	
	$results = $exUpaDao->getUser_project_awardByAttributeMap(array("pid" => $pid));
	
	$upa = $results[0];
	if($upa->milestone_request == "N") {
		$logger->error("Error: Milestone request has not been made for project $pid.");
		die("Error: Milestone request has not been made.");
	}

	if($upa->milestone_request_accepted == "Y") {
		$logger->error("Error: $uid attempted to accept a request that is already accepted for project $pid.");
		die("Error: This Milestone request has already been request.");
	}
	
	if(!$exUsrProjDao->userOwnsProject($uid, $pid)) {
		$logger->error("Error: User who does not own job can't accept the milestone request.");
		die("Error: User who does not own job can't accept the milestone request.");
	}

	/** TODO: Perform Transaction of payment from employer to employee of the Milestone request percentage.  */
	
	$logger->debug("Logging Transaction to the Database. ");
	
	$bidResults = $exBidDao->getBidByAttributeMap(array("uid" => $upa->uid, "pid" => $_POST["pid"]));
	
	if($bidResults == null) {
		$logger->debug("Error: Can't reference bid for the hired user " . $upa->uid . " for project $pid and employer $uid");
		die("Error: Can't reference bid for the hired user.");
	}
	
	$bid = $bidResults[0];
	$milestone = ($bid->milestone / 100.0);
	$amount = $bid->amount;
	$charge = $milestone * $amount;
	
	$attrMap = array();
	
	$logger->debug("Charging Milestone Request for $milestone% completed payment by [$uid] to " . $upa->uid . ".  The charge is calculated to be $charge. in");
	
	$attrMap["amount"] = $charge;
	$attrMap["from_uid"] = $uid;
	$attrMap["to_uid"] = $upa->uid;
	$attrMap["pid"] = $bid->pid;

	$dt = new DateTime();
	
	$attrMap["date"] = $dt->format("Y-m-d");
	$attrMap["type"] = "MILE_STONE";
	// TODO: When credit cards are being used. get the actual status based on transaction status.
	$attrMap["status"] = "APPROVED";
	
	
	try {
		$userDao = new ExUserDao($dbh);
		
		$employer = $userDao->getUserByUid($uid);
		$recipient = $userDao->getUserByUid($upa->uid);
		
		$customerId = $employer->customer_id;
		
		if($customerId == null) {
			$logger->error("Error: Employer does not have a customer id.");
			die("Error: Employer does not have a customer id.");
		}
		
		if($ricipient->recipient_id == null) {
			$logger->error("Error: Recipient User does not have a recipient id.");
			die("Error: Recipient user does not have a recipient id.");
		}
		
		$cu = Stripe_Customer::retrieve($customerId);
		
		
		$transactionDao->insertTransaction($attrMap);
	
		$exUpaDao->updateUser_project_award(array("milestone_request_accepted" => "Y", "update_ts" => $dt->format("Y-m-d H:i:s")), array("pid" => $pid));
	}
	catch(Exception $e) {
		
	}
	
?>