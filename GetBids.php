<?PHP
	require_once 'QueryUtility.php';

	session_start();
	
	if(!isset($_POST["jid"])) {
		die("Invalid Invocation");
	}
	
	$jid = $_POST["jid"];
	
	$bids = QueryUtility::getBidsByJobId($jid);
	$job = QueryUtility::getJobById($jid);
	
	if(!empty($bids)) {
			$len = count($bids);
			
			$uid = null;
			$userOwnsJob = null;
			if(isset($_SESSION["uid"])) {
				$uid = $_SESSION["uid"];
				$userOwnsJob = QueryUtility::userOwnsJob($uid,$jid);
				echo "User Owns Job? " . $userOwnsJob;
			}
			
			echo "<table id=\"bidTable\" border=\"0\">\n";
			echo "<tr><th><p>Bidder Name</p></th><th><p>Bid Date</p></th><th><p>Start Date</p></th><th><p>End Date</p></th><th><p>Bid Amount</p></th></tr>\n";
			for($i = 0; $i < $len; $i++) {
				$currBid = $bids[$i];
				$user = QueryUtility::getUserByUid($currBid->getUid());
				
				if($uid != null && $currBid->getUid() == $uid) {
					echo "<tr style=\"background-color: green;\">";
				}
				else {
					echo "<tr>";
				}
				
				echo "<td><p class=\"bidderName\">" . $user->getName() . "</p></td><td><p class=\"bidderAddTs\">" . $currBid->getAddTs() . "</p></td><td><p>". $currBid->getStartDate() . 
					"</p></td><td><p>" . $currBid->getEndDate() . "</p></td><td><p class=\"bidAmount\">" . $job->getCurrencyType() . $currBid->getAmount() . "</p></td>";
					
				
				if($userOwnsJob) {
					echo "<td><a href=\"javascript:invokeHireUserService(" . $user->getUid() . ")\">Hire</a></td>";
				}
				
				echo "</tr>\n";
			}
			
			echo "</table>\n";
		}
	else {
		echo "<h3>No Bids</h3><br />";
	}
?>