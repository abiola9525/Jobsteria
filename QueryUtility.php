<?PHP
	require_once 'Job.php';
	require_once 'Connection.php';
	require_once 'Bid.php';
	require_once 'User.php';
	require_once 'UserProject.php';
	require_once 'UserProjectAward.php';
	require_once 'PrivateMessage.php';
	require_once './gen-beans/CreditCard.php';
	
	class QueryUtility {

		public static function getJobById($jid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT * FROM `project` WHERE pid=%s", $jid);
			$resultSet = mysql_query($q, $dbh);
			
			$row = mysql_fetch_row($resultSet);
			
			$name = $row[1];
			$description = $row[2];
			$upperBudget = $row[3];
			$lowerBudget = $row[4];
			$currencyType = $row[5];
			$status = $row[6];
			$addTs = $row[7];
			$updateTs = $row[8];
			$bidEndDate = $row[9];
			
			
			$job = new Job($jid, $name, $description, $upperBudget, $lowerBudget, $currencyType, $status, $addTs, $updateTs, $bidEndDate);
			return $job;
		}
		
		/**
		 * Description: Returns an array list of Bid objects.
		 */
		public static function getBidsByJobId($jid) {
		
		
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT * FROM `bid` WHERE pid=%s", $jid);
			
			$resultSet = mysql_query($q);
			$bids = array();
			
			while($row=mysql_fetch_row($resultSet)) {
				$uid = $row[0];
				$pid = $row[1];
				$message = $row[2];
				$startDate = $row[3];
				$endDate = $row[4];
				$addTs = $row[5];
				$updateTs = $row[6];
				$amount = $row[7];
				$charge = $row[8];
				$milestone = $row[9];
				
				
				$bid = new Bid($uid, $pid, $message, $startDate, $endDate, $addTs, $updateTs, $amount, $charge, $milestone);
				array_push($bids, $bid);
			}
			
			return $bids;
		}
		
		public static function getUserByUid($uid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT * FROM `user` WHERE uid=%s", $uid);
			
			$resultSet = mysql_query($q);
			$user = null;
			
			if($row=mysql_fetch_row($resultSet)) {
				$uid = $row[0];
				$name = $row[1];
				$username = $row[2];
				$password = $row[3];
				$addTs = $row[4];
				$updateTs = $row[5];
				$address = $row[6];
				$birthdate = $row[7];
				$zipcode = $row[8];
				$active = $row[9];
				$email = $row[10];
				$stateProv = $row[11];
				$city = $row[12];
				$country = $row[13];
				$photo = $row[14];
				$phone = $row[15];
				$workPhone = $row[16];
				$skype = $row[17];
				$aim = $row[18];
				$resume = $row[19];
			
				$user = new User($uid, $name, $username, $password, $addTs, $updateTs, $address, $birthdate, $zipcode, $active, $email, $stateProv, $city, $country, $photo, $phone, $workPhone, $skype, $aim, $resume);
			}
			
			return $user;
		}
		
		public static function getUidByUsername($username) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT `uid` FROM `user` WHERE `username` = '%s'", $username);
			
			$resultSet = mysql_query($q);
			
			if($row = mysql_fetch_row($resultSet)) {
				return $row[0];
			}
			
			return null;
		}
		
		public static function insertNewUser($user) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("INSERT INTO `user` VALUES (default, '%s', '%s', '%s', default, default, 
						 '%s', str_to_date('%s', '%%m-%%d-%%Y'), '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user->getName(),
						$user->getUserName(), $user->getPassword(), $user->getAddress(), 
						$user->getBirthdate(), $user->getZip(), "Y", $user->getEmail(), $user->getState(),
						$user->getCity(), $user->getCountry(), $user->getPhoto(), $user->getPhone(), 
						$user->getWorkPhone(), $user->getSkype(), $user->getAim(), $user->getResume()
						);
			
			mysql_query($q);
			mysql_query("COMMIT");
		}
		
		
		public static function login($username, $password) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT `password` FROM `user` WHERE `username` = '%s'", $username);
			
			$resultSet = mysql_query($q);
			
			if($row=mysql_fetch_row($resultSet)) {
				$realPassword = $row[0];
				
				return $password == $realPassword;
			}
			
			return false;
		}
		
		public static function getBidsByUid($uid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$bids = array();
			$q = sprintf("SELECT * FROM `bid` WHERE `uid` = %d", $uid);
			
			$resultSet = mysql_query($q);
				
				
			while($row=mysql_fetch_row($resultSet)) {
				$uid = $row[0];
				$pid = $row[1];
				$message = $row[2];
				$startDate = $row[3];
				$endDate = $row[4];
				$addTs = $row[5];
				$updateTs = $row[6];
				$amount = $row[7];
				$charge = $row[8];
				$milestone = $row[9];
				
				$bid = new Bid($uid, $pid, $message, $startDate, $endDate, $addTs, $updateTs, $amount, $charge, $milestone);
				array_push($bids, $bid);
			}
			
			return $bids;
		}
		
		
		public static function insertNewJob($job) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("INSERT INTO `project` VALUES (default, '%s', '%s', %d, %d, '%s', '%s', default, default, ADDTIME(NOW(), '7 0:0:0.0'))", $job->getName(), 
				 $job->getDescription(), $job->getUpperBudget(), $job->getLowerBudget(), $job->getCurrencyType(), $job->getStatus());
				 
			mysql_query($q);
			
			
			
			mysql_query("COMMIT");
			
			return;
		}
		
		
		public static function insertNewUserProject($uid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			
			$q = sprintf("INSERT INTO `user_project` VALUES (%d, LAST_INSERT_ID(), default, default)", $uid);
			
			mysql_query($q);
			mysql_query("COMMIT");
			
			return;
			
			
		}
		
		
		public static function userOwnsJob($uid, $pid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT `uid` FROM `user_project` WHERE `pid` = %d", $pid);
			$resultSet = mysql_query($q);
			$return = false;
			
			if($row=mysql_fetch_row($resultSet)) {
				$ownerUid = $row[0];
				$return = $ownerUid == $uid;
			}
			
			return $return;
		}
		
		
		public static function userBidOnJob($uid, $pid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT `uid` FROM `bid` WHERE `pid` = %d", $pid);
			$resultSet = mysql_query($q);
			$result = false;
			
			while($row=mysql_fetch_row($resultSet)) {
				$currUid = $row[0];
				if($currUid == $uid) {
					return true;
				}
			}
			
			return $result;
		}
		
		
		public static function insertNewBid($bid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			//INSERT INTO `bid` VALUES (7,1, "I have done a job similar to this before.  Please consider me to do the job!", curdate(), date_add(curdate(), interval 30 day), default, default, 500);
			
			
			$q = sprintf("INSERT INTO `bid` VALUES (%d, %d, '%s', STR_TO_DATE('%s', '%%m-%%d-%%Y'), STR_TO_DATE('%s', '%%m-%%d-%%Y'), default, default, %f, %f, %d) ", $bid->getUid(),
					      $bid->getPid(), $bid->getMessage(), $bid->getStartDate(), $bid->getEndDate(), $bid->getAmount(), $bid->getCharge(), $bid->getMilestone());
						  
			mysql_query($q) or die("Error Executing Query");
			mysql_query("COMMIT");
			
		}
	
		public static function getUserProjectsByUid($uid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT * FROM `user_project` WHERE `uid` = %d", $uid);
			
			$resultSet = mysql_query($q) or die("Error Executing Query");
			
			$projects = array();
			$i = 0;
			
			while($row=mysql_fetch_row($resultSet)) {
				$uid = $row[0];
				$pid = $row[1];
				$addTs = $row[2];
				$updateTs = $row[3];
				
				$userProject = new UserProject($uid, $pid, $addTs, $updateTs);
				$projects[$i++] = $userProject;
			}
			
			return $projects;
		}
		
		
		public static function getAvgBidPriceByProjectId($jid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
		
			$q = sprintf("SELECT  AVG(`amount`) FROM `bid` WHERE `pid` = %d GROUP BY `pid`", $jid);
			
			$resultSet = mysql_query($q) or die("Error Executing Query");
			$avg = 0;
			if($row=mysql_fetch_row($resultSet)) {
				$avg = $row[0];
			}
			
			return $avg;
		}
		
		
		public static function insertNewProjectAward($projectAward) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("INSERT INTO `user_project_award` VALUES (%d, %d, default, default, 'N')", $projectAward->getUid(), $projectAward->getPid());
							
							
			mysql_query($q) or die("Error Executing Query");
			
			mysql_query("COMMIT");
		}
		
		
		
		public static function getBidderHiredForJob($jid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
		
			$q = sprintf("SELECT `uid` FROM `user_project_award` WHERE `pid` = %d", $jid);
			
			
			$resultSet = mysql_query($q) or die("Error Executing Query");
			$uid = null;
			
			if($row=mysql_fetch_row($resultSet)) {
				$uid = $row[0];
			}
			
			return $uid;
		}
		
		
		public static function getInBoxPrivateMessagesByUid($uid) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("SELECT * FROM `private_message` WHERE to_uid=%d", $uid);
			
			$resultSet = mysql_query($q) or die("Error Executing Query");
			
			$pms = array();
			$i = 0;
			while($row=mysql_fetch_row($resultSet)) {
				$fromUid = $row[0];
				$toUid = $row[1];
				$subject = $row[2];
				$message = $row[3];
				$date = $row[4];
				$addTs = $row[5];
				$updateTs = $row[6];
				
				$pms[$i++] = new PrivateMessage($fromUid, $toUid, $subject, $message, $date, $addTs, $updateTs);
			}
			
			return $pms;
		}
		
		public static function insertPrivateMessage($pm) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("INSERT INTO `private_message` VALUES(%d, %d, '%s', '%s', CURDATE(), default, default, 'N')", $pm->getFromUid(), $pm->getToUid(), $pm->getSubject() , $pm->getMessage());
			
			
			mysql_query($q) or die("Error Executing Query");
			mysql_query("commit");
			
		}
		
		/**
		private $Uid;
		private $MerchantId;
		private $MerchantRefCode;
		private $CardAccountNumber;
		private $CardExpirationMonth;
		private $CardExpirationYear;
		private $CardLastFourDigits;
		private $CardName;
		private $CardSecurityId;
		*/
		public static function insertCreditCard($cc) {
			$dbh = Connection::getConnection();
			mysql_select_db("sys_freelance", $dbh);
			
			$q = sprintf("INSERT INTO `credit_card` VALUES (%s, '%s', '%s', '%s', %s, %s, '%s', '%s', %s)", 
							$cc->getUid(), $cc->getMerchantId(), $cc->getMerchantRefCode(),
							$cc->getCardAccountNumber(), $cc->getExpirationMonth(), 
							$cc->getExpirationYear(), $cc->getLastFourDigits(), 
							$cc->getCardName(), $cc->getCardSecurityId());
			
			mysql_query($q) or die("Error Executing Query");
			mysql_query("commit");
		}
		
	}

?>