<?php

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"];
$uuid       = $_GET["uuid"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT xr.job_id AS Job_ID, xr.date AS Date, 
        FROM_UNIXTIME(xr.start_time) AS StartTime,
        FROM_UNIXTIME(xr.end_time) AS EndTime,
        xr.account AS Account,
        CONCAT(xr.num_cores, '      ', xr.num_nodes, '      ', xr.num_threads) AS
        'Cores  Nodes  Threads',
        queue AS Queue
        FROM xalt_run xr 
        WHERE xr.uuid = '$uuid' AND
        xr.user = '$userId' AND
        xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate' AND '$endDate'
        GROUP BY Job_ID
        ORDER BY Date DESC;
    ";

    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Job_ID\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"StartTime\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"EndTime\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Account\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Cores  Nodes  Threads\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Queue\",\"pattern\":\"\",\"type\":\"string\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Job_ID'] . "\",\"f\":null},
    {\"v\":\"" . $row['Date'] . "\",\"f\":null},
    {\"v\":\"" . $row['StartTime'] . "\",\"f\":null},
    {\"v\":\"" . $row['EndTime'] . "\",\"f\":null},
    {\"v\":\"" . $row['Account'] . "\",\"f\":null},
    {\"v\":\"" . $row['Cores  Nodes  Threads'] . "\",\"f\":null},
    {\"v\":\"" . $row['Queue'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Job_ID'] . "\",\"f\":null},
    {\"v\":\"" . $row['Date'] . "\",\"f\":null},
    {\"v\":\"" . $row['StartTime'] . "\",\"f\":null},
    {\"v\":\"" . $row['EndTime'] . "\",\"f\":null},
    {\"v\":\"" . $row['Account'] . "\",\"f\":null},
    {\"v\":\"" . $row['Cores  Nodes  Threads'] . "\",\"f\":null},
    {\"v\":\"" . $row['Queue'] . "\",\"f\":null}
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
