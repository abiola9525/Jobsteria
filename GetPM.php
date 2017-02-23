<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "Private_messageDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("GetPM Invoked");
	
	if(!isset($_SESSION["uid"])) {
		$logger->error("Error: User is not Authenticated.");
		die("Error: User is not Authenticated.");
	}
	
	$dbh = DBOConnection::getConnection();
	$pmDao = new Private_messageDAO($dbh);
	$exUserDao = new ExUserDAO($dbh);
	
	$results = array();
	$pms = $pmDao->getPrivate_messageByAttributeMap(array("to_uid" => $_SESSION["uid"]));
	
	foreach($pms as $k => $v) {
		$user = $exUserDao->getUserByUid($v->from_uid);
		$user->password = null;
		$user->phone = null;
		$user->email = null;
		$results[] = array("pm" => $v, "from_user" => $user);
	}
	
	$logger->debug("Got Results " . json_encode($results));
	echo json_encode($results);
?>