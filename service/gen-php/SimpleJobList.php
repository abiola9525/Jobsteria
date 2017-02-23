<?PHP
	require_once('../../util/FreeConfiguration.php');
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
	require_once('../../DBOConnection.php');
	
	if(!isset($_POST["base"]) || !isset($_POST["rc"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}

	$attrMap=array();
	$attrMap["base"]=$_POST["base"];
	$attrMap["rc"]=$_POST["rc"];

	$dbh = DBOConnection::getConnection();
	$projDao = new ExProjectDAO($dbh);
	$tups = $projDao->getProjectByAttributeMapInRangeOrderedBy(array("deleted" => "N"), $attrMap["rc"],$attrMap["base"], "add_ts");
	
	echo json_encode($tups);
?>
