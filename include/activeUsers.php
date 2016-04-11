<?php
/*  
 * \brief Get active users based on sysHost and given date range.
 *        X-axis caters for day-week-month distribution depending upon the daterange.
 * */

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

    switch(true) {
    case ($days > 30) :           # group by month 
        $dateFormat = " DATE_FORMAT(xr.date, '%b') AS Month ";
        $xl_dateFormat = " DATE_FORMAT(xl.date, '%b') AS Month ";
        $groupBy    = " GROUP BY Month, Year ";
        break;
    case ($days < 30 && $days > 7):    # group by week
        $dateFormat = " DATE_FORMAT(xr.date, '%u') AS Week ";
        $xl_dateFormat = " DATE_FORMAT(xl.date, '%u') AS Week ";
        $groupBy    = " GROUP BY Week, Year ";
        break;
    case ($days < 7) :            # group by day
        $dateFormat = " DATE_FORMAT(xr.date, '%d-%b') AS Day ";
        $xl_dateFormat = " DATE_FORMAT(xl.date, '%d-%b') AS Day ";
        $groupBy    = " GROUP BY Day, Year ";
        break;
    }

    $sql = "SELECT $dateFormat,  
        COUNT(DISTINCT xr.user) as RunUsers,
        date(min(xr.date)) as DateTimeRange,
        YEAR(xr.date) AS Year
        FROM xalt_run xr 
        WHERE xr.syshost = '$sysHost' AND 
        xr.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        $groupBy  
        ORDER BY Year desc, DateTimeRange ASC;
    ";

    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    $sql_xl = "
        SELECT $xl_dateFormat, 
        COUNT(DISTINCT xl.build_user) as BuildUsers,
        date(min(xl.date)) as DateTimeRange,
        YEAR(xl.date) AS Year
        FROM xalt_link xl 
        WHERE xl.build_syshost = '$sysHost' AND 
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        $groupBy
        ORDER BY Year desc,DateTimeRange ASC; 
    ";

    $query2 = $conn->prepare($sql_xl);
    $query2->execute();
    $result2 = $query2->fetchAll(PDO:: FETCH_ASSOC);

    echo "{\"cols\": [
{\"id\":\"\",\"label\":\"DateTimeRange\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Users Running Jobs\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Users Compiling Codes\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Average\",\"pattern\":\"\",\"type\":\"number\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;
$i = 0;
foreach($result as $row){
    $row_num++;
    $avg = 0;
    $avg = ($row['RunUsers'] + $result2[$i]['BuildUsers'] ) / 2;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['DateTimeRange'] . "\",\"f\":null},
    {\"v\":" . $row['RunUsers'] . ",\"f\":null},
    {\"v\":" . $result2[$i]['BuildUsers'] . ",\"f\":null},
    {\"v\":" . $avg . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['DateTimeRange'] . "\",\"f\":null},
    {\"v\":" . $row['RunUsers'] . ",\"f\":null},
    {\"v\":" . $result2[$i]['BuildUsers'] . ",\"f\":null},
    {\"v\":" . $avg . ",\"f\":null}
    ]}, ";
    } 
    $i++;
}
echo " ] }";
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
