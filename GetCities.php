<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CitiesDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("GetCities Invoked");
	
	if(!isset($_POST["project_country"]) || !isset($_POST["project_state"])) {
		die("Error: Incomplete Invocation Request");
	}
	
	$dbh = DBOConnection::getConnection();
	$citiesDao = new CitiesDAO($dbh);
	
	$results = $citiesDao->getCitiesByAttributeMap(array("CountryID" => $_POST["project_country"], "RegionID" => $_POST["project_state"]));
	
	$cityNameList = array();
	
	foreach($results as $v) {
		$cityNameList[] = $v->City;
	}
	
	$logger->debug("Cities Retrieved " . json_encode($cityNameList));
	
	echo json_encode($cityNameList);
?>