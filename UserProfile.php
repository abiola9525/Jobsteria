<?PHP
	require_once(dirname(__FILE__) . "\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("ex_dao_root_dir") . "ExUserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CountriesDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "RegionsDAO.php");
	require_once("DBOConnection.php");
	require_once(dirname(__FILE__) . "\\util\\HtmlUtil.php");
	session_start();

	
	if(!isset($_SESSION["loggedIn"])) {
		$_SESSION["loggedIn"] = 0;
	}
	
	
	
	if(intval($_SESSION["loggedIn"]) != 1 && (empty($_POST["uname"]) || empty($_POST["pass"]))) {
		header("Location: http://" . FreeConfiguration::getInstance()->getProperty("base_url") ."index.php");
	}
	
	if(!$_SESSION["loggedIn"]) {
		$username = $_POST["uname"];
		$password = $_POST["pass"];
		
		$dbh = DBOConnection::getConnection();
		$exUserDao = new ExUserDAO($dbh);

		if($exUserDao->canLogin($username, $password)) {
			$_SESSION["uid"] = $exUserDao->getUidByUsername($username);
			$_SESSION["uname"] = $username;
			$_SESSION["pass"] = $password;
			$_SESSION["loggedIn"] = 1;
		}
	}
	
	
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
@import url("http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css")
	;
</style>
</head>
<body onload="loadTabs()">
	<script src="http://code.jquery.com/jquery-latest.min.js"
		type="text/javascript"></script>
	<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
	<script src="lib/lib.js"></script>
	<div id="header">
		<div id="links">
			<a href="index.php">Home</a> <a href="JobBrowser.php">Browse Jobs</a>
			<a href="PostJob.php">Post Job</a>
		</div>
	
	<?PHP
	if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"]==1) {
		$dbh = DBOConnection::getConnection();
	$userDao = new ExUserDAO($dbh);

	$user = $userDao->getUserByUid($_SESSION["uid"]);
		
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

	if($_SESSION["loggedIn"] == 1) {
		$uid = $_SESSION["uid"];
		$dbh = DBOConnection::getConnection();
		
		$userDao = new ExUserDAO($dbh);
		$countryDao = new CountriesDAO($dbh);
		$regionDao = new RegionsDAO($dbh);
		
		$user = $userDao->getUserByUid($uid);
		
		if($user->avitar == null) {
			$user->avitar = "../man-silhouette.jpg";
		}
		
		$scaleWidth = 300;
		$scaleHeight = HtmlUtil::thumbnailHeightFromWidth("./imgs/photos/" .$user->avitar, $scaleWidth);
		
		
		echo "<div id=\"profile\">\n";
		echo "<div style=\"width: 80%; margin-left: auto; margin-right: auto;\" id=\"info\">\n";
		echo "<div id=\"photo\" style=\"float: right;\">\n";
		echo "<img src=\" ./imgs/photos/" . $user->avitar . "\" height=\"$scaleHeight\" width=\"$scaleWidth\" />\n";
		echo "</div>\n";
		echo "<div  id=\"info\" style=\"height: 300px;\">\n";
		echo "<p><b>Name: </b> " . $user->name . " </p>\n";
		
		$results = $countryDao->getCountriesByCountryId($user->country);
		
		if($results == null) {
			$country = "Unknown";
		}
		else {
			$country = $results[0]->Country;
		}
		
		echo "<p><b>Country: </b>" . $country . "</p>\n";
		
		$results = $regionDao->getRegionsByRegionID($user->state_prov);
		
		if($results == null) {
			$region = "Unknown";
		}
		else {
			$region = $results[0]->Region;
		}
		
		echo "<p><b>Location: </b> " . $user->city . "," . $region . "</p>";
		echo "<p><b>Join Date: </b> " . $user->add_ts . "</p>";
		echo "</div>\n";
		echo "</div>\n";
	
		echo "</div>\n";
		
		//echo "</div>\n";
	}
	else echo "<h1>Login Failed!</h1>";

?>
	<div id="userProfileOptionMenu">
		<script type="text/javascript">
		$(function () {
				$("#user_menu").menu();
			} 
		);

		function showPerspective(v) {
			//alert("I am Invoked for " + v);
			var pWorker = document.getElementById("workerTabPane");
			var pEmployer = document.getElementById("recentInfoEmployerPane");
			var pPm = document.getElementById("pmPane");
			var pFinance = document.getElementById("financesPane");
			var pIPane = document.getElementById("profileInfoPane");
			
			var arr = [pWorker, pEmployer, pPm, pFinance, pIPane];

			for(var i = 0; i < arr.length; i++) {
				if(i == v) {
					arr[i].style.display="block";
				}
				else {
					arr[i].style.display="none";
				}
			}
			
					
		}
		</script>
		<ul id="user_menu" style="width:250px;">
			<li><a href="javascript:showPerspective(0)">Worker Perspective</a></li>
			<li><a href="javascript:showPerspective(1)">Employer Perspective</a></li>
			<li><a href="javascript:showPerspective(2)">Private Messages</a></li>
			<li><a href="javascript:showPerspective(3)">Finances</a></li>
			<li><a href="javascript:showPerspective(4)">Personal Information</a></li>
		</ul>
	</div>

	
	
	<div id="workerTabPane" class="hide">
		<h3>Recent Information for Workers</h3>

	<!-- TODO: Add some functionality here so that the tab links show up as Green and have the word "New"
	before the link text to alert the user that they have new items.
	-->
		<div class="tabPane" id="workerTabs">
			<ul>
				<li><a href="#recentWorkHiredTab" onclick="invokeGetHiredService()">Currently Working Jobs</a></li>
				<li><a href="#recentBidsTab" onclick="invokeGetRecentBidsService()">Recent Bids</a></li>
				<li><a href="#recentWorkFinished" onclick="invokeGetRecentWorkFinished()">Recent Work Finished</a></li>

			</ul>
			<div id="recentWorkHiredTab">
				<div id="recentWorkHiredDescription">
				<p>The information in this tab pane are recent jobs you have been hired to work for that you placed bids on.</p>
				</div>
				<div id="recentWorkHiredTabView"></div>
			</div>

			<div id="recentBidsTab">
				<div id="recentBidsTabView"></div>
			</div>
			
			<div id="recentWorkFinished">
				<p>Information in this tab lists recent work that you have completed.</p>
				
				<div id="recentWorkFinishedView"></div>
			</div>
			<script type="text/javascript">

			function invokeGetHiredService() {
				$.post("GetHired.php", {}).done(function(data) {
				
					var code = "<table border=\"1\">";
					code += "<tr><th>Acknowledge</th><th>Job Name</th><th>Bid Amount</th><th>Milestone Request</th><th>Job Complete Request</th>";
					code += "</tr>";
					var objs = $.parseJSON(data);

					for(var i = 0; i < objs.length; i++) {
						var obj = objs[i];
						var hireObj = obj.hire;
						var projectObj = obj.project;
						var bidObj = obj.bid;

						if(hireObj.accepted == "N") {
							code += "<tr>";
							code += "<td id=\"acceptOffer" + projectObj.pid +"\"><a style=\"color: blue;\" href=\"javascript:acceptOfferInvoke(" + projectObj.pid  + ")\">Accept Offer</a></td>";	
						}
						else {
							code += "<tr>";
							code += "<td style=\"color: green;\">Offer Accepted!</a></td>";	
						}

						code += "<td><a style=\"color: blue; \" href=\"JobView.php?jid=" + projectObj.pid + "\">" + projectObj.name + "</a></td><td>" + getCurrency(projectObj.currency_type)  + bidObj.charge + "</td>";

						if(hireObj.milestone_request_accepted == "Y") {
							code += "<td><em style=\"color: green;\">Milestone Request Accepted by Employer</em></td>";
						}
						else if(hireObj.milestone_request == "Y") {
							code += "<td>Milestone Request has been made, but needs to be approved by employer</td>";
						}
						else {
							code += "<td id=\"msRequest" + projectObj.pid +"\"><a style=\"color:blue\" href=\"javascript:milestoneRequest(" + projectObj.pid +")\">Create Milestone Request</a></td>";
						}

						if(projectObj.status == "FIN_CLS") {
							code += "<td>Job has been marked as finished and closed by Employer.</td>";
						}
						else if(projectObj.status == "FIN") {
							 code += "<td>Job has been marked as finished by Employer.</td>";
						}
						else if(projectObj.status == "IN_PROG" && hireObj.project_complete_request == "Y") {
							code += "<td><em style=\"color: green;\"> Job Completion Request has been sent to employer, but they must still approve.</em></td>"; 
						}
						else {
							code += "<td id=\"jcReqCol" + projectObj.pid +"\"><a href=\"javascript:requestJobComplete(" + projectObj.pid + ")\" style=\"color:blue;\">Request Job Completion</a></td>";
						}
						
						code += "</tr>\n";
					}

					code += "</table>";
					document.getElementById("recentWorkHiredTabView").innerHTML = code;
				});
			}

			function requestJobComplete(v) {
				$.post("JobCompleteRequest.php", {pid: v}).done(function(data) { 
					if(data.length == 0) {
						document.getElementById("jcReqCol" + v).innerHTML = "<em style=\"color:green;\">Job Completion Request has been sent to employer, but they must still approve.</em>"; 
					}
					else {
						document.getElementById("jcReqCol" + v).innerHTML = "<em style='color:red;'>" + data + "</em>";
					}
				});
			}
					
			function invokeGetRecentBidsService() {
				$.post("GetRecentBids.php", {}, function(data) { 
					document.getElementById("recentBidsTabView").innerHTML = data;
				} ) . fail(function () {alert("ERROR"); } ) ;
			}

			invokeGetHiredService();
			invokeGetRecentBidsService();
			
			</script>
		</div>
	</div>


	<div id="recentInfoEmployerPane" class="hide">
		<h3>Recent Information for Employers</h3>
		
		<div class="tabPane" id="employerTabs">
			<ul>
				<li><a href="#jobsTab" onclick="invokeGetUserProjectsService()">Jobs Owned</a></li>
				<li><a href="#employerHireTab" >Recently Hired</a></li>
				<li><a href="#msRequestTab" onclick="invokeGetMsRequestService()">Milestone Request</a></li>
				<li><a href="#jobCompletionRequestTab" onclick="invokeGetJobCompletionRequestService()">Job Completion Requests</a></li>
			</ul>
			<div id="employerHireTab">
				
			</div>
			<div id="jobsTab">
				<div id="jobsTabView"></div>
			</div>
			
			<div id="msRequestTab">
				<div id="msDescription">
					<p>
					You will receive a Milestone request when the person whom you've employed has finished a certain percentage of their job.  The
					employed person must first make the request in order for you to accept the offer.  Only accept the milestone request if it is correct, that is, only if the
					job is the percentage complete that the employee is explaining to be completed.  Accepting the request will pay your employed person for the work percentage of work
					they have completed thus far.
					</p>
				</div>
				<div id="msRequestView">
				</div>
			</div>
			<div id="jobCompletionRequestTab">
				<div id="jcrtDescription">
					<p>You will receive a Job Completion request when the person whom you've employed has finished the job.  The employed person
					must first make the request in order for you to accept the offer.  Further more, a milestone request must be completed and accepted by the employer in order
					to submit a job complete request.  You should only accept the job completion request if the job has been completed by the hired employee.  Once you accept the
					offer, the employee will be paid the remaining percentage left over from what has not been paid by the milestone request.</p>
				</div>
				<div id="jcrtView">
				</div>
			</div>
			<script type="text/javascript">

			function acceptJobCompleteRequest(v) {
				$.post("./AcceptJobComplete.php", {pid: v}).done(function (data) {
		
				});
			}
			
			function invokeGetJobCompletionRequestService() {
				$.post("./GetJobCompletionRequests.php", {}).done(function (data) {
					var obj;
					
					try {
						obj = $.parseJSON(data);
					}
					catch(e) {
						alert("Error: invalid json" + e);
						return;
					}

					var code=  "<table border=\"1\">";
					code += "<tr><th>Job Name</th><th>Request Date</th><th>Accept Job Completion</th></tr>";
					
					for(var  i= 0; i < obj.length; i++) {
						var jobObj = obj[i].job;
						var requestObj = obj[i].request;

						code += "<tr><td>" + jobObj.name + "</td><td>" + requestObj.add_ts + "</td>";

						code += "<td id=\"jcrt" + jobObj.pid + "\">";
						if(jobObj.status == "FIN") {
							code += "<em style='color: green'>Accepted Job Completion Request.  The project is now finished.</em></td>";
						}
						else {
							code += "<a href=\"javascript:acceptJobCompleteRequest(" + jobObj.pid +")\">Accept Job Complete Request</a></td></tr>";
						}
					}

					code += "</table>";
					document.getElementById("jcrtView").innerHTML = code;
				});
			}
			
			function invokeGetUserProjectsService() {
				$.post("GetUserProjects.php", {}, function(data) { 
					document.getElementById("jobsTabView").innerHTML = data;
				} ) . fail(function () {alert("ERROR"); } ) ;
			}
			
			function acceptMilestoneRequest(v) {
				$.post("AcceptMilestoneRequest.php", {pid: v}).done(function(data) { 
				
						document.getElementById("col" + v).innerHTML = "<em style=\"color: green\">Milestone Request Accepted</em>";
					
				});
			}
			
			function invokeGetMsRequestService() {
				$.post("GetMilestoneRequest.php", {}).done(function(data) {
					var obj = $.parseJSON(data);

					if(obj.length == 0) {
						document.getElementById("msRequestView").innerHTML = "<h3>No Milestone Requests at this Time</h3>";
						return;
					}
					
					var code = "<table border=\"1\">\n";
					code += "<tr><th>Job Name</th><th>Milestone Request</th><th>Request Date</th><th>Accept Request</th></tr>";
					
					for(var i = 0; i < obj.length ;i++) {
						var project = obj[i].job;
						var request = obj[i].request;
						var milestone = obj[i].milestone;
						
						code += "<tr><td><a href=\"./JobView.php?jid=" + project.pid + "\">" + project.name + "</a></td><td>Worker claims job is " + milestone + "% complete.  Please verify this is correct before accepting.</td>";
						code += "<td>" + request.update_ts + "</td><td id=\"col" + project.pid + "\">";

						if(request.milestone_request_accepted == "Y") {
							code += "<em style=\"color: green;\">Milestone Request has been accepted</em>";
						}
						else { 
							code +="<a href=\"javascript:acceptMilestoneRequest(" + project.pid + ")\">Accept Request</a></td></tr>";
						}
					}

					code += "</table>";
					document.getElementById("msRequestView").innerHTML = code;
				});
				
			}
			
			invokeGetMsRequestService();
			invokeGetUserProjectsService();
			
			</script>
		
		</div>
	</div>

	<div id="financesPane" class="hide">
		<h3>Finances</h3>
		<div class="tabPane" id="financeTabs">
			<ul>
				<li><a href="#recentTransTab">Recent Transactions</a></li>
			</ul>
			<div id="recentTransTab">
				<div id="recentFinanceInfo"><p>This shows your recent transactions.  Payments to employee's and service charges are highlighted in red, and received payments into
				your bank account associated with your default credit card are highlighted in green.</p></div>
				<div id="recentFinanceView"></div>
			</div>
			<script type="text/javascript">
			function getFinancesInfo() {
				$.post("GetFinanceInfo.php", {}).done(function(data) { 
					
					var code = "<table border=\"1\">";
					code += "<tr><th>Transaction ID</th><th>Type</th><th style=\"width: 20%;\">Job</th><th>Payment To</th><th>Payment From</th><th>Time</th><th>Amount</th>";
					var uid = "<?php if(isset($_SESSION["uid"])) { echo $_SESSION["uid"]; } ?>";
					var obj = null;
					try {
						obj = $.parseJSON(data);
					}
					catch(e) {
						document.getElementById("recentFinanceView").innerHTML = "<p>Could not retrieve finances due to internal server error.</p>";
						return;
					}

					if(obj.length == 0) {
						document.getElementById("recentFinanceView").innerHTML = "<h3>No Finances at this time</h3>";
						return;
					}
					
					for(var i = 0; i < obj.length; i++) {
						var transactionObj = obj[i].transaction;
						var toUsrObj = obj[i].to_user;
						var fromUsrObj = obj[i].from_user;
						var projectObj = obj[i].project;

						code += "<tr><td>" + transactionObj.tid + "</td><td>" + getTransactionType(transactionObj.type) + "</td><td>" + projectObj.name + "</td>";
			
						
						var paymentTo = "";

						if(transactionObj.type == "SERVICE_CHARGE") {
							paymentTo = "Jobsteria!";
						}
						else {
							paymentTo = toUsrObj.name;
						}

						var paymentFrom = fromUsrObj.name;

						code += "<td>" + paymentTo + "</td>";
						code += "<td>" + paymentFrom + "</td>";

						code += "<td>" + transactionObj.add_ts + "</td><td>";
						
						if(uid == fromUsrObj.uid) {
							code += "<em style=\"color: red;\">-";
						} 
						else {
							code += "<em style=\"color: green;\">";
						}

						code += getCurrency(projectObj.currency_type) + transactionObj.amount + "</em></td></tr>";
					}

					code += "</table>";
					
					document.getElementById("recentFinanceView").innerHTML = code; 
				}
				);
			}
			
			getFinancesInfo();
			</script>
			
		</div>
	</div>

	<div id="pmPane" class="show">
		<h3>Private Messages</h3>
		
		<div class="tabPane" id="messageTabs">
			<ul>
				<li><a href="#messageBinTab">Message Bin</a></li>
				<li><a href="#sendMessageTab">Send Message</a></li>
				
			</ul>
		
			<div id="messageBinTab">
				<div id="messageDisp">
				</div>
				<div id="readMessageDisp" style="display: none;">
					<p>From:<input type="text" id="from" disabled size="80" /></p>
					<p>Subject:<input type="text" id="subj" disabled  size="80" /></p>
					<p>Message:</p>
					<textarea id="msgContent" cols="80" rows="20" ></textarea>
				</div>
				<script type="text/javascript">

					function viewMessage(fromUser, subject, index) {
						document.getElementById("from").value = fromUser;
						document.getElementById("subj").value = subject;
						document.getElementById("msgContent").innerHTML = document.getElementById("msgVal" + index).value;
						document.getElementById("readMessageDisp").style.display="block";
					}
					
					function invokeGetPM() {
						$.post("./GetPM.php", {}).done(function(data) { 
							var obj = $.parseJSON(data);

							if(obj == null || obj.length == 0) {
								document.getElementById("messageDisp").innerHTML = "<h3>No Messages at this time</h3>";
								return;
							}
							
							
							var code = "<table border=\"1\">";
							code += "<form>";
							code += "<tr><th>Select</th><th>From</th><th>Subject</th><th>Sent</th></tr>";
							for(var i = 0; i < obj.length; i++) {
								var msg = obj[i].pm;
								var usrObj = obj[i].from_user;
								
								code += "<tr><td><input type=\"checkbox\" name=\"msg" + i + "\" id=\"msg" + i + "\" />";
								code += "<input type=\"hidden\" id=\"msgVal" + i + "\" value=\"" + msg.message + "\" /></td>";
								code += "<td>" + usrObj.username + "</td>";
								code += "<td><a href=\"javascript:viewMessage('" + usrObj.username + "','" + msg.subject +"'," + i + ")\">" + msg.subject + "</a></td>";
								code += "<td>" + msg.add_ts + "</td></tr>";
							
							}
							code += "</form>";
							code += "</table>";
							document.getElementById("messageDisp").innerHTML = code;
						});
					}

					invokeGetPM();
				</script>
				
			</div>
			
			<div id="sendMessageTab">
			
				<form>
					<p>To:<input type="text" name="username" id="username" value="" maxlength="80" size="80" /></p>
					<p>Subject:<input type="text" name="subject" id="subject" maxlength="80" size="80" /></p>
					<p>Message:</p>
					<textarea name="message" id="message" cols="80" rows="20"></textarea>
					
					<br />
				<input style="float: left;" type="button" value="Send" onclick="invokeSendMessage()" />
				</form>
				
			</div>




		</div>
	</div>

	<div id="profileInfoPane" class="hide">
		<h3>Profile Information</h3>
		<div class="tabPane" id="profileTabs">
			<ul>
				<li><a href="#updateUserProfileTab">Profile Information</a></li>
				<li><a href="#creditCardInfo">Credit Card Information</a></li>
			</ul>
			<div id="creditCardInfo">
				<div id="registeredCreditCards">
				</div>
				<a href="javascript:showCreditCardForm(0)" style="color: blue;">Add</a>
				<form id="creditCardForm" style="display: none;">
					<fieldset style="width: 50%">
						<legend>Credit Card Information</legend>
						<a href="javascript:removeCreditCard()" id="removeCard">Remove</a>
						
						<p>Card Number:
							<input type="text" name="cardNumber" id="cardNumber" />
						</p>
						<p>Card Name:
							<input type="text" name="cardName" id="cardName" />
						</p>
						<p>Expires On:
							<select name="expireMonth" id="expireMonth">
								<option value="">SELECT ONE</option>
								<option value="1">January</option>
								<option value="2">February</option>
								<option value="3">March</option>
								<option value="4">April</option>
								<option value="5">May</option>
								<option value="6">June</option>
								<option value="7">July</option>
								<option value="8">August</option>
								<option value="9">September</option>
								<option value="10">October</option>
								<option value="11">November</option>
								<option value="12">December</option>
							</select>
						
						
					
							<select name="expireYear" id="expireYear">
							<option value="">SELECT ONE</option>
					<?php 	
						$yearRange = 20;
                    	$thisYear = date('Y');
                	    $startYear = ($thisYear + $yearRange);
                 
                    	foreach (range($thisYear, $startYear) as $year) 
                    	{
                       	     print '<option value="'.$year.'">' . $year . '</option>';
                    	}
					?>
						
						
							</select>
						</p>
						
						<p>CVC: <input type="text" name="cvc" id="cvc" /></p>
						<input type="hidden" name="card_id" id="card_id" />
						<input type="button" id="addCard" value="Add Card" onclick="javascript:registerCreditCard('add')" />
						<input type="button" id="updateCard" value="Update Card" onclick="javascript:registerCreditCard('update')" />
					</fieldset>				
				</form>
			</div>
			
			<div id="updateUserProfileTab">
				<div id="updateUserContainer">
					<form method="POST" action="RegisterUser.php">


						<fieldset style="width: 50%">
							<legend>Basic Account Information</legend>
							Name:<input type="text" name="name" id="name" /><br /> Username:<input
								type="text" name="username" id="username" /><br /> Password:<input
								type="password" name="password" id="password" /><br />
						</fieldset>

						<fieldset style="width: 50%">
							<legend>Location Information</legend>
							Country:<select name="country" id="country">
								<option value="">SELECT ONE</option>
								<option value="America">USA</option>
								<option value="India">India</option>
								<option value="Europe">Europe</option>
							</select> State/Providence:<select name="state_prov"
								id="state_prov">
								<option value="">SELECT ONE</option>
								<option value="AZ">Arizona</option>
								<option value="MD">Maryland</option>
								<option value="VA">Virginia</option>
							</select> <br /> City:<input type="text" name="city" id="city" /><br />
							Address:<input type="text" name="address" id="address" /><br />
							Zipcode:<input type="text" name="zipcode" id="zipcode" /><br />
						</fieldset>

						<fieldset style="width: 50%">
							<legend>Contact Information</legend>
							E-mail:<input type="text" name="email" id="email" /><br /> Phone:<input
								type="text" name="phone" id="phone" /><br /> Birth Date:<input
								type="text" name="birth_date" id="birth_date" /><br />
						</fieldset>




						<fieldset style="width: 50%">
							<legend>Extra Information</legend>
							Resume:<input type="file" name="resume_file_loc"
								id="resume_file_loc" /><br /> Avitar:<input type="file"
								name="avitar" id="avitar" />
						</fieldset>

						<input type="button" onclick="invokeUpdateUser(invkUpdtUsrCb)" value="Update" />
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>

	<style>
#view {
	width: 60%;
	margin-left: auto;
	margin-right: auto;
}

.tabPane {
	font-size: 12px;
}

 table {width: 70%;}
 
 .hide { display: none; }
 
 .show { display: block; }
 
</style>

	<script src="https://js.stripe.com/v1/"></script>
	
	<script type="text/javascript">

	function showCreditCardForm(x) {
		document.getElementById("creditCardForm").style.display="block";
		if(x == 0) {
			document.getElementById("addCard").style.display="block";
			document.getElementById("updateCard").style.display="none";
			document.getElementById("removeCard").style.display="none";
			document.getElementById("card_id").value="";
		}
		else {
			document.getElementById("addCard").style.display="none";
			document.getElementById("updateCard").style.display="block";
			document.getElementById("removeCard").style.display="block";
		}
	}
	
	function populateCreditCardForm(id) {
		$.get("GetCreditCard.php", {card_id: id}).done(function(data) {
			var o = $.parseJSON(data);

			if(o.brand == "Visa" || o.brand == "MasterCard") {
				document.getElementById("cardNumber").value = "XXXX-XXXX-XXXX-" + o.last4;
			}
			else if(o.brand == "") {
			}
			
			document.getElementById("expireMonth").value = o.exp_month;
			document.getElementById("expireYear").value = o.exp_year;
			document.getElementById("cardName").value = o.name;
			document.getElementById("card_id").value = id;
			showCreditCardForm(1);
		});
	}
	
	function getCreditCards() {
		$.post("GetCreditCard.php", {}).done(function(data){
			var o = $.parseJSON(data);

			var length = o.total_count;
			var i;
			var genHtml = "<table border=\"1\"><tr><th>Credit Card Type</th><th>Credit Card Number</th><th>Expiration</th></tr>";
			for(i=0; i < length ;i++) {
				var node = o.data[i];
				genHtml += "<tr style=\"text-align: center;\"><td>" + node.brand + "</td>";

				if(node.brand == "Visa" || node.brand == "MasterCard") {
					genHtml += "<td><a href=\"javascript:populateCreditCardForm('" + node.id +"')\">XXXX-XXXX-XXXX-" + node.last4 + "</a></td>";
				}
				else if (node.brand == "American Express") {
					genHtml += "<td><a href=\"javascript:populateCreditCardForm('" + node.id + "')\">XXXX-XXXXXX-X" + node.last4 + "</a></td>";
				}
				
				genHtml += "<td>" + node.exp_month + "/" + node.exp_year + "</td>";
				genHtml += "</tr>";
			}

			genHtml += "</table>";

			document.getElementById("registeredCreditCards").innerHTML = genHtml;
		});
	}

	
	
    function registerCreditCard(action) {

    	Stripe.setPublishableKey('pk_test_GM9nrfcdptEpMQIg9Q1VFtSV');
      
		var cardNumber = document.getElementById("cardNumber").value;
		var expireMonth = document.getElementById("expireMonth").value;
		var expireYear = document.getElementById("expireYear").value;
		var cvc = document.getElementById("cvc").value;
		var card_id = document.getElementById("card_id").value;
		var name = document.getElementById("cardName").value;
		var cardType = Stripe.cardType(cardNumber);
		// Validate Data
		
		if(action == 'add') {
			if(!Stripe.card.validateCardNumber(cardNumber)) {
				alert("You have entered an invalid card number.");
				return;
			}

			if(!Stripe.card.validateExpiry(expireMonth, expireYear)) {
				alert("It seems that your credit card has expired.");
				return;
			}
		
			if(!Stripe.card.validateCVC(cvc)) {
				alert("You have entered an invalid CVC.");
				return;
			}
		
			Stripe.createToken({number: cardNumber, cvc: cvc, exp_month: expireMonth, exp_year: expireYear, name: name}, function(status, response) {
				if(status == 200) {
					$.post("./RegisterCreditCard.php",
							{cardType: cardType,
						     cardNumber: cardNumber,
						     expireMonth: expireMonth,
						     expireYear: expireYear,
						     cvc: cvc,
						     card_id: card_id,
						     cardName: name,
						     stripeToken: response.id
						    }).done(function(data) { 
						    	getCreditCards();
							}).fail(function(){}
							);
				}
				else {
					alert("Error: Status " + status);
				}
			
	   		});
		
		}
		else {
			$.post("./RegisterCreditCard.php",
					{cardType: cardType,
				     cardNumber: cardNumber,
				     expireMonth: expireMonth,
				     expireYear: expireYear,
				     cvc: cvc,
				     card_id: card_id,
				     cardName: name
				    }).done(function(data) { 
					    alert(data);
				    	getCreditCards();
					}).fail(function(){}
					);
		}
    }
	
	
	function invokeSendMessage() {
		var vmessage = document.getElementById("message").value;
		var vto_uid = document.getElementById("username").value;
		var vsubject = document.getElementById("subject").value;
		var base = "./service/gen-php/";
		
		$.post(base + "SendPM.php",{message : vmessage,username: vto_uid,subject : vsubject}).done(function(data) {  }).fail(function () {});
	}
	
	function loadTabs() {
		 $(function() {
			 $( "#workerTabs" ).tabs();
			 $( "#profileTabs" ).tabs();
			 $( "#messageTabs" ).tabs();
			 $( "#employerTabs" ).tabs();
			 $( "#financeTabs" ).tabs();

			 });

		 getCreditCards();
		 
	}

	
	
	
	
	function invokeGetCreditCardService() {
		
	}

	function invkUpdtUsrCb(data) {
	
	}

	function invokeUpdateUser(cb) {
		var vphone = document.getElementById("phone").value;
		var vbirth_date = document.getElementById("birth_date").value;
		var vzipcode = document.getElementById("zipcode").value;
		var vresume_file_loc = document.getElementById("resume_file_loc").value;
		var vpassword = document.getElementById("password").value;
		var vcity = document.getElementById("city").value;
		var vcountry = document.getElementById("country").value;
		var vavitar = document.getElementById("avitar").value;
		var vstate_prov = document.getElementById("state_prov").value;
		var vusername = document.getElementById("username").value;
		var vaddress = document.getElementById("address").value;
		var vemail = document.getElementById("email").value;
		var vname = document.getElementById("name").value;
		var base = "./service/gen-php/";

		$.post(base + "UpdateUser.php",{phone : vphone,birth_date : vbirth_date,zipcode : vzipcode,resume_file_loc : vresume_file_loc,password : vpassword,city : vcity,country : vcountry,avitar : vavitar,state_prov : vstate_prov,username : vusername,address : vaddress,email : vemail,name : vname}).done(function(data) { cb(data); }).fail(function () {});
	}

	function showUpdateUserForm() {
		document.getElementById("updateUserContainer").style.display = "block";
	}

	
	

	function acceptOfferCb(pid, data) {
		var node = document.getElementById("acceptOffer" + pid);
		node.innerHTML="<em style=\"color: green\">Offer Accepted!</em>";
	}
	
	function acceptOfferInvoke(vpid) {
		log("acceptOfferService " + vpid);
		$.post("AcceptOffer.php", {pid: vpid}).done(function(data) { acceptOfferCb(vpid, data); });
	}

	function milestoneRequest(v) {
		$.post("RegisterMilestoneRequest.php", {pid: v}).done(function(data) {
			//alert(data);
			document.getElementById("msRequest" + v).innerHTML = "Created Milestone Request";
		});
	}

	
	
</script>


	</div>

</body>
</html>

