<?php
/*
 * Get Executable Detail for: 
 * Query 1 - Given Object/Library Path.
 * Query 2 - Given ExecName.
 */
$sysHost    = $_GET["sysHost"];   
$startDate  = $_GET["startDate"]; 
$endDate    = $_GET["endDate"];   
$objPath    = $_GET["objPath"];   
$execName   = $_GET["execName"];   
$user       = $_GET["user"];
$query      = $_GET["query"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($query == 1) {        /* find exec for given objPath */
        $sql="
            SELECT SUBSTRING_INDEX(xl.exec_path, '/' ,-1) AS Executable, 
            min(xl.date) as MinDate,
            max(xl.date) as MaxDate,
            count(*) as Count
            FROM xalt_link xl 
            INNER JOIN 
            (
                SELECT distinct jlo.link_id 
                FROM join_link_object jlo 
                INNER JOIN xalt_object xo   ON (jlo.obj_id = xo.obj_id)
                WHERE 
                xo.sysHost='$sysHost' AND
                xo.object_path like CONCAT('%','$objPath', '%')
            ) 
            ka ON ka.link_id = xl.link_id 
            WHERE     
            xl.date BETWEEN '$startDate' AND '$endDate' AND
            xl.build_user = '$user'
            GROUP BY Executable 
            ORDER BY Count Desc;";
    } else if ($query == 2) {     /* find given execName */
        $sql="
            SELECT SUBSTRING_INDEX(xl.exec_path, '/' ,-1) AS Executable, 
            min(xl.date) as MinDate,
            max(xl.date) as MaxDate,
            count(*) as Count
            FROM xalt_link xl 
            WHERE     
            xl.date BETWEEN '$startDate' AND '$endDate' AND
            xl.build_user = '$user' AND
            xl.build_sysHost='$sysHost' AND
            xl.exec_path like CONCAT('%','$execName', '%')
            GROUP BY Executable 
            ORDER BY Count Desc;";
    }

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Executable\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"LinkDate_Oldest\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"LinkDate_Latest\",\"pattern\":\"\",\"type\":\"string\"}, 
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
        {\"v\":\"" . $row['MinDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['MaxDate'] . "\",\"f\":null},
        {\"v\":" . $row['Count'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
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
