<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "ProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "User_projectDAO.php" );
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "BidDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectAwardDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CountriesDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") .  "ExCitiesDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "RegionsDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(dirname(__FILE__) . "\\DBOConnection.php");
	
	session_start();
?>

<!--
@Description - My Website
@file - index.php
@date - 01/12/14
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
<body onload="invokeGetBid(invkGetBidCb)">
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script src="lib/lib.js" type="text/javascript"></script>
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

<?PHP
	
	if(!empty($_GET["jid"])) {
	
		$dbh = DBOConnection::getConnection();
		$projDao = new ProjectDAO($dbh);
		$exUsrProjDao = new ExUserProjectDAO($dbh);
		
		$jid = $_GET["jid"];
		$projs = $projDao->getProjectByPid($jid);
		
		if(count($projs) <= 0) {
			die("<h1>Error: Job Id does not Exist</h1>");
		}
		
		$project = $projs[0];
		
		date_default_timezone_set('UTC');
		
		 
		$nowTime = new DateTime();
		$endTime = new DateTime($project->end_bid_date);
		$interval = $nowTime->diff($endTime, true);
		
		if($nowTime >= $endTime && $project->status == "BID") {
			$projDao->updateProject(array("status" => "BID_CLS"), array("pid" => $jid));
		}

		$currencyType = HtmlUtil::getCurrency($project->currency_type);
		
	
		
		echo "<div id=\"jobInfoPane\" style=\"width: 100%; display: inline-block;\">";
		
		echo "<div id=\"jobDescription\" style=\"float: left; width: 60%;\">\n";
		echo "<h1>" . $project->name . "</h1><br />\n";
		
		if(isset($_SESSION["uid"])) {
			if($exUsrProjDao->userOwnsProject($_SESSION["uid"], $jid)) {
				echo "<div id=\"userControl\">";
				echo "<a href=\"javascript:invokeDeleteJob(deleteJbCb)\">Delete Job</a> | \n";
				echo "<a href=\"UpdateJob.php?jid=$jid\">Update Job</a>\n";
				echo "</div>";
			}
			}
		
		echo "<p><b>Job Id:</b> " . $project->pid . "</p>\n";
		echo "<p><b>Job Site:</b> " . ($project->remote == "Y" ? "Remote" : "Local");
		
		if($project->remote == "N") {

			$countryDao = new CountriesDAO($dbh);
			$regionDao = new RegionsDAO($dbh);
			$exCityDao = new ExCitiesDAO($dbh);
			$countryResults = $countryDao->getCountriesByCountryId($project->project_country);
			$country = $countryResults[0];
			
			$regionResults = $regionDao->getRegionsByAttributeMap(array("CountryID" => $project->project_country, "RegionID" => $project->project_state));
			$region = $regionResults[0];
			
			$cityResults = $exCityDao->getCity($project->project_country, $project->project_state, trim(ucfirst($project->project_city)));
		
			if($cityResults != null) {
				$city = $cityResults[0];
				$cityLat = $city->Latitude;
				$cityLong = $city->Longitude;
			}
			
			
			echo "<p><b>Country:</b> <img src=\"imgs/flag_icons/png/" . strtolower($country->ISO2) . ".png\" alt=\"" . $country->Country ."\" /> " .  $country->Country . "</p>";
			echo "<p><b>Job State/Region:</b> " . $region->Region . "</p>";
			echo "<p><b>Job City: </b> " . ($cityResults == null ? $project->project_city : $city->City) . "</p>";
			echo "<p><b>Job Address: </b> " . $project->project_address . "</p>";
		}
		
		echo "<p><b>Budget:</b>" . $currencyType . $project->budget_lower_bound . "-" . $currencyType . $project->budget_upper_bound . "</p>\n";
		echo "<p><b>Date:</b> Begins on " . $project->job_start_date . " and ends on " . $project->job_projected_end_date . "</p>\n";
		
		echo "<p><b>Description:</b>" . $project->description . "</p>\n";

		if($project->status == "BID") {
			echo "<p><b>Status:</b> <em class=\"open\">Open for Bidding</em></p>"  ;
			echo "<p><b>End Time: </b><em class=\"open\">" . $interval->d . " days, " . $interval->h . " hours</em></p>";
		}
		else if($project->status == "BID_CLS") {
			echo "<p><b>Status: </b> No more bidding. Job owner must choose a bidder.</p>";
		}
		else if($project->status == "PRE_HIRE") {
			echo "<p><b>Status:</b> A Bidder has been extended an offer, but they still must accept it.</p>";
		}
		else if($project->status == "IN_PROG") {
			echo "<p><b>Status</b>: Job is In Progress</p>";
		}
		else {
			echo "<p><b>Status:</b><em  class=\"close\"> Closed From Bidding.</em></p>";
		}
		
		
		
		echo "</div>"; //close job description
		
		
		/** Print Job Owner Information */
		$exUserDao = new ExUserDAO($dbh);
		$results = $exUsrProjDao->getUser_projectByAttributeMap(array("pid" => $project->pid));
		$usrProj = $results[0];
		$user = $exUserDao->getUserByUid($usrProj->uid);
		
		if($user->avitar == null) {
			$user->avitar = "../man-silhouette.jpg";
		}
		
		$scaleWidth = 300;
		$scaleHeight = HtmlUtil::thumbnailHeightFromWidth("./imgs/photos/" . $user->avitar, $scaleWidth);
		
		echo "<div id=\"jobOwnerInfo\" style=\"float: right; width: 40%; margin-top: 100px;\">";
		
		
		
		echo "<img src=\"imgs/photos/" . $user->avitar . "\" width=\"" . $scaleWidth . "\" height=\"" . $scaleHeight . "\" />"; 
		echo "<p><b>Job Owner:</b> " . $user->name . "</p>";
		
		$countryDao  = new CountriesDAO($dbh);
		$countryResults = $countryDao->getCountriesByAttributeMap(array("CountryID" => $user->country));
		$country = $countryResults[0];
		
		
		echo "<p><b>Affiliated Country:</b> <img src=\"imgs/flag_icons/png/" . $country->ISO2 . ".png\" /> " . $country->Country . "</p>";  
		echo "</div>\n"; // close jobOwnerInfo
		
	
		if($project->remote == "N") {
			echo "<div style=\"float: right; height:350px; width:40%;\" id=\"google_maps_canvas\" onload=\"loadMap()\">";
			
			echo "</div>";
		}
		
		echo "</div>\n";  //close the div id="jobInfoPane"
	}
		
?>
	
	
	<div id="bidForm" style=" display: none; width: 60%; margin-left: auto; margin-right: auto;  margin-top: 100px; ">
	<form  action="RegisterBid.php"  method="POST">
		<input type="hidden" id="charge" name="charge" value="0" />
		<div id="left">
			<p>Amount Paid to you: <input type="number" name="amount" id="amount" onchange="addServiceCharge(this.value)" size="6" /> 
			Charge:<input type="number" readOnly="true" id="chargeDisp" value="0" /></p>
			<p>Milestone Request: <input type="number" size="3" name="milestone" id="milestone" value="75">% </p>
			<p>Start Date: <input type="date" name="startDate" id="startDate"  />
			 End Date: <input type="date" name="end_date" id="end_date"  /></p>
			<p>Proposal: </p>
			<p><textarea rows="10" cols="80" name="message" id="message"></textarea></p>
			<p><input type="button" value="Place Bid" onclick="invokeRegisterBid(invkRegBidCb)" /></p>
		</div>
		<input type="hidden" id="pid" name="pid" value="<?PHP if(isset($_GET["jid"])) echo $_GET["jid"];  ?>" />
	
	</form>
	
	</div>
	

		<div id="awardWinner" style="float: right; width: 100%;margin-left: auto; margin-right: auto; margin-top:100px; ">
			<?PHP
				
				$dbh = DBOConnection::getConnection();
				$upaDao = new ExUserProjectAwardDAO($dbh);
				
				if(!empty($_GET["jid"])) {
					$upaObj = $upaDao->userHiredForJob($_GET["jid"]);
					
					if($upaObj != null) {
						$userDao = new UserDAO($dbh);
						$usrInfo = $userDao->getUserByUid($upaObj->uid);
	
						if(count($usrInfo) <= 0) {
							die("<h1>This Project has awarded to a user who no longer exists in the database.</h1>");
						}
	
						$user = $usrInfo[0];
						echo "<div style=\"width: 30%;  margin-left: auto; margin-right:auto;\">"; 
						echo "<h3 style=\"color:green;\"> " . $user->name . " has been selected by the project owner to complete this job!</h3>";
						
						if($user->avitar == null) {
							$user->avitar = "../man-silhouette.jpg";
						}
						
						$scaleWidth = 200;
						$scaleHeight = HtmlUtil::thumbnailHeightFromWidth("./imgs/photos/" . $user->avitar, $scaleWidth);
						
						echo "<img src=\"imgs/photos/" . $user->avitar . "\" width=\"$scaleWidth\" height=\"$scaleHeight\" />";
						echo "<p><b>Name: </b>" . $user->name . "</p>";
						
						$results = $countryDao->getCountriesByAttributeMap(array("CountryID" => $user->country));
						
						$country = null;
						
						if($results != null) {
							$country = $results[0];
						}
						
						echo "<p><b>Affiliated Country: <img src=\"./imgs/flag_icons/png/" . $country->ISO2 . ".png\" /> " . $country->Country . "</p>";
						/** TODO: Probably add user ratings. */
						 
						echo "</div>";
					}
					
					
				}

			?>
		</div>
		
		<?PHP		
		
		if(isset($_SESSION["uid"])) {
			$uid = $_SESSION["uid"];
			$dbh = DBOConnection::getConnection();
			$usrPrjDao = new User_projectDAO($dbh);
			$bidDao = new BidDAO($dbh);
			
			$attrMap = array();
			$attrMap["uid"] = $uid;
			$attrMap["pid"] = $_GET["jid"];
			
			$usrProjOwnerTups = $usrPrjDao->getUser_projectByAttributeMap($attrMap);
			$usrBids = $bidDao->getBidByAttributeMap($attrMap);
			
			if($project->status == "BID") {
				if(count($usrProjOwnerTups) <= 0 && count($usrBids) <= 0 && $upaObj == null) {
					echo "<form action=\".\">";
					echo "<input type=\"button\" value=\"Place Bid\" id=\"bidButton\" onclick=\"showBidForm()\" />";
					echo "</form>";
				}
			}
		}
		?>
		
		<div id="bidContainer" style="float: left; display: block; width: 100%; height: 100%; margin-top: 100px;" >
		
		</div>
	

</div>

</div>

<script type="text/javascript">

	function canvasMapInit() {
			
			var geoPoint = <?php
			$exCityDao = new ExCitiesDAO($dbh);
			$cityResults = $exCityDao->getCity($project->project_country, $project->project_state, trim(ucfirst($project->project_city)));
			 if($cityResults != null) { 
				echo "new google.maps.LatLng(" . $cityLat . ", " . $cityLong . ");\n"; 
			} 
			else {
				echo "null;\n";
		    }
		     ?>
			var mapOptions = { zoom : 10, center: geoPoint };
			var canvas = document.getElementById("google_maps_canvas");
			
			var map = new google.maps.Map(canvas, mapOptions);

			var marker = new google.maps.Marker( {position: geoPoint, map: map, title: "Job City Location Marker"} );
	}

	function loadMap() {
		google.maps.event.addDomListener(window, 'load', canvasMapInit);
	}

	function deleteJbCb(data) {
		alert(data);
	}
	
	function invokeDeleteJob(cb) {
		
		var vjid = document.getElementById("pid").value;
		var base = "service/gen-php/"; 
		var d = confirm("Are you sure that you want to delete this job?");

		if(!d) {
			return;
		}
		
		$.post(base + "DeleteJob.php",{jid : vjid}).done(function(data) { cb(data); }).fail(function () {});
	}
		
	function showBidForm() {
		document.getElementById("bidForm").style.display="block";
		document.getElementById("bidButton").style.display="none";
	}
	
	function addServiceCharge(amount) {
		total = parseFloat(amount);
		total += 0.10*amount;
		total = total.toFixed(2);
		
		document.getElementById("charge").value = total;
		document.getElementById("chargeDisp").value = total;
		
		return total;
	}
	
	function addBid() {
		invokeRegisterBidService();
		invokeGetBidsService() ;
	}

	function invkRegBidCb(data) {
		document.getElementById("bidForm").style.display="none";
		invokeGetBid(invkGetBidCb);
	}
	
	function invokeRegisterBid(cb) {
		var vmilestone = document.getElementById("milestone").value;
		var vmessage = document.getElementById("message").value;
		var vamount = document.getElementById("amount").value;
		var vstartDate = document.getElementById("startDate").value;
		var vcharge = document.getElementById("charge").value;
		var vendDate = document.getElementById("end_date").value;
		var vpid = document.getElementById("pid").value;
		var base= "./service/gen-php/";

		
		$.post(base + "RegisterBid.php",{milestone : vmilestone,message : vmessage,amount : vamount,startDate : vstartDate,charge : vcharge,endDate : vendDate,pid : vpid}).done(function(data) { invkRegBidCb(data); }).fail(function () {});
	}
	

	function invkGetBidCb(data) {
	
		var objs = $.parseJSON(data);
		var uid = "<?php if(isset($_SESSION["uid"])) { echo $_SESSION["uid"]; } ?>";
		var code = "<div style=\"float: bottom;\" id=\"bids\">\n";
		code += "<table id=\"bidTable\">\n";

		code += "<tr>";
		<?php 
		if(isset($_SESSION["uid"])) {
			if($exUsrProjDao->userOwnsProject($_SESSION["uid"], $jid)) {
				echo "code += \"<th>Hire</th>\";\n";
			}
		}
		?>
		code += "<th><p>Bidder</p></th><th><p>Bid Date</p></th><th><p>Start Date</p></th><th><p>End Date</p></th><th><p>Bid Amount</p></th></tr>\n";
		
		for(var i = 0; i < objs.length; i++) {
			var obj = objs[i];
			var usrObj = obj.user;
			var bidObj = obj.bid;
			var countryObj = obj.country;
			
			if(uid == usrObj.uid) {
				code += "<tr style=\"color:green\">";
			}
			else {
				code += "<tr>";
			}

			<?php 
			if(isset($_SESSION["uid"])) {
				if($exUsrProjDao->userOwnsProject($_SESSION["uid"], $jid)) {
					echo "code += \"<td><a href=\\\"javascript:hire(\" + usrObj.uid + \")\\\">Hire</a></td>\";\n";
				}
			}
			?>
			
			code += "<td><p><img src=\"./imgs/flag_icons/png/" + countryObj.ISO2  +".png\" /> " + usrObj.name + "</p></td><td>" + bidObj.add_ts + "</td><td>" + bidObj.start_date + "</td><td>" + bidObj.end_date + "</td><td>" + "<?php echo $currencyType; ?>" + bidObj.charge + "</td></tr>\n";
		}

		code += "</table>\n";
		code += "</div>";

		if(objs.length > 0) {
			document.getElementById("bidContainer").innerHTML = code;
		}
	}


	
	function invokeGetBid(cb) {
		var vjid = document.getElementById("pid").value;
		var base = "./service/gen-php/";
	
		$.post(base + "GetBids.php",{pid : vjid}).done(function(data) { cb(data); }).fail(function () {});
	}
	
	
	
	function hire(theUid) {
		var theJid = document.getElementById("pid").value;

		$.post("HireUser.php", {uid: theUid, pid: theJid}, function(data) { 
	
			if(data == "1") {
				alert("Error: User already hired for this job");
			}
			else {
				
			}
		} );
	}


	
</script>

<?php echo "<script>google.maps.event.addDomListener(window, 'load', canvasMapInit);</script>"; ?>
			
<style type="text/css">
	p.bidderName { font-weight: bold; font-size: large; }
	#bidTable {margin-left: auto; margin-right:auto; margin-top: 5px;}
	p.bidderAddTs {color: #707070 ; font-size: x-small;}
	p.bidAmount {font-size: xx-large;}
	
	.open {color: #00FF00;}
    .close {color: #FF0000; }	
</style>

</body>
</html>