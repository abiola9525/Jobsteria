<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_POST["jid"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	$dbh = DBOConnection::getConnection();
	$exProjDao = new ExProjectDAO($dbh);
	$exUsrProjDao = new ExUserProjectDAO($dbh);
	$exBidDao = new ExBidDAO($dbh);
	
	
	$logger->debug("Ensure that user owns the job that is attempting to delete.");
	
	if(!$exUsrProjDao->userOwnsProject($_SESSION["uid"], $_POST["jid"])) {
		$logger->debug("User " . $_SESSION["uid"] . " attempted to delete job " . $_POST["jid"] .  " that does not belong to them.");
		die(2);
	}
	
	$results = $exProjDao->getProjectByPid($_POST["jid"]);
	$project = $results[0];
	
	if($project->status != "BID" && $project->status != "PRE_HIRE") {
		$logger->error("Error: User attempted to delete a job that is in progress or completed.");
		die(1);
	}
	
	$attrMap = array("pid" => $_POST["jid"], "uid" => $_SESSION["uid"]);
	
	//$exUsrProjDao->deleteUser_project($attrMap);
	//$exBidDao->deleteBid(array("pid" => $_POST["jid"]));
	//$exProjDao->deleteProject(array("pid" => $_POST["jid"]));
	
	$logger->debug("Deleting Job " . $_POST["jid"] . " For UID " . $_SESSION["uid"]);
	
	$exProjDao->updateProject(array("deleted" => "Y"), array("pid" => $_POST["jid"]));
	
	
	$logger->debug("Deleted Project");
?>
