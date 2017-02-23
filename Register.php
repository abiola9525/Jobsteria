
<!--
@Description - My Website
@file - index.php
@date - 01/11/14
@author - Gary Drocella
-->
<?php 
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once("DBOConnection.php");
?>
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
<div id="successDisp" style="display: none;">
	<h2 style="color: green;">Registration Success</h2>
</div>
<h3>Register Form</h3>
<p>Please fill out the form below inorder to create an account, and start bidding!</p>

<div id="registerFormWrapper">

	<form  method="POST" action="service/gen-php/RegisterUser.php" enctype="multipart/form-data">
	
	<p>
	<fieldset style="width:50%">
	<legend>Basic Account Information</legend>
		Name:<input type="text" name="name" id="name" /><br />
		Username:<input type="text" name="username" id="username" /><br />
		Password:<input type="password" name="password" id="password" /><br />
	</fieldset>
	
	<fieldset style="width:50%">
		<legend>Location Information</legend>
		Country:<select name="country" id="country" onchange="invokeGetRegions(invkGetRegionsCb)"> 
		<?php 
					HtmlUtil::getCountryOptionSelection();
		?>
		</select>
		<br />
		<div id="regionContainer" style="display: none;">
		 State/Region:<select name="state_prov" id="state_prov" onchange="invokeGetCities(invkGetCitiesCb)">
		 	<option value="">SELECT ONE</option>
		 	<option value="AZ">Arizona</option>
		 	<option value="MD">Maryland</option>
		 	<option value="VA">Virginia</option>
		 </select>
		 </div>
		 <br />
		<div id="cityContainer" style="display: none;">
			<p>City:<input type="text" name="city" id="city" /></p>
			<p>Address:<input type="text" name="address" id="address" /></p>
			<p>Zipcode:<input type="text" name="zipcode" id="zipcode" /></p>
		</div>
	
		
		
	</fieldset>
	
	<fieldset style="width:60%">
		<legend>Contact Information</legend>
		E-mail:<input type="text" name="email" id="email" /><br />
		Phone:<input type="text" name="phone" id="phone" /><br />
		Birth Date:<input type="text" name="birth_date" id="birth_date" /><br />
	</fieldset>
	
	<fieldset style="width:60%">
		<legend>Extra Information</legend>
		Resume:<input type="file" name="resume_file_loc" id="resume_file_loc" /><br />
	
		Avitar:<input type="file" name="avitar" id="avitar" />
	
	</fieldset>
	
	<input type="submit" value="Submit" />
	
	</form>

	
	

		
	


</div>
</div>
</div>

<script type="text/javascript">

		function init() {
			 $(function() {
				 $( "#birth_date" ).datepicker({dateFormat: 'yy-mm-dd'});});
		}
		
		function invkeRgstrUsrCb(data) {
			document.getElementById("successDisp").style.display = "block";
			document.getElementById("registerFormWrapper").style.display = "none";
		}

		
		function invokeRegisterUser(cb) {
			var vphone = document.getElementById("phone").value;
			var vbirth_date = document.getElementById("birth_date").value;
			var vzipcode = document.getElementById("zipcode").value;
			var vresume_file_loc = document.getElementById("resume_file_loc").value;
			var vpassword = document.getElementById("password").value;
			var vcountry = document.getElementById("country").value;
			var vcity = document.getElementById("city").value;
			var vavitar = document.getElementById("avitar").value;
			var vstate_prov = document.getElementById("state_prov").value;
			var vusername = document.getElementById("username").value;
			var vaddress = document.getElementById("address").value;
			var vemail = document.getElementById("email").value;
			var vname = document.getElementById("name").value;

			var base = "./service/gen-php/";

			var ts = Date.parse(vbirth_date);
			
			if(isNaN(ts)) {
				alert("Error: You entered an invalid date for the birthday field");
				return;
			}
				
			
			$.post(base + "RegisterUser.php",{phone : vphone,
				birth_date : vbirth_date,
				zipcode : vzipcode,
				resume_file_loc : vresume_file_loc,
				password : vpassword,
				country : vcountry,
				city : vcity,
				avitar : vavitar,
				state_prov : vstate_prov,
				username : vusername,
				address : vaddress,
				email : vemail,
				name : vname
				}).done(function(data) { cb(data); }).fail(function () { alert("Error!");});
		}
				

		function invkGetRegionsCb(data) {
			alert(data);
			document.getElementById("state_prov").innerHTML = data;
			document.getElementById("regionContainer").style.display = "block";
			
		}

		function invokeGetRegions(cb) {
			var vcountry_id = document.getElementById("country").value;
			var base = "service/gen-php/";
			$.post(base + "GetRegions.php",{project_country : vcountry_id}).done(function(data) { cb(data); }).fail(function () {});
		}

		function invkGetCitiesCb(data) {
			var obj = $.parseJSON(data);
			
			$("#project_city").autocomplete({source:obj});
			document.getElementById("cityContainer").style.display = "block";
		}

		function invokeGetCities(cb) {
			var vcountry_id = document.getElementById("country").value;
			var vregion_id = document.getElementById("state_prov").value;

			$.post("GetCities.php", {project_country : vcountry_id, project_state : vregion_id}).done(function(data) { cb(data); });
		}
		
</script>

<style type="text/css">
	em.required { color: red; }
	
	div#compSet1 { float: left; }
	
	div#registerFormWrapper { margin-left: auto; margin-right: auto;}
</style>

</body>
</html>