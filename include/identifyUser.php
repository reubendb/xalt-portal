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
$query      = $_GET["query"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($query == 1) {
        $sql= "SELECT xl.build_user as Users, count(*) as Count,
            min(xl.date) as minDate, max(xl.date) as maxDate
            FROM xalt_link xl 
            INNER JOIN (
                SELECT DISTINCT jlo.link_id 
                FROM join_link_object jlo 
                INNER JOIN xalt_object xo ON (jlo.obj_id = xo.obj_id)
                WHERE xo.syshost='$sysHost' AND 
                xo.object_path LIKE CONCAT('%', '$objPath','%')
            ) 
            ka ON ka.link_id = xl.link_id 
            WHERE
            xl.date BETWEEN '$startDate' AND '$endDate'
            GROUP BY Users
            ORDER BY Count Desc;
        ";
    } else if ($query == 2) {
        $sql= "SELECT xl.build_user as Users, count(*) as Count,
            min(xl.date) as minDate, max(xl.date) as maxDate
            FROM xalt_link xl 
            WHERE
            xl.date BETWEEN '$startDate' AND '$endDate' AND
            xl.exec_path LIKE CONCAT('%', '$execName','%') AND
            xl.build_syshost='$sysHost' 
            GROUP BY Users
            ORDER BY Count Desc;
        "; 
    }
    #    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": 
        [
    {\"id\":\"\",\"label\":\"User\",\"pattern\":\"\",\"type\":\"string\"}, 
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
        {\"v\":\"" . $row['minDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['maxDate'] . "\",\"f\":null},
        {\"v\":" . $row['Count'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Users'] . "\",\"f\":null},
        {\"v\":\"" . $row['minDate'] . "\",\"f\":null},
        {\"v\":\"" . $row['maxDate'] . "\",\"f\":null},
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
