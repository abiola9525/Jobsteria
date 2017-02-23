<?php
	class DBOConnection {
		private static $dbh;
		
		public static function getConnection() {
			if(self::$dbh == null) {
				self::$dbh = new PDO('mysql:host=localhost;port=3306;dbname=sys_freelance', 'root', 'root');
				self::$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
			}
			return self::$dbh;
		}
	}
?>