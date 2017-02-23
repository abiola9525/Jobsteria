<?PHP
	class CountriesDAO {
		var $dbh;
		function __construct($dbh) {
		$this->dbh=$dbh;
		}
		public function getCountriesByCountryId($pk) {
			$dbh=$this->dbh;
			$q=$dbh->prepare("SELECT * FROM countries WHERE CountryId= ?");
			$q->execute(array("$pk"));
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		protected function generateFilter($map) {
			$filterStr = "";
			foreach($map as $k => $v) {
				$filterStr .= " $k= ? AND";
			}
			$filterStr = substr($filterStr,0,strlen($filterStr)-4);			return $filterStr;
		}
		public function getCountriesByAttributeMapInRange($fkMap, $r1, $r2) {
			$dbh=$this->dbh;
			$qStr="SELECT * FROM countries WHERE " . self::generateFilter($fkMap) . " LIMIT ? OFFSET ?";
			$q=$dbh->prepare($qStr);
			$vals=array_values($fkMap);
			array_push($vals, $r1, $r2);
			$q->execute($vals);
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		public function getAllCountriessInRange($r1, $r2) {
			$dbh=$this->dbh;
			$q = $dbh->prepare('SELECT * FROM countries LIMIT ? OFFSET ?');
			$q->execute(array($r1, $r2));
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		public function getAllCountriess() {
			$dbh=$this->dbh;
			$q = $dbh->prepare('SELECT * FROM countries');
			$q->execute();
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		public function getCountriesByAttributeMap($fkMap) {
			$dbh=$this->dbh;
			$qStr="SELECT * FROM countries WHERE " . self::generateFilter($fkMap);
			$q=$dbh->prepare($qStr);
			$q->execute(array_values($fkMap));
			$returnTuples=array();
			while(($rs=$q->fetch(PDO::FETCH_OBJ))) {
				array_push($returnTuples,$rs);
			}
			return $returnTuples;
		}
		public function insertCountries($map) {
			$dbh=$this->dbh;
			$genQuery = "INSERT INTO countries %s VALUES %s";
			$colNames = "(";
			$colVals = "(";
			$valArr = array();
			foreach($map as $k => $v) {
				$colNames .= "$k ,";
				$colVals .= "? ,";
				array_push($valArr,$v);
			}
			$colNames = substr($colNames, 0, strlen($colNames)-1) . ")";
			$colVals = substr($colVals, 0, strlen($colVals)-1) . ")";
			$genQuery = sprintf($genQuery, $colNames, $colVals);
			$q=$dbh->prepare($genQuery);
			$q->execute($valArr);
		}
		public function updateCountries($updateMap, $filterMap) {
			$dbh=$this->dbh;
			$genQuery = "UPDATE countries SET %s WHERE %s";
			$toUpdate = str_replace("AND", ",", self::generateFilter($updateMap));
			$toFilter = self::generateFilter($filterMap);
			$genQuery = sprintf($genQuery, $toUpdate, $toFilter);
			$q=$dbh->prepare($genQuery);
			$q->execute(array_merge(array_values($updateMap),array_values($filterMap)));
		}
		public function deleteCountries($deleteMap) {
			$dbh=$this->dbh;
			$genQuery = "DELETE FROM countries WHERE " . self::generateFilter($deleteMap);
			$q=$dbh->prepare($genQuery);
			$q->execute(array_values($deleteMap));
		}
	}
?>
