<?php
	require_once(dirname(__FILE__) . "..\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "ProjectDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	/**
	 * @author Gary
	 * @date 08/17/14
	 * Time: 4:36pm
	 */
	class ExProjectDAO extends ProjectDAO {
		private $logger;
		
		public function __construct($dbh) {
			parent::__construct($dbh);
			Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
			$this->logger = Logger::getLogger(__CLASS__);
		}
		
		public function getProjectByAttributeMapInRangeOrderedBy($fkMap, $r1, $r2, $orderBy) {
			$dbh=$this->dbh;
			$qStr="SELECT * FROM project WHERE " . self::generateFilter($fkMap) . " ORDER BY $orderBy DESC LIMIT ? OFFSET ?";
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
		
		public function advancedSearchOnProject($eqMap, $regexMap, $startDate, $endDate, $cid, $scid, $base, $rc) {
			$dbh = $this->dbh;
			$qStr = "SELECT * FROM project WHERE ";
			$regFilter = self::generateRegexFilter($regexMap);
			$dateVals = array();
			if(count($regexMap) > 0 && count($eqMap) > 0) {
				$regFilter .= " AND ";
			}
			$qStr .= $regFilter . parent::generateFilter($eqMap);

			if(count($eqMap) > 0 && $startDate != null) {
				$qStr .= " AND ";
			}
			
			if($startDate != null) {
				$qStr .= " job_start_date >= STR_TO_DATE(?, '%Y-%m-%d') ";
				array_push($dateVals, $startDate);
			}
			
			if($startDate != null && $endDate != null) {
				$qStr .= " AND ";
			}
			
			if($endDate != null) {
				$qStr .= " job_projected_end_date >= STR_TO_DATE(?, '%Y-%m-%d') ";
				array_push($dateVals, $endDate);
			}
			
			$cVals = array();
			
			if($scid != null) {
				array_push($cVals, $scid);
				
				if(count($eqMap) != 0 || count($regexMap) !=0 || $endDate != null || $startDate != null) {
					$qStr .= " AND ";
				}
				
				$qStr .= " pid in (SELECT pid FROM project_category WHERE cid=?) ";
			}
			else if($cid != null)  {
				if(count($eqMap) != 0 || count($regexMap) !=0 || $endDate != null || $startDate != null) {
					$qStr .= " AND ";
				}
				
				$qStr .= " pid in (SELECT pid FROM project_category WHERE cid=? or cid in (select cid from category where parent_cid=?)) ";
				array_push($cVals, $cid, $cid);
			}
			
			
			$qStr .=  " LIMIT ? OFFSET ?";
			$vals = array(); //array_merge(array_values($regexMap), array_values($eqMap), $dateVals);
			
			foreach($regexMap as $v) {
				$toks = explode(" ", $v);
				$vals = array_merge($vals, $toks);
			}
			
			$vals = array_merge($vals, array_values($eqMap), $dateVals, $cVals);
			array_push($vals, $rc, $base);
			
			$this->logger->debug("Generate Query: " . $qStr);
			//$this->logger->debug("With Values: " . var_export($vals));
			
			$q = $dbh->prepare($qStr);
			$q->execute($vals);
			
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
				
			$this->logger->debug("Found " . count($returnTuples) . " Tuples!");
			return $returnTuples;
		}
		
		
		public function getJobCountByCid($cid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("select count(pid) as count from project_category where cid=?");
			$q->execute(array("cid" => $cid));
			
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
				
			if($returnTuples != null) {
				return $returnTuples[0]->count;
			}
			
			return 0;
		}
		
		public function getProjectsByCid($cid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("SELECT * FROM project WHERE pid in (SELECT pid FROM project_category WHERE cid=?) ORDER BY add_ts DESC");
			$q->execute(array($cid));
			
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		
		protected function generateRegexFilter($regexMap) {
			$f = "";
			foreach($regexMap as $k => $v) {
				$toks = explode(" ", $v);
				foreach($toks as $tok) {
					$f .= " $k LIKE CONCAT('%', ?, '%') OR";
				}
				$f = substr($f, 0, strlen($f)-2);
				$f .= " AND";
			}
			$f = substr($f, 0, strlen($f)-3);
			return $f;
		}
		
	}
?>