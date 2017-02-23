<?PHP
	session_start();
	require_once("./util/FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once("DBOConnection.php");
?>

<!--
@Description - My Website
@file - index.php
@date - 01/11/14
@author - Gary Drocella
-->

<html>
<head>
<title>Jobsteria!</title>
<style type="text/css">
	@import url("layout.css");
	@import url("http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css");
</style>

</head>
<body onload="init()">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<div id="header">
	<div id="links" style="width: auto">
		    <a href="index.php">Home</a>
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
		
	<h3>Welcome to My Jobs</h3>
	<p>Are you looking for jobs to do for payment?  Look no further! Bid on a job to work on today in your local town or anywhere you like.</p>


	</div>
	<script type="text/javascript">
		document.getElementById("linkMenu").style.height = document.getElementById("display").clientHeight;
		document.getElementById("linkMenu").style.display="none";
		document.getElementById("linkMenu").style.display="block";
	</script>
</div>
<style type="text/css">
.ui-menu { list-style:none; padding: 2px; margin: 0; display:block; outline: none;  }
.ui-menu .ui-menu { margin-top: -3px; position: absolute; }
.ui-menu .ui-menu-item { margin: 0; padding: 0; zoom: 1; width: 100%; }
.ui-menu .ui-menu-divider { margin: 5px -2px 5px -2px; height: 0; font-size: 0; line-height: 0; border-width: 1px 0 0 0; }
.ui-menu .ui-menu-item a { text-decoration: none; float:left  padding: 2px .4em; line-height: 1.5; zoom: 1; font-weight: normal; }
.ui-menu .ui-menu-item a.ui-state-focus,
.ui-menu .ui-menu-item a.ui-state-active { font-weight: normal; margin: -1px; }	
</style>

<script type="text/javascript">
	function init() {
		 $(function() {
			 $( "#menu" ).menu();
			 });
	}
</script>

</body>
</html>