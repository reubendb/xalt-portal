<?php

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT xr.uuid AS Unique_ExecId, count(1) AS No_Jobs, 
        SUBSTRING_INDEX(xr.exec_path , '/', -1) AS Executable, 
        MIN(date) AS MinDate, MAX(date) AS MaxDate, 
        ROUND(SUM(xr.run_time/3600)) as CPU_Hrs 
        FROM xalt_run xr 
        WHERE xr.user = '$userId' AND
        xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate' AND '$endDate'
        GROUP BY Unique_ExecId, Executable 
        ORDER BY No_Jobs DESC;
    ";

    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Unique_ExecID\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"No_Jobs\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Executable\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"MinDate\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"MaxDate\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"CPU_Hrs\",\"pattern\":\"\",\"type\":\"number\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Unique_ExecId'] . "\",\"f\":null},
    {\"v\":" . $row['No_Jobs'] . ",\"f\":null},  
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
    {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
    {\"v\":" . $row['CPU_Hrs'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Unique_ExecId'] . "\",\"f\":null},
    {\"v\":" . $row['No_Jobs'] . ",\"f\":null},  
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
    {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
    {\"v\":" . $row['CPU_Hrs'] . ",\"f\":null}
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
