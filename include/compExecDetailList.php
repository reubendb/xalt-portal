<?php
/*
 * Get compiler executable details for given user, sysHost , linkProgram and date range. 
 * */

$sysHost     = $_GET["sysHost"];
$startDate   = $_GET["startDate"];
$endDate     = $_GET["endDate"];
$linkProgram = $_GET["linkProgram"];
$user        = $_GET["user"];
$exec        = $_GET["exec"];

try {
    include (__DIR__ ."/wrapper.php");
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($linkProgram == 'gpp') { $linkProgram = 'g++'; }              # specail condition for g++ as ajax call reads + as special character

        $sql="
        SELECT
        xl.exec_path as ExecPath,
        xl.date as BuildDate,
        xl.link_program as LinkProgram,
        xl.exit_code as ExitCode,
        xl.build_user as BuildUser,
        IF (
            (SELECT COUNT(*) 
            FROM xalt_run xr
            WHERE xr.uuid = xl.uuid) >= 1, 'true', 'false'
        ) as JobRun,
        xl.uuid as Uuid
        FROM xalt_link xl  
        WHERE xl.build_user = '$user' AND
        xl.build_syshost = '$sysHost' AND
        xl.link_program = '$linkProgram' AND
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND
        SUBSTRING_INDEX(xl.exec_path, '/', -1) = '$exec'
        ORDER BY Date desc ;";


    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Executable Path\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build Date\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Link Program\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"ExitCode\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build User\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Job Run[T/F]\",\"pattern\":\"\",\"type\":\"boolean\"}, 
    {\"id\":\"\",\"label\":\"Uuid\",\"pattern\":\"\",\"type\":\"string\"} 
    ], 
    \"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;
        $execPath = wrapper($row['ExecPath'], 45);

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $execPath . "\",\"f\":null},
        {\"v\":\"" . $row['BuildDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['LinkProgram'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":\"" . $row['BuildUser'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null},
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $execPath . "\",\"f\":null},
        {\"v\":\"" . $row['BuildDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['LinkProgram'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":\"" . $row['BuildUser'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null},
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null}
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
