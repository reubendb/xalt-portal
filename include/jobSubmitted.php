<?php
/*
 * Get number of jobs (JobId not RunID) for given sysHost and date range. 
 */
$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT MONTH(xr.date) AS Mon_numeric, 
        MONTHNAME(xr.date) AS Month, 
        COUNT(DISTINCT xr.job_id) as Jobs
        FROM xalt_run xr 
        WHERE xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate'  AND '$endDate'
        GROUP BY Month
        ORDER BY Mon_numeric desc 
        ";

#    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Month\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Jobs\",\"pattern\":\"\",\"type\":\"number\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    if ($row_num == $total_rows){
        echo "{\"c\":[{\"v\":\"" . $row['Month'] . "\",\"f\":null},{\"v\":" . $row['Jobs'] . ",\"f\":null}]}";
    } else {
        echo "{\"c\":[{\"v\":\"" . $row['Month'] . "\",\"f\":null},{\"v\":" . $row['Jobs'] . ",\"f\":null}]}, ";
    } 
}
echo " ] }";
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
