<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExCategoryDAO.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	
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
<link href="jQueryAssets/jquery.ui.core.min.css" rel="stylesheet" type="text/css">
<link href="jQueryAssets/jquery.ui.theme.min.css" rel="stylesheet" type="text/css">
<link href="jQueryAssets/jquery.ui.button.min.css" rel="stylesheet" type="text/css">
<script src="jQueryAssets/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="jQueryAssets/jquery-ui-1.9.2.button.custom.min.js" type="text/javascript"></script>
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
<h1>Post Job</h1>
<div id="insertSuccessPane"></div>
<form name="post_job"  id="postJobForm" action="RegisterJob.php" method="POST">
	<div id="compSet1">
		<fieldset>
			<legend>Job Information</legend>
			<p>Job Title: <em class="required">*</em><input type="input" name="name" id="name" value="" size="80" /></p>
			<p>Description: <em class="required">*</em></p>
			<textarea rows="10" cols="80" name="description" id="description" wrap="hard"></textarea><br />
		</fieldset>
		<fieldset>
			<legend>Job Budget</legend>
		<p>Budget: <em class="required">*</em>
		<select name="currency_type" id="currency_type">
			<option value="">SELECT ONE</option>
			<option value="US">$</option>
			<option value="EURO">&#x20AC</option>
			<option value="IR">&#x20B9</option>
		</select>
		<select name="budget_lower_bound" id="budget_lower_bound">
			<option value="">SELECT</option>
			<option value="5-50">5-50</option>
			<option value="50-100">50-100</option>
			<option value="100-500">100-500</option>
			<option value="500-1000">500-1000</option>
			<option value="1000-5000">1000-5000</option>
			<option value="5000-10000">5000-10000</option>
			<option value="10000-0">more than 10000</option>
		</select>
		 </p>
		 </fieldset>
		 
		 <fieldset>
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
		 	<div id="subcategoryPane"></div>
		 	
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
					});
				}
		 	</script>
		 	
		 </fieldset>
		 
		<p>
        <fieldset>
        	<legend>Job Schedule</legend>
			Job Start Date:<em class="required">*</em><input class=".ui-datepicker" type="input" name="job_start_date" id="job_start_date" />
			Job Projected End Date:<em class="required">*</em><input class=".ui-datepicker" type="input" name="job_projected_end_date" id="job_projected_end_date" />
		</fieldset>
        </p>
		
        
		<fieldset>
			<legend>Job Site</legend>
			<p>Is the Job Remote?			  </p>
			<p>
		      <input type="radio" name="remote" id="remoteY" onclick="showSelect(1)" value="Y" />
			  
			  Yes<br />
			  <input type="radio" name="remote" id="remoteN" onclick="showSelect(2)" value="N" />No		  </p>
		</fieldset>
		
		<fieldset id="jobLocation" style="display:none;">
			<legend>Job Location</legend>
			
			
			
			<p>Country:<select name="project_country" id="project_country" onchange="invokeGetRegions(invkGetRegionsCb)">
				<?php 
					HtmlUtil::getCountryOptionSelection();
				?>
			</select></p>
			
			<div id="regionContainer" style="display:none;">
				<p>State/Region: <select name="project_state" id="project_state"  onchange="invokeGetCities(invkGetCitiesCb)">
				<option value="">SELECT ONE</option>
				<option value="AZ">Arizona</option>
				<option value="MD">Maryland</option>
				<option value="VA">Virginia</option>
			</select>
			</p>
			</div>
			<div id="cityContainer" style="display: none;">
			<p>City: <input type="text" name="project_city" id="project_city" /></p>
			<p>Address: <input type="text" name="project_address" id="project_address" /></p>
            
            </div>
            
		</fieldset>
		<input type="button" value="submit" onclick="invokeRegisterJob(invkRgJb)" />
	</div>

</form>

</div>
</div>
<script type="text/javascript" src="./lib/lib.js"></script>
<script type="text/javascript">

function showSelect(v) {
	if(v == 1) {
		document.getElementById("jobLocation").style.display="none";
	}
	else if(v == 2) {
		document.getElementById("jobLocation").style.display="block";	
	}
}

function getRadioValue(name) {
	var elms = document.getElementsByName(name);
	
	for(var i = 0; i < elms.length; i++) {
		if(elms[i].checked) {
			return elms[i].value;
		}

	}
	return null;

}
	
function invkRgJb(data) {
	
	if(data.length == 0) { 
		document.getElementById("insertSuccessPane").innerHTML = "<h1 style=\"color: green\">Job Post Success!</h1>";
	 }
	 else {
		 document.getElementById("insertSuccessPane").innerHTML = "<h1 style=\"color: red\">Job Post Failed!</h1>";
		 document.getElementById("postJobForm").style.display="none";
	 }
}
	
function invokeRegisterJob(cb) {

	var vjob_projected_end_date = document.getElementById("job_projected_end_date").value;
	var vjob_start_date = document.getElementById("job_start_date").value;
	var vdescription = document.getElementById("description").value;
	var vbudget_lower_bound = document.getElementById("budget_lower_bound").value;
	var vname = document.getElementById("name").value;
	var vcurrency_type = document.getElementById("currency_type").value;
	var base = "./service/gen-php/";

	var budget = vbudget_lower_bound.split("-");
	var vbudget_lower_bound = budget[0];
	var vbudget_upper_bound = budget[1];
	var vremote = getRadioValue("remote");
	var vproject_country = document.getElementById("project_country").value;
	var vproject_state = document.getElementById("project_state").value;
	var vproject_city = document.getElementById("project_city").value;
	var vproject_address = document.getElementById("project_address").value;
	var vcid = document.getElementById("cid").value;
	//var vpcid = document.getElementById("parent_cid").value;
	
	$.post(base + "RegisterJob.php",{budget_upper_bound: vbudget_upper_bound,
	 job_projected_end_date : vjob_projected_end_date,
	 job_start_date : vjob_start_date,
	 description : vdescription,
	 budget_lower_bound : vbudget_lower_bound,
	 name : vname,
	 currency_type : vcurrency_type,
	 remote : vremote,
	 project_country : vproject_country,
	 project_state : vproject_state,
	 project_city : vproject_city,
	 project_address : vproject_address,
	 cid: vcid
	 //parent_cid: vpcid 
	 }).done(function(data) {
		 
		 cb(data);
	 }).fail(function () {});
	 
}

function init() {
	 $(function() {
		 $( "#job_start_date" ).datepicker({dateFormat: 'yy-mm-dd'});});
	 
	 $(function () {
		 $( "#job_projected_end_date").datepicker({dateFormat: 'yy-mm-dd'});});
}

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

<style type="text/css">
	em.required { color: red; }
</style>

</body>
</html>