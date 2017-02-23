<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") .  "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	session_start();
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not authenticated.");
	}
	
	$uid = $_SESSION["uid"];
	
	$dbh = DBOConnection::getConnection();
	$exUserProjAwardDao = new ExUserProjectAwardDAO($dbh);
	$exUserProjDao = new ExUserProjectDAO($dbh);
	$exBidDao = new ExBidDAO($dbh);
	$exProjDao = new ExProjectDAO($dbh);
	$results = $exUserProjDao->getUserProjectsByAttributeMapInRangeOrderedBy(array("uid" => $uid), 25, 0, "add_ts");
	
	if($results == null) {
		die("No jobs owned.");
	}
	
	$msResults = array();
	
	foreach($results as $k => $v) {
		$projResults = $exUserProjAwardDao->getUser_project_awardByAttributeMap(array("pid" => $v->pid, "milestone_request" => "Y"));
		
		if($projResults == null) {
			continue;
		}
		$projectResults = $exProjDao->getProjectByPid($projResults[0]->pid);
		$bidResults = $exBidDao->getBidByAttributeMap(array("pid" => $projResults[0]->pid, "uid" => $projResults[0]->uid));
		
		$msResults[] = array("request" => $projResults[0], "job" => $projectResults[0], "milestone" => $bidResults[0]->milestone);
	}
	
	$logger->debug("Got Results " . json_encode($msResults));
	
	echo json_encode($msResults);
?>