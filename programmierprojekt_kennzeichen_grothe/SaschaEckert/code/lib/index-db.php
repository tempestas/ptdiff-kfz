<?php

$connector = new MySqlConnector();

//var_dump($_GET);
if(isset($_GET["par"]) && isset($_GET["status"])){

$par=$_GET["par"];
$status=$_GET["status"];
$returnvalue=false;


switch ($status) {
    case "KEN":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_kurz like '$par';";
	$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "KRE":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_name like '$par';";
	$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "KENshort":
        $sql="SELECT `kreis_kurz`FROM `landkreis` WHERE kreis_kurz like '$par%';";
	$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "KREshort":
        $sql="SELECT `kreis_name` FROM `landkreis` WHERE kreis_name like '$par%';";
	$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    default:
        $returnvalue=json_encode(false);
        break;
}

print ($returnvalue);
}
else {
print json_encode(false);
}


class MySqlConnector{
	
	protected $link;
	private $server, $username, $password, $db;
	private $tablename = "serverUsage";
	
	function __construct(){
		$this->server = "localhost";
		$this->username = "db1176230-kfz";
		$this->password = "doenerali";
		$this->db = "db1176230-ptdiff";
		$this->connect();
		//print("verbindung hergestellt<br>");
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
	
	//TODO: make it static for file upload
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
	
	//array(ID, kreis_kurz, kreis_name, kreis_stadt, bundesland)
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
	
	public function execute($sql){
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
		else
		return false;

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

	

	// return: array(ID, kreis_kurz, kreis_name, kreis_stadt, bundesland)	
	public function getEntriesAsJSON($sql){
		$entries = $this->execute($sql);
		$result = Array();
		foreach ($entries as $line){
			//array_push($result, (array("ID" => $line['ID'], "kreis_kurz" => $line['kreis_kurz'], "kreis_name" => $line['kreis_name'], "kreis_stadt" => $line['kreis_stadt'], "bundesland" => $line['bundesland'])));
			array_push($result, $line);
		}

		if (empty($result))
			return json_encode(false);
		else
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
		//else;
			//return false;
	}

}

?>