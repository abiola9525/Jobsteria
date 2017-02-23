<?php

	/** TODO: LOOK INTO USING PHP __DIR__ To fix this fucked up mess. */
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "UserDAO.php");
	require_once(FreeConfiguration::getInstance()->getProperty("log4php") . "Logger.php");
	
	class ExUserDAO extends UserDAO {
		private $logger;
		
		public function __construct($dbh) {
			parent::__construct($dbh);
			Logger::configure(FreeConfiguration::getInstance()->getProperty("res") . "config.xml");
			$this->logger = Logger::getLogger(__CLASS__);
		}
		
		public function getUserByUid($uid) {
			$u = parent::getUserByUid($uid);
			if(count($u) <= 0) {
				return null;
			}
			
			return $u[0];
		}
		
		
		public function canLogin($username, $password) {
			$this->logger->debug("canLogin $username $password ?");
			$tups = self::getUserByAttributeMap(array("username" => $username));

			if(count($tups) < 0) {
				return false;
			}
			
			$passHash = md5($password);
			
			$this->logger->debug("Comparing $passHash with $password");
			
			if($passHash == $tups[0]->password) {
				return true;
			}
			
			return false;
		}
		
		public function getUidByUsername($username) {
			$u = parent::getUserByAttributeMap(array("username" => $username));
			
			if(count($u) < 0) {
				return -1;
			}
			$uid = $u[0]->uid;
			return $uid;
		}
		
	}
?>