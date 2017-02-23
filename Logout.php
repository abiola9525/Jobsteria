<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	
	session_start();
	
	$_SESSION["loggedIn"] = 0;
	session_unset();
	session_destroy();

    header("Location: http://" . FreeConfiguration::getInstance()->getProperty("base_url") . "index.php");
?>

