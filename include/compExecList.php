<?php

$sysHost     = $_GET["sysHost"];
$startDate   = $_GET["startDate"];
$endDate     = $_GET["endDate"];
$user        = $_GET["user"];
$linkProgram = $_GET["linkProgram"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($linkProgram == 'gpp') { $linkProgram = 'g++'; }              # specail condition for g++ as ajax call reads + as special character

    $sql="SELECT SUBSTRING_INDEX(xl.exec_path, '/', -1) as Executable, 
    COUNT(xl.date) as Count 
    FROM xalt_link xl  
    WHERE xl.build_syshost = '$sysHost' AND 
    xl.link_program = '$linkProgram' AND 
    xl.build_user = '$user' AND
    xl.exec_path NOT LIKE '%.so' AND -- exec filter starts  
    xl.exec_path NOT LIKE '%.o' AND                         
    xl.exec_path NOT LIKE '%.o.%' AND                       
    xl.exec_path NOT LIKE '%.so.%' AND -- exec filter ends  
    xl.date BETWEEN '$startDate' AND '$endDate'
    GROUP BY Executable 
    ORDER BY Count desc;";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Executable\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"} 
    ], 
    \"rows\": [ ";

    $total_rows = $query->rowCount();
    $row_num = 0;

    foreach($result as $row){
        $row_num++;

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
        {\"v\":" . $row['Count'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
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
