<?PHP
	require_once 'QueryUtility.php';
	require_once 'PrivateMessage.php';
	
	session_start();
	
	if(!isset($_SESSION["uid"])) {
		die("User is not Authenticated");
	}

	
	$fromUid = $_SESSION["uid"];
	$toUsername = $_POST["toUsername"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];

	$toUid = QueryUtility::getUidByUsername($toUsername);
	
	$pm = new PrivateMessage($fromUid, $toUid, $subject, $message, null, null, null);
	
	QueryUtility::insertPrivateMessage($pm);
	
	echo "Success";
	
?>