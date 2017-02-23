<?php
	require_once("FreeConfiguration.php");
	require_once("../../DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("exgen_dao_root_dir") . "ExUserDAO.php");
	
	session_start();
	
	if(!isset($_POST["uid"])) {
		
	}
	
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	$dbh = DBOConnection::getConnection();
	$exUserDao = new ExUserDAO($dbh);
	
	$user = $exUserDao->getUserByUid($_SESSION["uid"]);
	$user->password = null;
	
	echo json_encode($user);
?>