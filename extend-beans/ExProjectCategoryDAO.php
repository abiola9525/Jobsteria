<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\..\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "Project_categoryDAO.php");
	
	
	class ExProjectCategoryDAO extends Project_categoryDAO {
		public function __construct($dbh) {
			parent::__construct($dbh);
		}
		
		public function insertProjectCategoryByLastPid($cid) {
			$dbh= $this->dbh;
			$q = $dbh->prepare("INSERT INTO `project_category` VALUES (LAST_INSERT_ID(), ?,  default, default)");
			$q->execute(array($cid));
		}
	}
?>