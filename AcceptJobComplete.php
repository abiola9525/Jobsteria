<?php
	/**
	 * @author Gary Drocella
	 * @date 09/06/2014
	 * Time: 01:06pm
	 */

	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "TransactionDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	date_default_timezone_set('UTC');
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("Accept Job Complete");
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User not Authenticated.");
	}
	
	if(!isset($_POST["pid"])) {
		die("Error: Incomplete Invocation Request");
	}
	
	$uid = $_SESSION["uid"];
	$pid = $_POST["pid"];
	
	$dbh = DBOConnection::getConnection();
	$exAward = new ExUserProjectAwardDAO($dbh);
	$exUsrProj = new ExUserProjectDAO($dbh);
	$exProj  = new ExProjectDAO($dbh);
	$exBid = new ExBidDAO($dbh);
	
	/* First, ensure the person who is invoking really owns the job... */
	
	$results = $exUsrProj->getUser_projectByAttributeMap(array("pid" => $pid));
	
	if($results == null) {
		die("Error: Project doesn't exist.");
	}
	
	$usrProj = $results[0];
	
	if($usrProj->uid != $uid) {
		$logger->error("Error: User $uid attempted to accept job complete for true job [ $pid ] owner [" . $usrProj->uid . "]");
		die("Error: User does not own job.");
	}
	
	$results = $exAward->getUser_project_awardByAttributeMap(array("pid" => $pid));
	
	if($results == null) {
		$logger->error("Error: User $uid attempted to accept job completion for job to [$pid] whom no one has been hired.");
		die("Error: User has not been hired to do job yet.");
	}
	
	$award = $results[0];
	
	if($award->project_complete_request == "N") {
		$logger->error("Error: Jobs can only be accepted as completed when the employee makes a job completed request.");
		die("Error: Jobs can only be accepted as completed when the employee makes a job completed request.");
	}
	
	$logger->debug("Search for project with pid=$pid");
	$results = $exProj->getProjectByPid($pid);
	
	if($results == null) {
		$logger->error("Error: Project Not Found.");
		die("Error: Job was not found in the database. ");
	}
	
	$project = $results[0];
	
	if($project->status == "FIN" || $project->status == "FIN_CLS" || $project->status == "CLS") {
		$logger->error("Error: This job [$pid] is marked as Finished or Closed. Invoked by [$uid]");
		die("Error: This job is marked as finished or closed.");
	}
	
	
	try {
		
		$dbh->beginTransaction();
		
		/** TODO: Then update the job status as FIN */
	
		$exProj->updateProject(array("status" => "FIN"), array("pid" => $pid));
	
		$logger->debug("Project $pid has an update status of Finished.");
	
		/** TODO: Peform actual transaction. */
	
		/** TODO: Update Transaction Table */
	
		$results = $exBid->getBidByAttributeMap(array("uid" => $award->uid, "pid" => $pid));
		
		if($results == null) {
			$dbh->rollBack();
			$logger->error("Error: Rolling back because there is no bid associated with the hired user " . $award->uid . " for project $pid");
			die("Error: Could not accept job offer.");	
		}
		$bid = $results[0];
		
		$transactionDao = new TransactionDAO($dbh);
		
		$results = $transactionDao->getTransactionByAttributeMap(array("pid" => $pid, "from_uid" => $uid, "to_uid" => $award->uid));
		
		if($results == null) {
			$dbh->rollBack();
			$logger->error("Error: No record of milestone transaction. for pid=$pid uid=$uid to_uid= " . $award->uid);
			die("Error: Could not find a milestone transaction.");
		}
		
		$msTransaction = $results[0];
		$prevCharge = $msTransaction->amount;
		$newCharge  = $bid->amount - $prevCharge; //This will calculate the remaining charge.
		
		$dt = new DateTime();
		
		$attrMap = array();
		$attrMap["pid"] = $pid;
		$attrMap["from_uid"] = $uid;
		$attrMap["to_uid"]= $award->uid;
		$attrMap["amount"] = $newCharge;
		$attrMap["status"] = "APPROVED";
		$attrMap["date"] = $dt->format("Y-m-d");
		$attrMap["type"] = "JOB_FIN";
		$transactionDao->insertTransaction($attrMap);
		
		$logger->debug("Updating transaction to the database. ");
		$dbh->commit();
	
	}
	catch(Exception $e) {
		$dbh->rollBack();
		$logger->error("Error: Could not Accept Job Offer. Request made by User [$uid] and Project [$pid].\nException: $e");
		die("Error: Failed to Accept Job Offer");
	}
	
	//$transactionDao->
	
	
?>