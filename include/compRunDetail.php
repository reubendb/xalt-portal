<?php

$uuid=$_GET["uuid"];
$user=$_GET["user"];
$linkProgram=$_GET["linkProgram"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($linkProgram == 'gpp') { $linkProgram = 'g++'; }              # specail condition for g++ as ajax call reads + as special character

    $sql="SELECT xr.job_id AS JobId, xr.date AS Date,
        CONCAT(xr.num_cores,'     ',  xr.job_num_cores, '     ',num_nodes, '    ', num_threads) AS
        'Cores JobNumCores  Nodes Threads', 
        xr.account AS Account, xr.exit_code AS ExitCode 
        FROM xalt_run AS xr, xalt_link xl
        WHERE xr.user = '$user' AND 
        xr.syshost = xl.build_syshost = '$sysHost' AND
        xl.uuid = xr.uuid = '$uuid' 
        Order by Date desc;";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"JobId\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Run Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Cores JobNumCores  Nodes Threads\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Account\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"ExitCode\",\"pattern\":\"\",\"type\":\"string\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
    {\"v\":\"" . $row['Date'] . "\",\"f\":null},
    {\"v\":\"" . $row['Cores JobNumCores  Nodes Threads'] . "\",\"f\":null},
    {\"v\":\"" . $row['Account'] . "\",\"f\":null},
    {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
    {\"v\":\"" . $row['Date'] . "\",\"f\":null},
    {\"v\":\"" . $row['Cores JobNumCores  Nodes Threads'] . "\",\"f\":null},
    {\"v\":\"" . $row['Account'] . "\",\"f\":null},
    {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null}
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
