<?PHP
	require_once 'Connection.php';
	require_once 'Job.php';
	require_once 'QueryUtility.php';
	
	class JobList {
		private $jobList;
		private $size;
		private $min;
		private $max;
		
		public function __construct() {
			$this->jobList = array();
			$this->size = 0;
			$this->min = 0;
			$this->max = 0;
		}
		
		public function loadJobs($filter) {
			$dbh = Connection::getConnection();
			
			mysql_select_db("sys_freelance", $dbh);
			
			/* TODO: Add Filtering by distance, tag, etc. */
			
			$resultSet = mysql_query("SELECT * FROM `project` WHERE status='BID' LIMIT 0,10", $dbh);
			
			while($row = mysql_fetch_row($resultSet)) {
				$jid = $row[0];
				$name = $row[1];
				$description = $row[2];
				$upperBudget = $row[3];
				$lowerBudget = $row[4];
				$currencyType = $row[5];
				$status = $row[6];
				$addTs = $row[7];
				$updateTs = $row[8];
				$endBidDate = $row[9];
				
				$job = new Job($jid, $name, $description, $upperBudget, $lowerBudget, $currencyType, $status, $addTs, $updateTs, $endBidDate);
				$this->jobList[$this->size] = $job;
				$this->size++;
			}
		}
		
		public function toString() {
			$str = "<table name=\"jobTable\" border=\"1\">\n";
			$str .= "<tr><th>Name</th><th>Description</th><th>Budget</th><th>Status</th><th>Created</th></tr>";
			
			$len = count($this->jobList);
			
			for($i = 0; $i < $len; $i++) {
				$currJob = $this->jobList[$i];
				$str .= "<tr><td><a href=\"./JobView.php?jid=" . $currJob->getJid() . "\">" . $currJob->getName() . "</a></td><td>" . $currJob->getDescription() . "</td><td>" . $currJob->getCurrencyType() .
				$currJob->getLowerBudget() . "-" . $currJob->getCurrencyType() . $currJob->getUpperBudget() . "</td><td>" . $currJob->getStatus() .
				"</td><td>" . $currJob->getCreationDate() . "</td>";
					
				if(isset($_SESSION["uid"])) {
					if(QueryUtility::userBidOnJob($_SESSION["uid"], $currJob->getJid())) {
						$str .= "<td><img src= \"./imgs/CheckMark.png\" /></td>";
					}
				}
					
				$str .= "</tr>";
			}
			
			$str.= "</table>";
			
			return $str;
		}
		
	}
?>

