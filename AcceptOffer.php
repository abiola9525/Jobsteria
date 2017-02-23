<?php
	/**
	 * @author Gary Drocella
	 * @date 08/29/2014
	 * Time: 4:44pm
	 * updated: 12/24/2014 Time: 6:36pm
	 * - Integrating the almighty Stripe software for service charge.
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "TransactionDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	require_once("DBOConnection.php");
	
	session_start();
	date_default_timezone_set('UTC');
	Stripe::setApiKey(FreeConfiguration::getInstance()->getProperty("stripe_sk"));
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_POST["pid"])) {
		$logger->error("Error: Incomplete Request.");
		die("Error: Incomplete Request");
	}
	
	if(!isset($_SESSION["uid"])) {
		$logger->error("Error: user is not logged in.");
		die("Error: user is not logged in.");
	}
	$logger->debug("Accept Offer Invoked by " . $_SESSION["uid"]);
	
	/**
	 * TODO: Ensure that the user invoking this service, is the user who 
	 * the job offer has been extended to. 
	 */

	$dbh = DBOConnection::getConnection();
	$exUsrProjAward = new ExUserProjectAwardDAO($dbh);
	$exProjDAO = new ExProjectDAO($dbh);
	
	$actualUsr = $exUsrProjAward->userHiredForJob($_POST["pid"]);
	
	if($actualUsr->uid != $_SESSION["uid"]) {
		$logger->error("Error: User " . $_SESSION["uid"] . " attempted to accept offer for user " . $actualUsr->uid . "." );
		die(1);
	}
	
	if($actualUsr->accepted == "Y") {
		$logger->error("Error: User attempted to accept job offer that has already been accepted.");
		die(4);
	}
	
	try {
		$dbh->beginTransaction();
		
		$exUsrProjAward->updateUser_project_award(array("accepted" => "Y"), array("uid" => $_SESSION["uid"], "pid" => $_POST["pid"]));
		$exProjDAO->updateProject(array("status" => "IN_PROG"), array("pid" => $_POST["pid"]));
	
		$logger->debug("The User " . $_SESSION["uid"] . " Successfully updated hire offer acception for job " . $_POST["pid"]);
	
		$exUsrProjDao = new ExUserProjectDAO($dbh);
		$exBidDao = new ExBidDAO($dbh);
		$transactionDao = new TransactionDAO($dbh);
	
		$results = $exUsrProjDao->getUser_projectByAttributeMap(array("pid" => $_POST["pid"]));
	
		if($results == null) {
			$logger->error("Error: There is no job owner apparently for job id " . $_POST["pid"]);
			die(2);
		}
	
		$bidResults = $exBidDao->getBidByAttributeMap(array("pid" => $_POST["pid"], "uid" => $_SESSION["uid"]));
		
		if($results == null) {
			$logger->error("Error: User accepting offer for a bid that no longer exists.");
			die(3);
		}
	
		$bid = $bidResults[0];
	
		$bidCharge = $bid->charge * .10;
	
		$logger->debug("Charging 10% of $" .  $bid->charge . " which is \$$bidCharge");
	
		$exUserDao = new ExUserDAO($dbh);
		$user = $exUserDao->getUserByUid($actualUsr->uid);
		
		$customerId = $user->customer_id;
		
		$logger->debug("Charging Customer Id: " . $customerId);
		
		$pid = $_POST["pid"];
		
		/** Todo: Perform a validation by checking recent transactions do not contain the current job id that is about to be charged. */
		
		$charge = Stripe_Charge::create(array(
				"amount" => $bidCharge*100,
				"currency" => "usd",
				"customer" => $customerId,
				"description" => "{\"jid\": \"$pid\", \"reason\": \"Jobsteria Service Charge.\"}"
		));
		
		$dt = new DateTime();
	
		$attrMap = array();
		$attrMap["amount"] = $bidCharge;
		$attrMap["type"] = "SERVICE_CHARGE";
		$attrMap["from_uid"] = $_SESSION["uid"];
		$attrMap["pid"] = $_POST["pid"];
		$attrMap["date"] = $dt->format("Y-m-d"); 
	
		/* Temporarily claim status as approved. */
		/** TODO: When adding the cyber source transaction software. I will add the true status of the transaction. */

		$attrMap["status"] = "APPROVED"; 
	
		/** TODO: Get the users credit card number to charge their bank account associated with it. */
		$transactionDao->insertTransaction($attrMap);
	
		$dbh->commit();
		
		echo 0;
	}
	catch(Exception $e) {
		$logger->error("Error: caught exception $e...\nRolling Back Database");
		$dbh->rollBack();
		die(6);
	}
?>