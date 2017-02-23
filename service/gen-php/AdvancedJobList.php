<?PHP
	require_once(dirname(__FILE__) . '\\..\\..\\util\\FreeConfiguration.php');
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "ProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once('../../DBOConnection.php');
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger("AdvancedJobList Service");
	
	$attrMap=array();
	$regexMap=array();
	$startDate = null;
	$endDate = null;
	
	if(!empty($_POST["budget_upper_bound"])) { 
		$logger->debug("Setting upper bound to " . $_POST["budget_upper_bound"]);
		$attrMap["budget_upper_bound"]=$_POST["budget_upper_bound"];
	}
	
	if(!empty($_POST["job_projected_end_date"])) {
		$endDate = $_POST["job_projected_end_date"];
	}

	if(!empty($_POST["job_start_date"])) {
		$startDate=$_POST["job_start_date"];
	}

	if(!empty($_POST["status"])) {
		$attrMap["status"]=$_POST["status"];
	}

	if(!empty($_POST["description"])) {
		$regexMap["description"]=$_POST["description"];
	}
		
	if(!empty($_POST["budget_lower_bound"])) {
		$logger->debug("Setting lower bound to " . $_POST["budget_lower_bound"]);
		$attrMap["budget_lower_bound"]=$_POST["budget_lower_bound"];
	}
	

	if(!empty($_POST["name"])) {
		$regexMap["name"]=$_POST["name"];
	}
	
	if(!empty($_POST["currency_type"])) {
		$attrMap["currency_type"]=$_POST["currency_type"];
	}

	if(!empty($_POST["pid"])) {
		$attrMap["pid"]=$_POST["pid"];
	}

	if(!empty($_POST["project_country"])) {
		$attrMap["project_country"] = $_POST["project_country"];
	}
	
	if(!empty($_POST["project_state"])) {
		$attrMap["project_state"] = $_POST["project_state"];
	}
	
	if(!empty($_POST["project_city"])) {
		$attrMap["project_city"] = $_POST["project_city"];
	}
	
	$cid = null;
	$scid= null;
	if(!empty($_POST["cid"])) {
		$cid = $_POST["cid"];
	}
	
	if(!empty($_POST["parent_cid"])) {
		$scid = $_POST["parent_cid"];
	}
	
	$rc = $_POST["rc"];
	$base = $_POST["base"];

	$dbh = DBOConnection::getConnection();
	$exProjDao = new ExProjectDAO($dbh);
	
	$logger->debug("Here with count $rc and base $base");
	$tups =  $exProjDao->advancedSearchOnProject($attrMap, $regexMap, $startDate, $endDate, $cid, $scid, $base, $rc);
	
	echo json_encode($tups);
?>
