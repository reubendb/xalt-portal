<?php
/*
 * Get Executable Detail for given userID, sysHost and date range. 
 */
$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"];
$exec       = $_GET["exec"];

try {
    include (__DIR__ ."/conn.php");
    include (__DIR__ ."/wordwrap.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="
        SELECT 
        xr.exec_path as ExecPath,
        xr.job_id as JobId,
        xr.uuid as Uuid,
        xr.date as RunDate,
        xl.build_user as BuildUser,
        xl.link_program as LinkProgram,
        xl.date as BuildDate
        FROM xalt_run xr
        LEFT JOIN xalt_link xl ON (xr.uuid = xl.uuid)
        WHERE
        substring_index(xr.exec_path, '/', '-1') = '$exec' AND
        xr.syshost = '$sysHost' AND
        xr.user like CONCAT ('%','$userId', '%') AND
        xr.date BETWEEN '$startDate' AND '$endDate';
    ";

#    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Executable Path\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Job Id\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Run Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Build User\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Link Program\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Build Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Unique Id\",\"pattern\":\"\",\"type\":\"string\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    $execPath    = wrapper($row['ExecPath']);

    $uuid        = isset($row['Uuid']) ? $row['Uuid'] : 'N/A';
    $buildUser   = isset($row['BuildUser']) ? $row['BuildUser'] : 'N/A';
    $linkProgram = isset($row['LinkProgram']) ? $row['LinkProgram'] : 'N/A';
    $buildDate   = isset($row['BuildDate']) ? $row['BuildDate'] : 'N/A';

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $execPath . "\",\"f\":null},
    {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
    {\"v\":\"" . $row['RunDate'] . "\",\"f\":null},
    {\"v\":\"" . $buildUser . "\",\"f\":null},
    {\"v\":\"" . $linkProgram . "\",\"f\":null},
    {\"v\":\"" . $buildDate . "\",\"f\":null},
    {\"v\":\"" . $uuid . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $execPath . "\",\"f\":null},
    {\"v\":\"" . $row['JobId'] . "\",\"f\":null},
    {\"v\":\"" . $row['RunDate'] . "\",\"f\":null},
    {\"v\":\"" . $buildUser . "\",\"f\":null},
    {\"v\":\"" . $linkProgram . "\",\"f\":null},
    {\"v\":\"" . $buildDate . "\",\"f\":null},
    {\"v\":\"" . $uuid . "\",\"f\":null}
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
