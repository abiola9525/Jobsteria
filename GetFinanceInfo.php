<?php
	/** 
	 * @author Gary Drocella
	 * @date 09/05/2014
	 * Time 06:53pm
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir"). "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExTransactionDAO.php");
	require_once(FreeConfiguration::getInstance()->getproperty("log4php") . "Logger.php");
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	
	session_start();
	
	if(!isset($_SESSION["uid"])) {
		$logger->error("Error: User is not authenticated.");
		die("Error: User Not Authenticated.");
	}
	
	$uid = $_SESSION["uid"];
	
	$dbh = DBOConnection::getConnection();
	$exTransDao = new ExTransactionDAO($dbh);
	$exUsrDao = new ExUserDAO($dbh);
	$exProjDao = new ExProjectDAO($dbh);
	
	$transactionResults = $exTransDao->getTransactionsByUidChronologically($uid);
	
	$results = array();
	
	if($transactionResults == null) {
		echo "<h3>No Finances Available</h3>";
	}
	
	foreach($transactionResults as $k => $v) {
		$toUsrResults = $exUsrDao->getUserByUid($v->to_uid);
		$fromUsrResults = $exUsrDao->getUserByUid($v->from_uid);
		$projResults = $exProjDao->getProjectByPid($v->pid);
		
		$toUsr = null;
		$fromUsr = null;
		$project = null;
		 
		if($toUsrResults != null) {
			$toUsr = $toUsrResults;
			$toUsr->password = null;
			$toUsr->email = null;
			$toUsr->phone =null;
		}
		
		if($fromUsrResults != null) {
			$fromUsr = $fromUsrResults;
			$fromUsr->password= null;
			$fromUsr->email = null;
			$fromUsr->phone = null;
		}
		else {
			$logger->error("Error: $uid has a transaction where there is no user for from_id.  Was the user deleted from the database? ");
			die("Error: Transaction with no from Id found");
		}
		
		if($projResults != null) {
			$project = $projResults[0];
		}
		else {
			$logger->error("error: $uid has a transaction for a project that is no longer in the database.  Was the project deleted from the database? ");
			die("Error: Transaction with no existing project.");
		}
		$project = $projResults[0];
		
		$results[] = array("transaction" => $v, "to_user" => $toUsr, "from_user" => $fromUsr, "project" => $project);
	}
	
	echo json_encode($results);
?>