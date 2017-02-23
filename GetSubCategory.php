<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExCategoryDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("GetSubCategory Invoked!");
	
	if(!isset($_POST["cid"])) {
		die("Error!");
	}
	
	$dbh = DBOConnection::getConnection();
	$exCatDao = new ExCategoryDAO($dbh);
	
	
	$results = $exCatDao->getCategoryByAttributeMap(array("parent_cid" => $_POST["cid"]));
	
	echo json_encode($results);
?>