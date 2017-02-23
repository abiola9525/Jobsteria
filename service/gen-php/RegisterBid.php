<?PHP
	require_once("../../DBOConnection.php");
	require_once('../../util/FreeConfiguration.php');
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "BidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "User_projectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("stripe") . "Stripe.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	session_start();
	Stripe::setApiKey(FreeConfiguration::getInstance()->getProperty("stripe_sk"));
	
	$logger->debug("RegisterBid.php Invoked!");
	
	if(!isset($_POST["milestone"]) || !isset($_POST["message"]) || !isset($_POST["amount"])  || !isset($_POST["startDate"]) || !isset($_POST["charge"]) || !isset($_POST["endDate"]) || !isset($_POST["pid"]) ) {
		$logger->debug("Failed: Incomplete Request Invocation!");
		die("Failed: Incomplete Request Invocation!");
	}
	if(!isset($_SESSION["uid"])) {
		$logger->debug("Failed: User is not Authenticated.");
		die("Failed: User is not Authenticated.");
	}
	
	
	
	$uid = $_SESSION["uid"];
	
	$attrMap=array();
	$attrMap["milestone"]=$_POST["milestone"];
	$attrMap["message"]=$_POST["message"];
	$attrMap["amount"]=$_POST["amount"];
	$attrMap["uid"]=$_SESSION["uid"];
	$attrMap["start_date"]=$_POST["startDate"];
	$attrMap["charge"]=$_POST["charge"];
	$attrMap["end_date"]=$_POST["endDate"];
	$attrMap["pid"]=$_POST["pid"];
	
	$dbh = DBOConnection::getConnection();
	$bidDao = new BidDAO($dbh);
	$usrProjDao = new User_projectDAO($dbh);
	$exUsrDao = new ExUserDAO($dbh);
	
	/* TODO: Before inserting the bid, ensure that the biding isn't closed. */
	
	try {
		$dbh->beginTransaction();
		
		$bidder = $exUsrDao->getUserByUid($uid);
		
		$customerId = $bidder->customer_id;
		
		if($customerId == null) {
			$logger->debug("Error: Bidder has no customer id available.");
			die("Error: Bidder has no customer id available.");
		}
		
		$cu = Stripe_Customer::retrieve($customerId);
		
		$card = $cu->cards->retrieve($cu->default_card);
		
		$logger->debug("Customer default card name " . $card->name);
		
		
		/** TODO: Add Bank Account functionality... */
		$recipient = Stripe_Recipient::create(array(
			"name" => $card->name,
			"type" => "individual",
			"card" => $cu->default_card,
			"email" => $bidder->email
		));
		
		$logger->debug("Recipient Created for uid:[$uid]");
		
		$bidDao->insertBid($attrMap);
		$exUsrDao->updateUser(array("recipient_id" => $recipient->id), array("uid" => $uid));
		
		$dbh->commit();
	}
	catch(Exception $e) {
		$dbh->rollBack();
		$logger->error("Error: Got exception $e");
		die("Error: Got Exception $e");
	}
?>
