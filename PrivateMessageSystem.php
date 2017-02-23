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
<head>
<title>GARY'S BLOG</title>
<style type="text/css">
@import url("layout.css");
</style>
</head>
<body onload="invokeGetPrivateMessagesService()">
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
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

	<div id="menu">
		<div class="button"><a style="width: 1%; left-align: auto; right-align: auto" href="javascript:showSend()">Send</a></div>
		<div class="button"><a style="width: 1%; left-align: auto; right-align: auto" href="">Delete</a></div>
	</div>
	<br />
	<div id="view">
	</div>
	<div id="content" style="display: none;">
		<form>
			<p>To: <input name="toUsername" id="toUsername" type="text" id="to" size="80" /></p>
			<p>Subject: <input name="subject" type="text" id="subject" size="80" /></p>
			<textarea name="message" id="msgContent" rows="15" cols="80">
			</textarea>
			<input style="float: right;" type="button" value="Send" onclick="invokeSendMessage()" />
		</form>
	</div>
</div>

<script type="text/javascript">
	function invokeGetPrivateMessagesService() {
		$.post("GetPrivateMessages.php", {}, function(data) {
			document.getElementById("view").innerHTML = data;
		} ) . fail(function () {alert("ERROR"); } ) ;
	}
	
	function invokeSendMessage() {
		alert("Invoke Send Message");
		
		var theToUsername = document.getElementById("toUsername").value;
		var theSubject = document.getElementById("subject").value;
		var theMessage = document.getElementById("msgContent").value;
		
		var json = {toUsername: theToUsername, subject: theSubject, message: theMessage};
		var func = function(data) { alert(data); document.getElementById("view").value = data; }

		$.post("http://localhost/MyFreeLancer/SendPrivateMessage.php", json, func);
	}
	
	function readMessage(index) {
		showSend();
		var msg = document.getElementById("message" + index).value;
		document.getElementById("msgContent").innerHTML = msg;
		
	}
	
	function showSend() {
		document.getElementById("content").style= "display; block";
	}
	
	function hideSend() {
		document.getElementById("content").style= "display; none";
	}
	
	function clearForm() {
	
	}
</script>

<style type="text/css">

	#view { width: 40%; margin-left: auto; margin-right: auto; margin-top: 50px }
	#menu { width: 40%; margin-left: auto; margin-right: auto; }
	#content { width: 60%; margin-left: auto; margin-right: auto; }
	.button {float: left; height: 25px; width: 100px;}
	
	.button:hover {}
	
</style>

</div>
<div id="credits">
<em>Created by - = Gary Drocella = -</em>
</div>
</body>
</html>