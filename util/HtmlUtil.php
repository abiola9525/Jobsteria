<?php
	require_once(dirname(__FILE__) . "\\..\\util\\FreeConfiguration.php");
	require_once(dirname(__FILE__) . "\\..\\DBOConnection.php");
	require_once(FreeConfiguration::getInstance()->getProperty("gen_dao_root_dir") . "CountriesDAO.php");

	/**
	 * @author Gary Drocella
	 * @date 08/16/2014
	 * Time 12:18pm
	 */
	class HtmlUtil {
		
		/**
		 * 
		 */
		public static function getCurrency($code) {
			if($code == "US") {
				return "$";
			}
			else if($code == "EURO") {
				return "&#163;";
			}
			else if($code == "IR") {
				return "&#8377;";
			}
		}
		
		public static function thumbnailHeightFromWidth($path, $scaleWidth) {
			list($width, $height) = getimagesize($path);
			$scaleHeight = $height*($scaleWidth/$width);
			return $scaleHeight;
		}
		
		public static function getCountryOptionSelection() {
			$dbh = DBOConnection::getConnection();
			$countryDao = new CountriesDAO($dbh);
			$cntryList = $countryDao->getAllCountriess();
			
			echo "<option value=\"\">SELECT ONE</option>";
			foreach($cntryList as $v) {
				echo "<option value=\"" . $v->CountryId . "\">" . $v->Country . "</option>"; 
			}
		}
	}
?>