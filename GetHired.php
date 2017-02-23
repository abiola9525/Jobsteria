<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once("DBOConnection.php");
	
	session_start();
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not logged in.");
	}
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("Get Hired Invoked");
	
	$dbh = DBOConnection::getConnection();
	$exUsrProjAwardDao = new ExUserProjectAwardDAO($dbh);
	$exProjectDao = new ExProjectDAO($dbh);
	$exBidDao = new ExBidDao($dbh);
	
	$hiredList = $exUsrProjAwardDao->getUserProjectAwardByAttributeMapInRangeOrderedBy(array("uid" => $_SESSION["uid"]), 25, 0, "add_ts");
	
	$results = array();
	
	$logger->debug("Getting Hired Jobs for " . $_SESSION["uid"]);
	$logger->debug("Found " . count($hiredList));
	
	foreach($hiredList as $v) {
		$logger->debug("Getting Project " . $v->pid);
		$projArr = $exProjectDao->getProjectByPid($v->pid);
		$bidArr = $exBidDao->getBidByAttributeMap(array("pid" => $v->pid, "uid" => $_SESSION["uid"]));
		
		if($projArr == null) {
			$logger->error("User " . $_SESSION["uid"] . " Hired for a project " . $v->pid . " that no longer doesn't exist.");
		}
		else if($bidArr == null) {
			$logger->error("User " . $_SESSION["uid"] . " Hired for a project " . $v->pid . " Without placing a bid on it?");
		}
		else {
			$results[] = array("project" => $projArr[0], "hire" => $v, "bid" => $bidArr[0]);
		}
	}
	
	$logger->debug("Finished GetHire");
	
	echo json_encode($results);
?>