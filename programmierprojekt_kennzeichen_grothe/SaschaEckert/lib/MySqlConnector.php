<?php


//var_dump($_GET);
if (strcmp($_GET["request"], "getPoints") == 0){
	//var_dump($this->getEntriesAsJavascriptArray());
	$connection = new MySqlConnector();
	print($connection->getEntriesAsJSON());
}
if (strcmp($_GET["request"], "getPointsAsGeoJson") == 0){
	$connection = new MySqlConnector();
	print($connection->getEntriesAsGeoJSON($_GET["type"]));
}
if (strcmp($_GET["request"], "getPointsInShapeAsGeoJson") == 0){
	$connection = new MySqlConnector();
	print($connection->getEntriesAsGeoJSON("PointsInShape"));
}


class MySqlConnector{
	
	protected $link;
	private $server, $username, $password, $db;
	private $tablename = "serverUsage";
	
	function __construct(){
		$this->server = "localhost";
		$this->username = "root";
		$this->password = "sqlpw";
		$this->db = "nutzerverwaltung";
		$this->connect();
		//print("verbindung hergestellt<br>");

		if ($_GET["type"] == 2) 
			$this->tablename = "radBoxen";

	}
	
	function __destruct(){
		$this->disconnect();
		//print("verbindung getrennt<br>");
	}
	
	private function connect(){
		$this->link = mysql_connect($this->server, $this->username, $this->password);
		mysql_select_db($this->db, $this->link);
	}
	
	private function disconnect(){
		mysql_close($this->link);
	}
	
	public function loadCVS($path){
		//$this->clearTable();
		
		$sqlLoadCVS = "
		LOAD DATA LOCAL INFILE '{$path}'
        INTO TABLE `{$this->tablename}`
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        (USERID, MINUTES, REGTIME, IP, OS, BROWSER)";
		
		$result = mysql_query($sqlLoadCVS) or die ('Query failed: '.mysql_error());
		if (!$result)
			print ("fail on loading cvs<br>");
		else
			print ("table filled with cvs contents<br>");
		//$this->resovleIPS();
	}
	
	private function clearTable(){
		$sqlDeleteTable = "DROP TABLE IF EXISTS $this->tablename;";
		$sqlCreateTable = "CREATE TABLE $this->tablename (USERID INTEGER, MINUTES INT, REGTIME VARCHAR(20), IP VARCHAR(15), OS VARCHAR(10), BROWSER VARCHAR(10),LATITUDE FLOAT, LONGITUDE FLOAT, PRIMARY KEY(USERID))";
		
		$result = mysql_query($sqlDeleteTable) or die ('Query failed: '.mysql_error());
		if (!$result)
			print ("fail on deleting table<br>");
		else {
			$result = mysql_query($sqlCreateTable) or die ('Query failed: '.mysql_error());
			if (!$result)
				print ("fail on creating table<br>");
			else
				print ("table cleared<br>");
		}
	}
	
	
	public function getEntries(){
		//$sql = "SELECT LONGITUDE, LATITUDE FROM $this->tablename WHERE LONGITUDE is not null and LATITUDE is not null";

		$sql = "
			SELECT 
				USERID,MINUTES,REGTIME,IP,OS,BROWSER,LATITUDE,LONGITUDE,X(geometry) AS X,Y(geometry) AS Y 
			FROM 
			$this->tablename
			WHERE 
				geometry is not null
			";
				
			return $this->execute($sql);
				
	}

	private function execute($sql){
		$result = mysql_query($sql) or die ('Query failed: '.mysql_error());

		if ($result){
			$lines = array();
			while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
				array_push($lines,$line);
				//var_dump($line);
			}
			//var_dump($lines);
			return $lines;
		}
		else;
		//return false;

	}

	// return: array(ID, kreis_kurz, kreis_name, kreis_stadt, bundesland)	
	public function getEntriesAsJSON(){


		$entries = $this->getEntries();
		$result = Array();

		foreach ($entries as $line){
//			array_push($result, (array("ID".$line[kreis_id] => array("" => $line[X], "" => $line[Y]))));
			array_push($result, (array("ID" => $line[kreis_id], "kreis_kurz" => $line[kreis_kurz], "kreis_name" => $line[kreis_name], "kreis_stadt" => $line[kreis_stadt], "bundesland" => $line[bundesland])));
		}

		return json_encode($result);

	}

	
	public function getHeader(){
		$sql = "DESCRIBE $this->tablename";
		$result = mysql_query($sql) or die ('Query failed: '.mysql_error());
		
		if ($result){
			$lines = array();
			while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
				array_push($lines,$line);
				//var_dump($line);
			}
			//var_dump($lines);
			return $lines;
			
		}
		else;
			//return false;
	}

}

?>
