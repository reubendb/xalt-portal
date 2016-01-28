<?php
/*
 * Get list of users for a given linkProgram, sysHost and date range.
 * */
$sysHost     = $_GET["sysHost"];
$startDate   = $_GET["startDate"];
$endDate     = $_GET["endDate"];
$linkProgram = $_GET["linkProgram"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($linkProgram == 'gpp') { $linkProgram = 'g++'; }              # specail condition for g++ 
                                                                      # as ajax call reads + as special character
        $sql= "
        SELECT xl.build_user as Users, COUNT(xl.date) as Count,
            min(xl.date) as MinDate, max(xl.date) as MaxDate
            FROM xalt_link xl 
            WHERE xl.link_program = '$linkProgram' AND 
            xl.build_syshost = '$sysHost' AND
            xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
            GROUP BY Users 
            ORDER BY Count desc 
            ;";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Users\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Earliest_LinkDate\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Latest_LinkDate\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"} 
    ], 
    \"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $row['Users'] . "\",\"f\":null},
        {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
        {\"v\":" . $row['Count'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Users'] . "\",\"f\":null},
        {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
        {\"v\":" . $row['Count'] . ",\"f\":null}
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
