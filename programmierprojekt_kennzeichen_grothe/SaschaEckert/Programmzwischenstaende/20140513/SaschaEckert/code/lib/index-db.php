<?php

$connector = new MySqlConnector();

//var_dump($_GET);
if(isset($_GET["par"]) && isset($_GET["status"])){

$par=$_GET["par"];
$status=$_GET["status"];
$returnvalue=false;
$sql;

header("Access-Control-Allow-Origin: *"); //Benoetigt fuer den Zugriff ueber Ajax

switch ($status) {
    case "KEN":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_kurz like '$par' ORDER BY kreis_kurz;";
		$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "KRE":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_stadt like '$par' ORDER BY kreis_stadt ;";
		$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "BDL":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and bundesland.name like '$par' ORDER BY kreis_kurz;";
		$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "BDLshort":
    if ($par == null || $par == ""){
      $sql="SELECT bundesland.name AS bundesland FROM bundesland ORDER BY bundesland.name;";
			$returnvalue=$connector->getEntriesAsJSON($sql);
      }
		else
    {
    $sql="SELECT bundesland.name AS bundesland FROM bundesland WHERE bundesland.name like '$par%' ORDER BY bundesland.name;";
			$returnvalue=$connector->getEntriesAsJSON($sql);
      }
        break;
	case "KENshort":
        $sql="SELECT `kreis_kurz`FROM `landkreis` WHERE kreis_kurz like '$par%' ORDER BY kreis_kurz;";
		if ($par == "")
			$returnvalue=json_encode(false);
		else
			$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "KREshort":
        $sql="SELECT `kreis_stadt` FROM `landkreis` WHERE kreis_stadt like '$par%' ORDER BY kreis_stadt;";
		if ($par == "")
			$returnvalue=json_encode(false);
		else
			$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
	case "KENlong":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_kurz like '$par%' ORDER BY kreis_kurz;";
		if ($par == "")
			$returnvalue=json_encode(false);
		else
			$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
	case "KRElong":
        $sql="SELECT landkreis.id AS ID, `kreis_kurz`, `kreis_name`, `kreis_stadt`, bundesland.name AS bundesland FROM `landkreis`, bundesland WHERE `bd_id` = bundesland.id and kreis_stadt like '$par%' ORDER BY kreis_kurz;";
		if ($par == "")
			$returnvalue=json_encode(false);
		else
			$returnvalue=$connector->getEntriesAsJSON($sql);
        break;
    case "exportCSV":
		$connector->exportToCSV();
        break;
    case "exportXML":
		$connector->exportToXML();
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

if (isset($_GET["debug"])){
print "<br><br>";
var_export($sql);
}


class MySqlConnector{
	
	protected $link;
	private $server, $username, $password, $db;
	private $tablename = "landkreis";
	
	function __construct(){
		$this->server = "localhost";
		$this->username = "db1176230-kfz";
		$this->password = "doenerali";
		$this->db = "db1176230-ptdiff";
		$this->connect();
		//SET NAMES 'utf8';  
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

		$result = mysql_query("SET NAMES 'utf8';") or die ('Query failed: '.mysql_error());

		$result = mysql_query($sql) or die ('Query failed: '.mysql_error());

		if ($result){
			$lines = array();
			while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
				array_push($lines,$line);
			}
			return $lines;
		}
		else
		return false;

	}

		

	// return: array(ID, kreis_kurz, kreis_name, kreis_stadt, bundesland)	
	public function getEntriesAsJSON($sql){
		$entries = $this->execute($sql);
		$result = Array();
		foreach ($entries as $line){
			array_push($result, $line);
		}

if (isset($_GET["debug"])){
print "<br><br>";
var_export($result);
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

	public function exportToCSV(){
		$sql = "SELECT * FROM $this->tablename;";
		//print $sql;
		$dump = $this->execute($sql);
		$delimeter=";";
		$filename="export";

 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: application/csv; charset=utf-8");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
 
		$fh = @fopen( 'php://output', 'w' );
 
		$headerDisplayed = false;
 
		foreach ( $dump as $data ) {
		    // Add a header row if it hasn't been added yet
		    if ( !$headerDisplayed ) {
		        // Use the keys from $data as the titles
		        fputcsv($fh, array_keys($data), $delimeter);
		        $headerDisplayed = true;
		    }
 
		    // Put the data into the stream
		    fputcsv($fh, $data, $delimeter);
		}
		// Close the file
		fclose($fh);

	}

	public function exportToXML(){

		$sql = "SELECT * FROM $this->tablename;";
		//print $sql;
		$dump = $this->execute($sql);
		$delimeter=";";
		$filename="export.xml";

 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/xml; charset=utf-8");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
 
		$fh = @fopen( 'php://output', 'w' );
 
		$headerDisplayed = false;
 
/*		foreach ( $dump as $data ) {
		    // Add a header row if it hasn't been added yet
		    if ( !$headerDisplayed ) {
		        // Use the keys from $data as the titles
		        fputcsv($fh, array_keys($data), $delimeter);
		        $headerDisplayed = true;
		    }
 
		    // Put the data into the stream
		    fputcsv($fh, $data, $delimeter);
		}
		// Close the file
		fclose($fh);
*/


















//export
header('Content-Type: text/xml');
                
include 'config.inc.php'; 
 
$xml = new SimpleXMLElement("<?xml version='1.0' standalone='yes'?><xml/>");

$arr = array(array("name"=>"first", "type"=>"none"), array("name"=>"second", "type"=>"true"));
$arr= $dump;


 /*
foreach ($arr as $data){
    $row = $xml->addChild('line');
    foreach ($data as $key => $val){
        $row->addChild($key,utf8_encode($val));
    }
}*/

foreach ($arr as $key=>$value){
    $row = $xml->addChild($key);
    foreach ($value as $key => $val){
        $row->addChild($key,utf8_encode($val));
    }
}
 
{
  echo $xml->asXML();
}



//import
/*
$xml2 = "<xml><line><name>first</name><type>none</type></line><line><name>second</name><type>true</type></line></xml>";

$foo =  simplexml_load_string($xml2, null, LIBXML_NOCDATA);
$foo = json_decode(json_encode($foo, 1));

//var_dump (xml2array($xml2));

var_export($foo);
*/

	}




//public function exportToXML(){
//http://stackoverflow.com/questions/486757/how-to-generate-xml-file-dynamically-using-php
//}


}

?>
