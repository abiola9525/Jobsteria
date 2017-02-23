<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "BidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger("GetBidService");
	
	$logger->debug("Get Bid Invoked");
	
	if(!isset($_POST["jid"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	$attrMap=array();
	$attrMap["pid"]=$_POST["jid"];
	$attrMap["uid"]=$_SESSION["uid"];
	$dbh = DBOConnection::getConnection();
	$bidDao = new BidDAO($dbh);
	
	$tups = $bidDao->getBidByAttributeMap(array("pid" => $_POST["jid"]));
	
	$jsonEncoded = json_encode($tups);
	
	$logger->debug("Got Bid Json Encoded $jsonEncoded");
	
	echo $jsonEncoded;
?>
