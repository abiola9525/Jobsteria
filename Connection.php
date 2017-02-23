<?PHP
	class Connection {
		private static $dbh;
		private function __construct() {
			self::$dbh = mysql_connect("localhost", "root", "Nitrate1");
		}
		
		public static function getConnection() {
			if(!self::$dbh) {
				new self();
			}
			return self::$dbh;
		}
	}
?>