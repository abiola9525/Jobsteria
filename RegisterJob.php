<?PHP
	require_once('../../util/FreeConfiguration.php');
	
	session_start();
	
	if(!isset($_POST["jobTitle"]) || !isset($_POST["description"]) 
		 || !isset($_POST["lowerBound"]) || !isset($_POST["upperBound"]) || !isset($_POST["currencyType"])) {
		die("FAIL! :(");
	}
	
	if(!isset($_SESSION["uid"])) {
		die("Failed: User is not Authenticated.");
	}
	
	$uid = $_SESSION["uid"];
	
	$jobTitle = $_POST["jobTitle"];
	$description = $_POST["description"];
	$lowerBound = $_POST["lowerBound"];
	$upperBound = $_POST["upperBound"];
	$currencyType = $_POST["currencyType"];
	
	QueryUtility::insertNewJob(new Job(null, $jobTitle, $description, $upperBound, $lowerBound, $currencyType, 'BID', null, null, null));
	QueryUtility::insertNewUserProject($uid);
?>