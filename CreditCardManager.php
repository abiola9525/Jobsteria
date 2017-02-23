<?PHP
	require_once("./util/FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once("DBOConnection.php");
	session_start();

?>

<!--
@Description - My Website
@file - index.php
@date - 01/11/14
@author - Gary Drocella
-->

<html>
<!-- Credit Card Manager -->
<head>
<title>GARY'S BLOG</title>
<style type="text/css">
@import url("layout.css");
</style>
</head>
<body>
<div id="header">
	<div id="links">
		<a href="index.php">Home</a>
		<a href="JobBrowser.php">Browse Jobs</a>
		<a href="PostJob.php">Post Job</a>
	</div>
	<?PHP
	if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"]==1) {
		$dbh = DBOConnection::getConnection();
		$userDao = new UserDAO($dbh);
		$u = $userDao->getUserByUid($_SESSION["uid"]);
		$user = null;

		if(count($u) > 0) {
			$user = $u[0];
		}
		else {
			die("Odd Error Occured");
		}
		
		echo "<div id=\"login\">\n";
		echo "<h3>Welcome <a href=\"UserProfile.php\">" . $user->name . "</a></h3>\n";
		echo "<a href=\"Logout.php\">logout</a>";
		echo "</div>\n";
	}
	else {
		echo "<div id=\"login\">\n";
		echo "	<form name=\"login\" action=\"UserProfile.php\" method=\"POST\">\n";
		echo "  <p>Username:<input type=\"text\" name=\"uname\" /></p>\n";
		echo "	<p>Password:<input type=\"password\" name=\"pass\" /></p>\n";
		echo "	<input type=\"submit\" name=\"submit\" value=\"login\" />\n";
		echo "<a href=\"Register.php\">Register</a>\n";
		echo "</form>\n";
		echo "</div>\n";
	}
	
	?>
</div>

<div id="display">
<p>Credit Card Information</p>
<form>
Name on Card: <input name="cardName" type="text" />
Account Number: <input name="cardAccountNumber" type="text" /><br />
Expiration Date: <input name="cardExpirationMonth" type="text" /> <input name="cardExpirationYear" type="text" /><br />
Security Code: <input name="securityCode" type="text" /><br />
Card Type: <input name="merchantType" type="text" /><br />
<input type="submit" value="Add Card" />
</form>
</div>



</div>
<div id="credits">
<em>Created by - = Gary Drocella = -</em>
</div>
</body>
</html>