<?php

$sysHost     = $_GET["sysHost"];
$startDate   = $_GET["startDate"];
$endDate     = $_GET["endDate"];
$linkProgram = $_GET["linkProgram"];
$user        = $_GET["user"];
$exec        = $_GET["exec"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($linkProgram == 'gpp') { $linkProgram = 'g++'; }              # specail condition for g++ as ajax call reads + as special character

        $sql="SELECT xl.uuid as Uuid, xl.date as Date,
        IF (
            (SELECT COUNT(*) 
            FROM xalt_run xr
            WHERE xr.uuid = xl.uuid) >= 1, 'true', 'false'
        ) as JobRun
        FROM xalt_link xl  
        WHERE xl.build_user = '$user' AND
        xl.build_syshost = '$sysHost' AND
        xl.link_program = '$linkProgram' AND
        xl.date BETWEEN '$startDate' AND '$endDate' AND
        xl.exec_path NOT LIKE '%.so' AND -- exec filter starts  
        xl.exec_path NOT LIKE '%.o' AND                         
        xl.exec_path NOT LIKE '%.o.%' AND                       
        xl.exec_path NOT LIKE '%.so.%' AND -- exec filter ends  
        SUBSTRING_INDEX(xl.exec_path, '/', -1) = '$exec'
        ORDER BY Date desc ;";


    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Uuid\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build Date\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Job Run[T/F]\",\"pattern\":\"\",\"type\":\"boolean\"} 
    ], 
    \"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null}
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
