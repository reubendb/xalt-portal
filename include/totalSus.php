<?php
/* 
 * Get totatSUs, though this query may not given the correct picture.
 */ 
$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];

try {

    print_r($sysHost, $startDate, $endDate);
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT ROUND(SUM(xr.run_time * xr.num_cores)/3600) AS 
        TotalSUs, MONTH(xr.date) AS mon, 
        MONTHNAME(xr.date) AS Month 
        FROM xalt_run xr WHERE xr.syshost='$sysHost' AND 
        xr.date BETWEEN '$startDate' AND '$endDate'
        GROUP BY  mon 
        ORDER BY mon desc, TotalSUs desc;
    ";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Month\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"TotalSUs\",\"pattern\":\"\",\"type\":\"number\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Month'] . "\",\"f\":null},
    {\"v\":" . $row['TotalSUs'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Month'] . "\",\"f\":null},
    {\"v\":" . $row['TotalSUs'] . ",\"f\":null}
    ]}, ";
    } 

}
echo " ] }";
}

catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
