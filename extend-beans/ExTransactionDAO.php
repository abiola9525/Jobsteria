<?php
	/**
	 * @author Gary Drocella
	 * @date 09/05/2014
	 * Time: 07:13pm
	 */

	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "TransactionDAO.php");
	
	class ExTransactionDAO extends TransactionDAO {
		public function __construct($dbh) {
			$this->dbh = $dbh;
		}
		
		public function getTransactionsByUidChronologically($uid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("select * from transaction where to_uid = ? or from_uid = ? ORDER BY add_ts DESC");
			$q->execute(array($uid, $uid));
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		
	}
?>