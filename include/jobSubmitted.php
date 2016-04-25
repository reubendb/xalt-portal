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

    $datetime1 = strtotime($startDate);
    $datetime2 = strtotime($endDate);

    $days = ($datetime2-$datetime1)/(3600*24);
    $monFlag = False; $weekFlag = False; $dayFlag = False;

    switch(true) {
    case ($days > 30) :           # group by month 
        $dateFormat = " DATE_FORMAT(xr.date, '%b') AS Month ";
        $groupBy    = " GROUP BY Month, Year ";
        $monFlag    = True;
        break;
    case ($days < 30 && $days > 7):    # group by week
        $dateFormat = " DATE_FORMAT(xr.date, '%u') AS Week ";
        $groupBy    = " GROUP BY Week, Year ";
        $weekFlag   = True;
        break;
    case ($days < 7) :            # group by day
        $dateFormat = " DATE_FORMAT(xr.date, '%d-%b') AS Day ";
        $groupBy    = " GROUP BY Day, Year ";
        $dayFlag    = True;
        break;
    }

    $sql = "SELECT $dateFormat,
        COUNT(DISTINCT xr.job_id) as Jobs,
        date(min(xr.date)) as DateTimeRange,
        YEAR(xr.date) as Year
        FROM xalt_run xr 
        WHERE xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate 00:00:00'  AND '$endDate 23:59:59'
        $groupBy
        ORDER BY Year desc, DateTimeRange ASC; 
        ";
    #    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"DateTimeRange\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Jobs\",\"pattern\":\"\",\"type\":\"number\"} 
    ], 
    \"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;
        if ($monFlag){
            if ($row_num == $total_rows){
                echo "{\"c\":[
            {\"v\":\"" . $row['Month'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}";
            } else {
                echo "{\"c\":[
            {\"v\":\"" . $row['Month'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}, ";
            } 
        } 
        elseif ($weekFlag) {
            if ($row_num == $total_rows){
                echo "{\"c\":[
            {\"v\":\"" . $row['DateTimeRange'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}";
            } else {
                echo "{\"c\":[
            {\"v\":\"" . $row['DateTimeRange'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}, ";
            } 
        } 
        elseif ($dayFlag){
            if ($row_num == $total_rows){
                echo "{\"c\":[
            {\"v\":\"" . $row['Day'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}";
            } else {
                echo "{\"c\":[
            {\"v\":\"" . $row['Day'] . "\",\"f\":null},
            {\"v\":" . $row['Jobs'] . ",\"f\":null}]}, ";
            } 
        }
    }
    echo " ] }";
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
