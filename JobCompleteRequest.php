<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	session_start();
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not authenticated. ");
	}
	
	if(!isset($_POST["pid"])) {
		die("Error: Incomplete Request Invocation.");
	}
	
	$dbh = DBOConnection::getConnection();
	$exUsrProjAwardDao = new ExUserProjectAwardDAO($dbh);
	
	/* First ensure that the projects milestone request was accepted. */
	
	$results = $exUsrProjAwardDao->getUser_project_awardByAttributeMap(array("uid" => $_SESSION["uid"], "pid" => $_POST["pid"]));
	
	if($results == null) {
		$logger->error("Error:User was not choosen to work on this project or the project doesn't exist.");
		die("Error: User was not choosen to work on this project or the project doesn't exist.");
	}
	
	$awardObj = $results[0];
	
	/* We also need to ensure that the user invoking the request is the one who is truly assigned to this job */
	
	if($awardObj->uid != $_SESSION["uid"]) {
		$logger->error("Error: User " . $_SESSION["uid"] . " attempted to complete job request for user, and he does not own the job");
		die("Error: User can only make job complete requests if they've been hired to perform the job.");
	}
	
	if($awardObj->milestone_request_accepted == "N") {
		$logger->error("Error: User attempted to send complete request without an accepted milestone request");
		die("Error: You must first complete a milestone request, which means you must make the milestone request and your employer must accept the request.");
	}
	
	
	
	/* At this point we can go ahead and set the flag for job complete require */
	
	$exUsrProjAwardDao->updateUser_project_award(array("project_complete_request" => "Y"), array("uid" => $_SESSION["uid"], "pid" => $_POST["pid"]));
	
?>