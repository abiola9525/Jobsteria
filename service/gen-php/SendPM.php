<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "Private_messageDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("SendPM Invoked");
	
	if(!isset($_POST["message"]) || !isset($_POST["username"]) || !isset($_POST["subject"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	/** TODO: Add business logic to only allow users to communicate if they are currently working togeather.  */
	
	$attrMap=array();
	$attrMap["message"]=$_POST["message"];
	$username=$_POST["username"];
	$attrMap["from_uid"]=$_SESSION["uid"];
	$attrMap["subject"]=$_POST["subject"];

	$dbh = DBOConnection::getConnection();
	$pmDao = new Private_messageDAO($dbh);
	$exUserDao = new ExUserDAO($dbh);
	$toUid=  $exUserDao->getUidByUsername($username);
	
	if($toUid == null) {
		die(1);
	}
	
	$attrMap["to_uid"] = $toUid;
	$pmDao->insertPrivate_message($attrMap);
?>
