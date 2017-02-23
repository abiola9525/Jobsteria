<?php
	/**
	 * @author Gary Drocella
	 * @date 08/16/14
	 * Description: Configuration of website. 
	 * TODO: Have properties loaded from an init file. 
	 */
	class FreeConfiguration {
		var $configMap;

		private function __construct() {
			//hard code for now
			$this->configMap = array();
			$this->configMap["service_root_dir"] = "C:\\UwAmp\\www\\MyFreelancer\\service\\gen-php\\";
			$this->configMap["lib_root_dir"] = "C:\\UwAmp\\www\\MyFreelancer\\service\\gen-lib\\";
			$this->configMap["gen_dao_root_dir"] = "C:\\UwAmp\\www\\MyFreelancer\\gen-dao\\";
			$this->configMap["ex_dao_root_dir"] = "C:\\UwAmp\\www\\MyFreelancer\\extend-beans\\";
			$this->configMap["log4php"] = "C:\\UwAmp\\www\\MyFreelancer\\includes\\log4php\\";
			$this->configMap["stripe"] = "C:\\UwAmp\\www\MyFreelancer\\includes\\stripe-php-1.17.4\\lib\\";
			$this->configMap["res"] = "C:\\UwAmp\\www\\MyFreelancer\\resources\\";
			$this->configMap["log"] = "C:\\UwAmp\\www\\MyFreelancer\\log\\";
			$this->configMap["base_url"] = "localhost/MyFreeLancer/";
			$this->configMap["stripe_sk"] = "sk_test_41JaqARDvT3P1OgOq6n1JD60";
		}
		
		public function getProperty($property) {
			return $this->configMap[$property];
		}
		
		public static function getInstance() {
			static $instance;
			if(empty($instance)) {
				$instance = new FreeConfiguration();
			}
			return $instance;
		}
	}
?>