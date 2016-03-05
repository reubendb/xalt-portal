<?php
/* 
 * Get active users based on sysHost and given date range.
 * */

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT MONTH(xr.date) AS Mon_numeric, 
        DATE_FORMAT(xr.date, '%b%y') AS Month, 
        YEAR(xr.date) AS Year, 
        COUNT(DISTINCT xr.user) as Users
        FROM xalt_run xr 
        WHERE xr.syshost = '$sysHost' AND 
        xr.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY Month, Year
        ORDER BY Year desc, Mon_numeric desc 
        ";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{\"cols\": [
{\"id\":\"\",\"label\":\"Month\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Users\",\"pattern\":\"\",\"type\":\"number\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Month'] . "\",\"f\":null},
    {\"v\":" . $row['Users'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Month'] . "\",\"f\":null},
    {\"v\":" . $row['Users'] . ",\"f\":null}
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
