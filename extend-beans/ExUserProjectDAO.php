<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "User_projectDAO.php");
	
	class ExUserProjectDAO extends User_projectDAO {
		public function __construct($dbh) {
			parent::__construct($dbh);
		}
		
		public function getProjectsByUid($uid) {
			return parent::getUser_projectByAttributeMap(array("uid" => $uid));
		}
		
		public function getUserProjectsByAttributeMapInRangeOrderedBy($fkMap, $r1, $r2, $orderBy) {
			$dbh=$this->dbh;
			$qStr="SELECT * FROM user_project WHERE " . self::generateFilter($fkMap) . " ORDER BY $orderBy DESC LIMIT ? OFFSET ?";
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
		
		public function userOwnsProject($uid, $pid) {
			$result = parent::getUser_projectByAttributeMap(array("uid" => $uid, "pid" => $pid));
			
			if($result == null) { 
				return false;
			}
			return count($result) > 0;
		}
		
		public function insertByLastId($uid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("INSERT INTO `user_project` VALUES (?, LAST_INSERT_ID(), default, default)");
			$q->execute(array($uid));
			$q = $dbh->prepare("SELECT pid from `user_project` WHERE pid=LAST_INSERT_ID()");
			$q->execute();
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
	}
?>