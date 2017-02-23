<?PHP
	require_once(dirname(__FILE__) . '\\..\\..\\util\\FreeConfiguration.php');
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(dirname(__FILE__) . '\\..\\..\\DBOConnection.php');
	
	session_start();
	
	if( !isset($_POST["phone"]) || !isset($_POST["birth_date"]) || !isset($_POST["zipcode"])  || !isset($_POST["password"]) || !isset($_POST["country"]) || !isset($_POST["city"])  || !isset($_POST["state_prov"]) || !isset($_POST["username"])  || !isset($_POST["address"]) || !isset($_POST["email"]) || !isset($_POST["name"]) ) {
		die("Failed: Incomplete Request Invocation!");
	}
	
	if(!empty($_FILES["avitar"])) {
		$photoName = time() . $_FILES["avitar"]["name"];
		move_uploaded_file($_FILES["avitar"]["tmp_name"],  "../../imgs/photos/" . $photoName);
	}
	else {
		$photoName = "../man-silhouette.jpg";
	}
	
	$attrMap=array();
	$attrMap["phone"]=$_POST["phone"];
	$attrMap["birth_date"]=$_POST["birth_date"];
	$attrMap["zipcode"]=$_POST["zipcode"];
	//$attrMap["resume_file_loc"]=$_POST["resume_file_loc"];
	$attrMap["password"]=md5($_POST["password"]);
	$attrMap["country"]=$_POST["country"];
	$attrMap["city"]=$_POST["city"];
	$attrMap["state_prov"]=$_POST["state_prov"];
	$attrMap["username"]=$_POST["username"];
	$attrMap["address"]=$_POST["address"];
	$attrMap["email"]=$_POST["email"];
	$attrMap["name"]=$_POST["name"];
	$attrMap["avitar"] = $photoName;
	 
	$dbh = DBOConnection::getConnection();
	
	$userDao = new UserDAO($dbh);
	$userDao->insertUser($attrMap);

	header("Location: http://" . FreeConfiguration::getInstance()->getProperty("base_url") . "index.php");
?>
