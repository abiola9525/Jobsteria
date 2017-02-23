<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "BidDAO.php");

	class ExBidDAO extends BidDAO {
		public function __construct($dbh) {
			parent::__construct($dbh);
		}
		
		public function getBidsByUid($uid) {
			return parent::getBidByAttributeMap(array("uid" => $uid));
		}
		
		public function getAverageBidPriceForProject($pid) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("SELECT  AVG(`charge`) as average FROM `bid` WHERE `pid` = ? GROUP BY `pid`");
			$q->execute(array($pid));
			$avg = $q->fetchColumn();
			return $avg;
		}
	}
?>