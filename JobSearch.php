<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");	
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExCategoryDAO.php");
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
<h1>Advanced Job Search</h1>
<form id="advanceSearchForm" method="POST">
	<fieldset>
		<legend>Search Type</legend>
		<input type="radio" name="search_type" id="search_type" value="1" onclick="showSelect(1)" />Search By Job Id<br />
		<input type="radio" name="search_type" id="search_type" value="2" onclick="showSelect(2)" />Search By Job Criteria<br />
	</fieldset>
	<fieldset id="idSrch" class="hide">
		<legend>Search With Id</legend>
		<p>Job Id:<input type="text" name="pid" id="pid" /></p>
	</fieldset>
	
	<fieldset id="txtSrch" class="hide">
		<legend>Search For Text</legend>
		<p>Containing Name:<input type="text" name="name" id="name" /> 
		Containing Description:<input type="text" name="description" id="description" /></p>
	</fieldset>
	
			 
		 <fieldset id="catSrch" class="hide">
		 	<legend>Job Category</legend>
		 	<?php 
		 		$dbh = DBOConnection::getConnection();
		 		$exCatDao = new ExCategoryDAO($dbh);
		 		$results = $exCatDao->getCategoriesLexicographically();
		 		
		 		echo "<p>Category: <select name=\"cid\" id=\"cid\" onchange=\"changeIt(this)\">";
		 		echo "<option value=\"\">SELECT ONE</option>";
		 		
		 		foreach($results as $k => $v) {
					if($v->parent_cid != null) {
						continue;
					}
					echo "<option value=\"" . $v->cid . "\"> " . $v->name . "</option>";
					
				}
		 		
		 		echo "</select></p>";
		 	?>
		 	<div id="subcategoryPane" style="display: none;"><select id="parent_cid"><option value="">SELECT ONE</option></select></div>
		 	
		 	<script type="text/javascript">
				function changeIt(v) {
					$.post("GetSubCategory.php",{cid: v.value}).done(function(data) {

						var obj = $.parseJSON(data);
						var code = "<p>Subcategory: <select id=\"parent_cid\" name=\"parent_cid\">";
						code += "<option value=\"\">SELECT ONE</option>";
						
						for(var i = 0; i < obj.length; i++) { 
							code += "<option value=\"" + obj[i].cid + "\" >" + obj[i].name + "</option>";
						}
						
						code += "</select></p>";

						document.getElementById("subcategoryPane").innerHTML = code;
						document.getElementById("subcategoryPane").style.display = "block";
					});
				}
		 	</script>
		 	
		 </fieldset>
	<fieldset id="locSrch" class="hide">
		<legend>Search Location</legend>
		Country:<select name="project_country" id="project_country" onchange="invokeGetRegions(invkGetRegionsCb)"> 
		<?php 
					HtmlUtil::getCountryOptionSelection();
		?>
		</select>
		<br />
		<div id="regionContainer" style="display: none;">
		 State/Region:<select name="project_state" id="project_state" onchange="invokeGetCities(invkGetCitiesCb)">
		 	<option value="">SELECT ONE</option>
		 	<option value="AZ">Arizona</option>
		 	<option value="MD">Maryland</option>
		 	<option value="VA">Virginia</option>
		 </select>
		 </div>
		 <br />
		<div id="cityContainer" style="display: none;">
			<p>City:<input type="text" name="project_city" id="project_city" /></p>
		</div>
		
		<script type="text/javascript">
		function invkGetRegionsCb(data) {
			document.getElementById("project_state").innerHTML = data;
			document.getElementById("regionContainer").style.display = "block";
			
		}

		function invokeGetRegions(cb) {
			var vcountry_id = document.getElementById("project_country").value;
			var base = "service/gen-php/";
			$.post(base + "GetRegions.php",{project_country : vcountry_id}).done(function(data) { cb(data); }).fail(function () {});
		}

		function invkGetCitiesCb(data) {
			var obj = $.parseJSON(data);
			
			$("#project_city").autocomplete({source:obj});
			document.getElementById("cityContainer").style.display = "block";
		}

		function invokeGetCities(cb) {
			var vcountry_id = document.getElementById("project_country").value;
			var vregion_id = document.getElementById("project_state").value;

			$.post("GetCities.php", {project_country : vcountry_id, project_state : vregion_id}).done(function(data) { cb(data); });
		}
		
		</script>
	</fieldset>
	
	<fieldset id="crtSrch" class="hide">
		<legend>Search For Other Criteria</legend>
		<p>With Status:<select name="status" id="status">
			<option value="">SELECT ONE</option>
			<option value="BID">Open</option>
			<option value="BID_CLS">Bid Closed</option>
			<option value="PRE_HIRE">Hiring</option>
			<option value="IN_PROG">In Progress</option>
			<option value="FIN">Finished</option>
			<option value="CLS">Closed</option>
			<option value="FIN_CLS">Finished and Closed</option>
		</select></p>
		<p>With Budget Range and Currency: <em class="required">*</em>
		<select name="currency_type" id="currency_type">
			<option value="">SELECT ONE</option>
			<option value="US">$</option>
			<option value="EURO">&#x20AC</option>
			<option value="IR">&#x20B9</option>
		</select>
		<select name="budget_lower_bound" id="budget_lower_bound">
			<option value="">SELECT ONE</option>
			<option value="5-50">5-50</option>
			<option value="50-100">50-100</option>
			<option value="100-500">100-500</option>
			<option value="500-1000">500-1000</option>
			<option value="1000-5000">1000-5000</option>
			<option value="5000-10000">5000-10000</option>
			<option value="10000-0">more than 10000</option>
		</select>
		 </p>
		<p>Between Project Start Date:<input type="text" name="job_projected_end_date" id="job_projected_end_date" />
		And Project End Date:<input type="text" name="job_start_date" id="job_start_date" /></p>
	</fieldset>
	
	
	<input type="button" onclick="invokeAdvancedJobList(invkAdvJbLstCb)" value="Search" /><br />
	
	<input type="hidden" name="base" id="base" value="0" />
	<input type="hidden" name="rc" id="rc" value="50" />
</form>
<div id="jobContainer" >

	

</div>
	<div id="menu" style="display: none;">
			<div id="left">
				<a href="javascript:invokeSimpleJobList(invkSmpJbLstCb, 0)">Previous</a>
			</div>
			<div id="right">
				<a href="javascript:invokeSimpleJobList(invkSmpJbLstCb, 1)">Next</a>
			</div>
	</div>
</div>

</div>
<script type="text/javascript" src="./lib/lib.js"></script>
<style type="text/css">
	#jobContainer {width: 70%; margin-left: auto; margin-right: auto;}
	#menu {width: 70%; margin-left: auto; margin-right: auto;}
	#left { float: left; }
	#right { float:right;}
	#advanceSearchForm { width:70%; }
	
	.hide {display: none;}
	.show {display: block;}
</style>
<script type="text/javascript">

function showSelect(val) {
	if(parseInt(val) == 1) {
		document.getElementById("idSrch").className="show";
		document.getElementById("txtSrch").className="hide";
		document.getElementById("crtSrch").className="hide";
		document.getElementById("locSrch").className="hide";
		document.getElementById("catSrch").className="hide";
	}
	if(parseInt(val) == 2) {
		document.getElementById("idSrch").className="hide";
		document.getElementById("txtSrch").className="show";
		document.getElementById("crtSrch").className="show";
		document.getElementById("locSrch").className = "show";
		document.getElementById("catSrch").className = "show";
	}
}
	
function invkAdvJbLstCb(data) {
	alert("invkAdvJbLstCb");
	invkSmpJbLstCb(data);
	document.getElementById("menu").style.display="block";
}

function invokeAdvancedJobList(cb) {
	var vjob_projected_end_date = document.getElementById("job_projected_end_date").value;
	var vjob_start_date = document.getElementById("job_start_date").value;
	var vstatus = document.getElementById("status").value;
	var vdescription = document.getElementById("description").value;
	var vbudget_lower_bound = document.getElementById("budget_lower_bound").value;
	var vname = document.getElementById("name").value;
	var vbase = document.getElementById("base").value;
	var vcurrency_type = document.getElementById("currency_type").value;
	var vpid = document.getElementById("pid").value;
	var vrc = document.getElementById("rc").value;
	var vproject_country = document.getElementById("project_country").value;
	var vproject_state = document.getElementById("project_state").value;
	var vproject_city = document.getElementById("project_city").value;
	var vcid = document.getElementById("cid").value;
	var vparent_cid = document.getElementById("parent_cid").value;
	var base = "service/gen-php/";

	var budget = vbudget_lower_bound.split("-");
	var vbudget_upper_bound = budget[1];
	vbudget_lower_bound = budget[0];

	alert("Invoking");
	
	$.post(base + "AdvancedJobList.php",{budget_upper_bound : vbudget_upper_bound,
		job_projected_end_date : vjob_projected_end_date,
		job_start_date : vjob_start_date,
		status : vstatus,description : vdescription,
		budget_lower_bound : vbudget_lower_bound,
		name : vname,base : vbase,
		currency_type : vcurrency_type,
		pid : vpid,
		rc : vrc,
		project_country: vproject_country,
		project_state: vproject_state,
		project_city: vproject_city,
		cid: vcid,
		parent_cid: vparent_cid
		}).done(function(data) { cb(data); }).fail(function () {  });
}

function invkSmpJbLstCb(data) {
	alert(data);
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

	alert(code);
	document.getElementById("jobContainer").innerHTML = code;
	
}

function init() {
	 $(function() {
		 $( "#job_start_date" ).datepicker({dateFormat: 'yy-mm-dd'});});
	 
	 $(function () {
		 $( "#job_projected_end_date").datepicker({dateFormat: 'yy-mm-dd'});});
}
</script>

</body>
</html>