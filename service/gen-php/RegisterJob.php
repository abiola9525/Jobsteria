<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "ProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectCategoryDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("Register Job Invoked");
	
	if(!isset($_POST["budget_upper_bound"]) || !isset($_POST["job_projected_end_date"]) || !isset($_POST["job_start_date"]) || !isset($_POST["description"]) || !isset($_POST["budget_lower_bound"]) || !isset($_POST["name"]) || !isset($_POST["currency_type"]) || !isset($_POST["remote"]) || !isset($_POST["cid"])) {
		die("Failed: Incomplete Request Invocation!");
	}
	
	$cid = $_POST["cid"];
	
	if(!empty($_POST["parent_cid"])) {
		$cid = $_POST["parent_cid"];
	}
	
	$remote = $_POST["remote"];
	
	if($remote == "N") {
		if(!isset($_POST["project_country"]) || !isset($_POST["project_state"]) || !isset($_POST["project_address"]) || !isset($_POST["project_city"])) {
			die("Failed: Incomplete Request Invocation!");
		}
	}
	
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	date_default_timezone_set('UTC');
	
	$logger->debug("Preparing attribute map.");
	
	$attrMap=array();
	$attrMap["budget_upper_bound"]=$_POST["budget_upper_bound"];
	$attrMap["job_projected_end_date"]=$_POST["job_projected_end_date"];
	$attrMap["job_start_date"]=$_POST["job_start_date"];
	$attrMap["description"]=$_POST["description"];
	$attrMap["budget_lower_bound"]=$_POST["budget_lower_bound"];
	$attrMap["name"]=$_POST["name"];
	$attrMap["currency_type"]=$_POST["currency_type"];
	$attrMap["status"]="BID";
	$attrMap["remote"]=$_POST["remote"];
	$attrMap["project_country"] = $_POST["project_country"];
	$attrMap["project_state"] = $_POST["project_state"];
	$attrMap["project_city"] = $_POST["project_city"];
	$attrMap["project_address"] = $_POST["project_address"];
	$attrMap["remote"] = $_POST["remote"];
	
	if($remote == "N") {
		$attrMap["project_country"] = $_POST["project_country"];
		$attrMap["project_state"] = $_POST["project_state"];
		$attrMap["project_city"] = $_POST["project_city"];
		$attrMap["project_address"] = $_POST["project_address"];
	}
	
	$today = date("Y-m-d H:i:s");
	$diff1week = new DateInterval('P7D');
	
	$d0 = new DateTime($today . "");
	$d0->add($diff1week);
	
	$attrMap["end_bid_date"] = $d0->format("Y-m-d H:i:s");
	$attrMap["deleted"] = "N";
	
	$logger->debug("Calculated End Bid Date " . $attrMap["end_bid_date"]);
	
	
	$dbh = DBOConnection::getConnection();
	$projDao = new ProjectDAO($dbh);
	$exUsrProjDao = new ExUserProjectDAO($dbh);
	
	try {
		$dbh->beginTransaction();
		
		$projDao->insertProject($attrMap);
		$results = $exUsrProjDao->insertByLastId($_SESSION["uid"]);
	
		if($results == null) {
			$dbh->rollBack();
			die("Error");		
		}
	
		$proj = $results[0];
	
		$logger->debug("Insert into Project Category Table " . $proj->pid . " and category id $cid");
		$projCat = new ExProjectCategoryDAO($dbh);
		$projCat->insertProject_category(array("pid" => $proj->pid, "cid" => $cid));
		
		$dbh->commit();
	}
	catch(Exception $e) {
		$dbh->rollBack();
		$logger->error("Error: $e");
		die("Error");
	}
?>
