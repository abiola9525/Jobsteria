<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExBidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");

	require_once("DBOConnection.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	
	if(!isset($_SESSION["uid"])) {
		die("Error: User is not logged in");
	}
	
	$uid = $_SESSION["uid"];
	
	$dbh = DBOConnection::getConnection();
	$exProjDao = new ExProjectDAO($dbh);
	$exBidDao = new ExBidDAO($dbh);
	$exUsrProjDao = new ExUserProjectDAO($dbh);
	
	$logger->debug("Getting Projects of user $uid");
	
	$projects = $exUsrProjDao->getUserProjectsByAttributeMapInRangeOrderedBy(array("uid" => $uid), 25, 0, "add_ts");
	$len = count($projects);
	
	
	$logger->debug("Found $len User Projects");
	
	if($len == 0) {
		die("<h3>No projects to manage at this time</h3>");
	}
	
	echo "<table border=\"1\">";
	echo "<tr><th>Job Title</th><th>Description</th><th>Average Bid</th><th>Created</th><th>Status</th><th>Job Link</th></tr>";
		
	for($i = 0; $i < $len; $i++) {
		$project = $projects[$i];
		
		$logger->debug("Trying to get Project of Pid " . $project->pid);
		
		$jobs = $exProjDao->getProjectByPid($project->pid);
		$job = $jobs[0];
		$avg = $exBidDao->getAverageBidPriceForProject($project->pid);
		
		if($avg == null) {
			$avg = "0.0";
		}
		$logger->debug("Calculated Average Bid $avg");
		
		echo "<tr><td>" . $job->name . "</td><td> " . $job->description . "</td><td>" . HtmlUtil::getCurrency($job->currency_type)  . $avg . "</td><td>" . 
				$job->add_ts . "</td><td>" . $job->status . "</td><td><a style=\"color: blue;\" href=\"JobView.php?jid=" . $job->pid . "\">Go</a></td></tr>";
		
		
	}
	
	echo "</table>";
	
?>