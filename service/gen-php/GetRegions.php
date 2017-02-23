<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "RegionsDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger(__FILE__);
	
	$logger->debug("GetRegions Invoked");
	
	if(!isset($_POST["project_country"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}
	

	
	$attrMap=array();
	$attrMap["CountryId"]=$_POST["project_country"];

	$dbh = DBOConnection::getConnection();
	$regionsDao = new RegionsDAO($dbh);
	
	$results = $regionsDao->getRegionsByAttributeMap($attrMap);
	
	$logger->debug("Got Results " . json_encode($results));
	
	echo "<option value=\"\">SELECT ONE</option>";
	
	foreach ($results as $v) {
		echo "<option value=\"" . $v->RegionID . "\">" . $v->Region . "</option>";
	}
	
?>
