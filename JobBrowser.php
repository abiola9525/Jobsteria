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
@import url("http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css");
</style>
</head>
<body onload="invokeSimpleJobList(invkSmpJbLstCb, -1)">
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
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

	<form>
		<input type="hidden" id="base" name="base" value="0" />
		<input type="hidden" id="rc" name="rc" value="50" />
	</form>
</div>


<div id="mainContainer">
	<div id="linkMenu">
	<script type="text/javascript">
		$(function () {
				$("#nav_menu").menu();
			} 
		);
	</script>
		<ul id="nav_menu">
			<li>Search Jobs
				<ul>
					<li><a href="JobBrowser.php">Browse Jobs</a></li>
					<li><a href="JobBrowseCategory.php">Browse Job By Category</a></li>
					<li><a href="JobSearch.php">Advanced Job Search</a></li>
				</ul>
			</li>
			
			<li><a href="PostJob.php">Post Job</a></li>
			
			
		</ul>
	</div>
<div id="display">

	<div id="jobContainer" >

	

	</div>
	<div id="menu">
			<div id="left">
				<a href="javascript:invokeSimpleJobList(invkSmpJbLstCb, 0)">Previous</a>
			</div>
			<div id="right">
				<a href="javascript:invokeSimpleJobList(invkSmpJbLstCb, 1)">Next</a>
			</div>
	</div>

<style type="text/css">
	#jobContainer {width: 70%; margin-left: auto; margin-right: auto;}
	#menu {width: 70%; margin-left: auto; margin-right: auto;}
	#left { float: left; }
	#right { float:right;}
</style>

<script type="text/javascript" src="./lib/lib.js"></script>

<script type="text/javascript">
function invkSmpJbLstCb(data) {

	var code = "<table name=\"jobTable\" border=\"1\">\n";
	code += "<tr><th>Name</th><th>Description</th><th>Budget</th><th>Status</th><th>Created</th></tr>";
	var obj = $.parseJSON(data);

	if(obj.length == 0) {
		document.getElementById("jobContainer").innerHTML = "<h1>No More Jobs Available Past this Point</h1>";
		return;
	}
	
	for(var i = 0; i < obj.length; i++) {
		var currJob = obj[i];
		var status = getStatus(currJob.status);
		var currency = getCurrency(currJob.currency_type);
		
		code += "<tr><td><a href=\"./JobView.php?jid=" + currJob.pid + "\">" + currJob.name;
		code += "</a></td><td>" + currJob.description + "</td><td>" + currency + currJob.budget_lower_bound + "-" + currency + currJob.budget_upper_bound + "</td><td>";
		code += status + "</td><td>" + currJob.job_start_date + "</td>";
		code += "</tr>\n";
	} 
	code += "</table>\n";

	document.getElementById("jobContainer").innerHTML = code;
	
}

function invokeSimpleJobList(cb, v) { 
	var base = "./service/gen-php/";
	var vbase = parseInt(document.getElementById("base").value);
	var vrc = parseInt(document.getElementById("rc").value);

	if(v < 0) {
		vbase =0;
	}
	else if(v == 0 && vbase-vrc >= 0) {
		vbase -= vrc;
	}
	else if(v > 0) {
		vbase += vrc;
	}
	else {
		return;
	}

	
	
	$.post(base + "SimpleJobList.php",{base : vbase,rc : vrc}).done(function(data) { cb(data);}).fail(function () { alert("Fail!"); });
	document.getElementById("base").value = vbase + "";
	document.getElementById("rc").value = vrc + "";
}
	
</script>


</div>
</div>
</body>
</html>