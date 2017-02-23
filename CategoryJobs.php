<?php
	/**
	 * @author Gary Drocella
	 * @date 09/03/2014
	 * Time: 10:14pm
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");	
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("CategoryJobs Invoked");
	
	if(!isset($_POST["cid"])) {
		die("Error: Incomplete Request Invocation!");
	}
	
	$dbh = DBOConnection::getConnection();
	$exProjDao = new ExProjectDAO($dbh);
	
	$results = $exProjDao->getProjectsByCid($_POST["cid"]);
	
	echo json_encode($results);
?>