<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CitiesDAO.php");
	
	class ExCitiesDAO extends CitiesDAO {
		public function __construct($dbh) {
			parent::__construct($dbh);
		}
		
		public function getCity($countryId, $regionId, $cityName) {
			$dbh = $this->dbh;
			$q = $dbh->prepare("select * from cities where CountryID = ? AND RegionID = ? AND City LIKE CONCAT('%', ?, '%')");
			$q->execute(array($countryId, $regionId, $cityName));
			
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;

		}
	}
?>