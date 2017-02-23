<?PHP
	/**
	 * @author Gary Drocella
	 * @date 08/29/2014
	 * Time 11:19am
	 */
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_SESSION["uid"])) {
		die("Error: user is not logged in");
	}
	
	$uid = $_POST["uid"];
	$pid = $_POST["pid"];
	$sessionUid = $_SESSION["uid"];
	
	/** We are Ensuring that the user who is doing the hiring is the one who owns the job */
	
	$logger->debug("Hiring user " . $uid . " for job $pid");
	
	$dbh = DBOConnection::getConnection();
	$usrProjAwardDao = new ExUserProjectAwardDAO($dbh);
	$usrProjDao = new ExUserProjectDAO($dbh);
	$exProjDao = new ExProjectDAO($dbh);
	
	$logger->debug("Sanity Check Session User.");
	if(!$usrProjDao->userOwnsProject($sessionUid, $pid)) {
		$logger->error("Error: User $sessionUid can't hire another user $uid for a project $pid that is not their own.");
		echo 2;
		exit();
	}
	
	$logger->debug("Determine if a user is already hired for this job.");
	
	if($usrProjAwardDao->userHiredForJob($pid) == null) {
		$usrProjAwardDao->insertUser_project_award(array("uid" => $uid, "pid" => $pid, "accepted" => 'N', 
				"milestone_request_reject_count" => 0, "milestone_request" => "N" , "milestone_request_accepted" => "N", "project_complete_request" => "N",
				"project_complete_request_reject_count" => 0));
		$exProjDao->updateProject(array("status" => "PRE_HIRE"), array( "pid" => $_POST["pid"]));
		
		$logger->debug("User Hired Inserted");
	}
	else {
		$logger->error("Error: Already a user hired for this job.");
		echo 1;
		exit();
	}
	
	
	echo 0;
?>