<?php
/* 
 * LookUp for XALT Database
 * 
 */ 

$sql   = $_GET["query"];
 
try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $conn->prepare($sql);
    $query->execute();
    $total_rows = $query->rowCount();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    $cols = array();
    $cols = array_keys($result[0]);

    $jsonCol = array();
    foreach($cols as $column){
        $jsonCol[] = "{\"id\":\"\",\"label\":\"$column\",\"pattern\":\"\",\"type\":\"string\"}";
    }
    $strcols = "{\"cols\": [".implode(", ",$jsonCol)."],\"rows\": [";

    $jsonRows = array();
    foreach($result as $row){
    $jsonFields = array();
        foreach($cols as $column){
            $jsonFields[] = "{\"v\":\"" .$row[$column]. "\",\"f\":null}";
        }
        $jsonRows[] = "{\"c\": [".implode(", ",$jsonFields). "]}";
    }
    $strrows = implode(", ",$jsonRows). "]}";

    echo ($strcols . $strrows);


}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
