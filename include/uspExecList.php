<?php

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT 
        SUBSTRING_INDEX(xr.exec_path , '/', -1) AS Executable, 
        MIN(date) AS MinDate, MAX(date) AS MaxDate, 
        count(1) AS No_Jobs
        FROM xalt_run xr 
        WHERE xr.user = '$userId' AND
        xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate' AND '$endDate'
        GROUP BY Executable 
        ORDER BY No_Jobs DESC;
    ";

    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Executable\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"MinDate\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"MaxDate\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"No_Jobs\",\"pattern\":\"\",\"type\":\"number\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
    {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
    {\"v\":" . $row['No_Jobs'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
    {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
    {\"v\":" . $row['No_Jobs'] . ",\"f\":null}
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
