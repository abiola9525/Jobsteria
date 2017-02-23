<?php
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_POST["uid"])) {
		$logger->error("Error: Incomplete Invocation.");
		die("Error: Incomplete Invocation");
	}
	
	if(!isset($_SESSION["uid"])) {
		$logger->error("Error: User not Authenticated.");
		die("Error: User not Authenticated.");
	}
	
	$dbh = DBOConnection::getConnection();
	$exUserDao = new ExUserDAO($dbh);
	
	$logger->debug("Getting User Infor for user id " . $_POST["uid"]);
	
	$user = $exUserDao->getUserByUid($_POST["uid"]);
	
	$user->password = null;
	$user->email = null;
	$user->phone = null;
	$user->resume_file_loc = null; 
	
	echo json_encode($user);
?>