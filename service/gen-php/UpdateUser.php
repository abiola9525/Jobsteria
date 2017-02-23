<?PHP
	require_once("../../util/FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once("../../DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger("UpdateUserService");
	
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	$dbh = DBOConnection::getConnection();
	$exUsrDao = new ExUserDAO($dbh);
	$attrMap = array();
	
	foreach($_POST as $k => $v) {
		if($v != null) {
			$attrMap[$k] = $v;
		}
	}
	
	$logger->debug("Updating User");
	$exUsrDao->updateUser($attrMap, array("uid" => $_SESSION["uid"]));
	
?>
