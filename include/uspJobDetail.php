<?php
/*
 * Get Job Details for given userID, sysHost and date range. 
 */
$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"];
$uuid       = $_GET["uuid"];

try {
    include (__DIR__ ."/wrapper.php");
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="
        SELECT xr.run_id AS RunId, xr.job_id AS JobId, xr.date AS Date,
        CONCAT(xr.num_cores,'     ',  xr.job_num_cores, '     ',num_nodes, '    ', num_threads) AS
        'Cores JobNumCores  Nodes Threads', 
        xr.account AS Account, xr.exec_type AS ExecType, xr.run_time AS RunTime,
        xr.exit_code AS ExitCode,
        xr.user AS RunUser, 
        xr.cwd AS Cwd 
        FROM xalt_run AS xr
        WHERE xr.uuid = '$uuid' AND
        xr.user = '$userId' AND
        xr.syshost = '$sysHost' AND
        xr.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY Job_ID
        ORDER BY Date DESC;
    ";

#    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"RunId\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"JobId\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Run Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"nC-nJC-nN-nT\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Account\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Exec Type\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Run Time (sec)\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"ExitCode\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Run User\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"CurrentWorkingDir\",\"pattern\":\"\",\"type\":\"string\"} 
], 
\"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;
        $cwd= wrapper($row['Cwd'], 45);

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $row['RunId'] . "\",\"f\":null},
        {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":\"" . $row['Cores JobNumCores  Nodes Threads'] . "\",\"f\":null},
        {\"v\":\"" . $row['Account'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExecType'] . "\",\"f\":null},
        {\"v\":\"" . $row['RunTime'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":\"" . $row['RunUser'] . "\",\"f\":null},
        {\"v\":\"" . $cwd . "\",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['RunId'] . "\",\"f\":null},
        {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":\"" . $row['Cores JobNumCores  Nodes Threads'] . "\",\"f\":null},
        {\"v\":\"" . $row['Account'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExecType'] . "\",\"f\":null},
        {\"v\":\"" . $row['RunTime'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":\"" . $row['RunUser'] . "\",\"f\":null},
        {\"v\":\"" . $cwd . "\",\"f\":null}
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
