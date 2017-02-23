<?PHP
	require_once(dirname(__FILE__) . "\\..\\..\\DBOConnection.php");
	require_once(dirname(__FILE__) . "\\..\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "BidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir"). "CountriesDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	session_start();
	
	Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
	
	$logger = Logger::getLogger("GetBidsService");
	
	if(!isset($_POST["pid"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}

	$attrMap=array();
	$attrMap["pid"]=$_POST["pid"];

	$dbh = DBOConnection::getConnection();
	$bidDao = new BidDAO($dbh);
	$exUserDao = new ExUserDAO($dbh);
	$countryDao = new CountriesDAO($dbh);
	
	$logger->debug("Invoking getBidByAttributeMap with pid" . $_POST["pid"]);
	$tups = $bidDao->getBidByAttributeMap($attrMap);
	
	$retObj = array();
	foreach($tups as $k => $v) {
		$u = $exUserDao->getUserByUid($v->uid);
		$cr = $countryDao->getCountriesByAttributeMap(array("CountryID" => $u->country));
		$u->password = null; // important to make sure we don't return user password.
		
		$c = null;
		if($cr != null) {
			$c = $cr[0];
		}
		
		$retObj[] = array("user" => $u, "bid" => $v, "country" => $c);
	}
	
	echo json_encode($retObj);
?>
