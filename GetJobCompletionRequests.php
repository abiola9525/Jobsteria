<?php
	/**
	 * @author Gary Drocella
	 * @date 09/06/2014
	 * Time: 05:01pm
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") .  "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	

	if(!isset($_SESSION["uid"])) {
		die("Error: User is not authenticated.");
	}
	
	$uid=$_SESSION["uid"];
	
	$dbh = DBOConnection::getConnection();
	$exUserProjAwardDao = new ExUserProjectAwardDAO($dbh);
	$exUserProjDao = new ExUserProjectDAO($dbh);
	$exProjDao = new ExProjectDAO($dbh);
	$results = $exUserProjDao->getUser_projectByAttributeMap(array("uid" => $uid));
	
	if($results == null) {
		die("<h1>No Jobs Owned at this time</h1>");
	}
	
	$msResults = array();
	
	$logger->debug("Found " . count($results));
	foreach($results as $k => $v) {
		$logger->debug("Searching For Pid= " . $v->pid);
		$projResults = $exUserProjAwardDao->getUser_project_awardByAttributeMapInRange(array("pid" => $v->pid, "project_complete_request" => "Y"), 25 , 0);
	
		if($projResults == null) {
			continue;
		}
		
		$logger->debug("Found an awarded project.");
		$projectResults = $exProjDao->getProjectByPid($projResults[0]->pid);
		//$bidResults = $exBidDao->getBidByAttributeMap(array("pid" => $projResults[0]->pid, "uid" => $projResults[0]->uid));
	
		$msResults[] = array("request" => $projResults[0], "job" => $projectResults[0]);
	}
	
	
	echo json_encode($msResults);
?>