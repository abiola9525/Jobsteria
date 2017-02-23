<?php
	/**
	 * @author Gary Drocella
	 * @date 09/03/2014
	 * Time: 12:08am
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") .  "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	date_default_timezone_set('UTC');
	
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$dbh = DBOConnection::getConnection();
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_POST["pid"])) {
		die("Error: Incomplete Request Invocation!");
	}
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User Not Authenticated!");
	}
	
	$uid = $_SESSION["uid"];
	
	/* First Make sure that user invoking is the one assigned to the project */
	$dbh = DBOConnection::getConnection();
	$exProjDao = new ExProjectDAO($dbh);
	$awardDao = new ExUserProjectAwardDAO($dbh);
	
	$results = $awardDao->getUser_project_awardByAttributeMap(array("pid" => $_POST["pid"]));
	
	if($results == null) {
		$logger->error("Error: job does not exist " . $_POST["pid"]);
		die("Error: job does not exist.");
	}
	
	$award = $results[0];
	if($award->uid != $uid) {
		$logger->error("Error: user $uid attempted to invoke milestone request for job belonging to " . $award->uid);
		die("Error: job is not yours.");
	}
	
	/* Then make sure that the job is in progress */
	
	$results = $exProjDao->getProjectByAttributeMap(array("pid" => $_POST["pid"]));
	
	if($results == null) {
		$logger->error("Error: job does not exist. " . $_POST["pid"]);
		die("Error: job does not exist.");
	}
	
	$proj = $results[0];
	
	if($proj->status == "IN_PROG") {
		$logger->debug("Creating Milestone Request.");
		$awardDao->incrementMilestoneRequestReject($_POST["pid"], $uid);
		$dt = new DateTime();
		$awardDao->updateUser_project_award(array("milestone_request" => "Y", "update_ts" => $dt->format("Y-m-d H:i:s")), array("pid" => $_POST["pid"], "uid" => $_SESSION["uid"]));
	}
	else {
		$logger->error("Error: User $uid attempted to perform mileston request on job " . $_POST["pid"] . " that has not yet started.");
		die("Error: Job has not started yet");
	
	}

	
?>