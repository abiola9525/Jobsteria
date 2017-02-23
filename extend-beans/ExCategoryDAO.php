<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CategoryDAO.php");

	class ExCategoryDAO extends CategoryDAO {
		public function __construct($dbh) {
			parent::__construct($dbh);
		}
		
		public function getCategoriesLexicographically() {
			$dbh = $this->dbh;
			$q = $dbh->prepare("select * from category order by name");
			$q->execute();
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
	}
?>