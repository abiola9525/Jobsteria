<?PHP
	session_start();
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CategoryDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExProjectDAO.php");
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
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<div id="header">
	<div id="links" style="width: auto">
		    <a href="index.php">Home</a>|
			<a href="JobBrowser.php">Browse Jobs</a>|
			<a href="PostJob.php">Post Job</a>|
			<a href="JobSearch.php">Advanced Job Search</a>|
			<a href="JobBrowseCategory.php">Browse Job By Category</a>
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
	<h1>Browse Jobs By Category</h1>
	<div id="categoryPane" style="width: 100%">
		<?php 
			$dbh = DBOConnection::getConnection();
			$catDao = new CategoryDAO($dbh);
			$exProjDao = new ExProjectDAO($dbh);
			
			$categories = $catDao->getAllCategorys();
			
			foreach($categories as $k => $v) {
				if($v->parent_cid != null) {
					continue;
				}
				echo "<div id=\"" . $v->name . "Pane\">";
				echo "<b><a href=\"javascript:browseCategory(" . $v->cid . ")\">" . ucfirst($v->name) ."</a></b><br />";
				
				
				
				$subCat = $catDao->getCategoryByAttributeMap(array("parent_cid" => $v->cid));
				foreach($subCat as $p => $q) {
					echo "<div style=\"float: left; width: 10%; height: 2%;\">";
					echo "<a href=\"javascript:browseCategory(" . $q->cid . ")\">" . ucfirst($q->name) . "(" . $exProjDao->getJobCountByCid($q->cid) .")</a>";
					echo "</div>";
				}
				echo "</div>";
				echo "<br />";
			}
			
		?>
	</div>
	<div id="browsePane" style="display: none;">
		<a href="javascript:revert()">Back</a>
		<div id="jobListPane">
		</div>
	</div>
</div>

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
<script type="text/javascript" src="lib/lib.js"></script>
<script type="text/javascript">
	function init() {
		 $(function() {
			 $( "#nav_menu" ).menu();
			 });
	}

	function browseCategory(v) {
		$.post("./CategoryJobs.php", {cid: v}).done(function(data) { 
			var obj = $.parseJSON(data);
			var code = "<table border=\"1\">";
			code += "<tr><th>Name</th><th>Description</th><th>Budget</th><th>Status</th><th>Created</th></tr>";
			
			for(var i =0; i < obj.length; i++) {
				var project = obj[i];
				code += "<tr><td><a href=\"./JobView.php?jid=" + project.pid + "\">" + project.name + "</a></td><td>" + project.description + "</td><td>" + getCurrency(project.currency_type) + project.budget_lower_bound + "-" + getCurrency(project.currency_type) + project.budget_upper_bound + "</td>";
				code += "<td>" + getStatus(project.status) + "</td><td>" + project.add_ts + "</td></tr>";
			}

			code += "</table>";
			
			
			document.getElementById("browsePane").style.display = "block";
			
			var node = document.getElementById("jobListPane");
			node.innerHTML = code;
			
			document.getElementById("categoryPane").style.display = "none";
		 });
	}

	function revert() {
		document.getElementById("browsePane").style.display = "none";
		document.getElementById("categoryPane").style.display = "block";
	}
</script>

</body>
</html>