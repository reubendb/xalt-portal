<?php
/*
 * Get Top Ten Users using given resource for given sysHost and date range. 
 */

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT  xr.user as User,
        ROUND(SUM((xr.run_time/3600) * xr.num_cores)) AS TotalCPU, 
        count(distinct xr.job_id) as NumberOfJobs
        FROM xalt_run xr
        WHERE xr.syshost='$sysHost' AND 
        xr.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' 
        GROUP BY User 
        ORDER BY TotalCPU desc limit 10;
    ";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{\"cols\":[
{\"id\":\"\",\"label\":\"User\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"TotalCPU Hours\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"NumberOfJobs\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"NumberOfInstances\",\"pattern\":\"\",\"type\":\"number\"}
],
\"rows\": [ ";

        $total_rows = $query->rowCount();
        $row_num = 0;

        foreach($result as $row){
            $row_num++;

            # get NumberofInstances for given user
            $user = $row['User'];

            $sql = "
                SELECT COUNT(xl.link_id) as NumberOfInstance
                FROM xalt_link xl 
                WHERE xl.build_syshost = '$sysHost' AND 
                xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND  
                xl.build_user = '$user';
            ";

            $query = $conn->prepare($sql);
            $query->execute();
            $instances = $query->fetchAll(PDO:: FETCH_ASSOC);

            if ($row_num == $total_rows){
                echo "{\"c\":[
            {\"v\":\"" . $row['User'] . "\",\"f\":null},
            {\"v\":" . $row['TotalCPU'] . ",\"f\":null},
            {\"v\":" . $row['NumberOfJobs'] . ",\"f\":null},
            {\"v\":" . $instances[0]['NumberOfInstance'] . ",\"f\":null}
            ]}";
            } else {
                echo "{\"c\":[
            {\"v\":\"" . $row['User'] . "\",\"f\":null},
            {\"v\":" . $row['TotalCPU'] . ",\"f\":null},
            {\"v\":" . $row['NumberOfJobs'] . ",\"f\":null},
            {\"v\":" . $instances[0]['NumberOfInstance'] . ",\"f\":null}
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
