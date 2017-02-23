<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	
	session_start();

	if(!isset($_SESSION["uid"])) {
		die("Error: user is not logged in");
	}
	
	$uid = $_SESSION["uid"];
	
	$dbh = DBOConnection::getConnection();
	$exBidDao = new ExBidDAO($dbh);
	$bids = $exBidDao->getBidsByUid($uid);			
	
	$exProjDao = new ExProjectDAO($dbh);
	
	echo "<table border=\"1\">\n";
	echo "<tr><th>Job Title</th><th>Description</th><th>Bid Amount</th><th>Bid Date</th><th>Job Link</th></tr>";

	$len = count($bids);
	for($i = 0; $i < $len; $i++) {
		$bid = $bids[$i];
		$jobs = $exProjDao->getProjectByPid($bid->pid);
		$job = $jobs[0];			
		echo "<tr><td>" . $job->name . "</td><td>" . $job->description . "</td><td>" . $bid->amount . "</td><td>" . $bid->add_ts
		 . "</td><td><a href=\"JobView.php?jid=" . $bid->pid . "\">Go</a></td></tr>\n";		
	}
	
	echo "</table>\n";
				
?>