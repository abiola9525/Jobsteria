<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "User_project_awardDAO.php");
	
	class ExUserProjectAwardDAO extends User_project_awardDAO {
		public function __construct($dbh) {	
			parent::__construct($dbh);
		}
		
		public function getUserProjectAwardByAttributeMapInRangeOrderedBy($fkMap, $r1, $r2, $orderedBy) {
			$dbh=$this->dbh;
			$qStr="SELECT * FROM user_project_award WHERE " . self::generateFilter($fkMap) . " ORDER BY $orderedBy DESC LIMIT ? OFFSET ? ";
			$q=$dbh->prepare($qStr);
			$vals=array_values($fkMap);
			array_push($vals, $r1, $r2);
			$q->execute($vals);
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		
		public function userHiredForJob($pid) {
			$result = parent::getUser_project_awardByAttributeMap(array("pid" => $pid));
			if($result != null) {
				return $result[0];
			}
			return null;
		}
		
		public function incrementMilestoneRequestReject($pid, $uid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("update user_project_award set `milestone_request_reject_count`=milestone_request_reject_count+1 where pid=? and uid=?");
			$q->execute(array($pid, $uid));
		}
	}
?>