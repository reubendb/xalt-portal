<?php
/*
 * Get Modules for: 
 * Query 1 - Given Syshost, date range.
 * Query 2 - Given Module, sysHost, date Range.
     */
$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];
$q         = $_GET["query"];
$module    = $_GET["module"];
$userId    = $_GET["userId"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $columns = '';

    if ($q == 1) {  /* if called from xalt_usage.html */

        $sql="
            SELECT DISTINCT SUBSTRING_INDEX(xo.module_name,'/',1) as Modules, 
            COUNT(DISTINCT xl.link_id) as Count, 
            COUNT(DISTINCT xl.build_user) as UniqueUser 
            FROM xalt_object xo, join_link_object jlo, xalt_link xl 
            WHERE jlo.link_id = xl.link_id AND 
            xl.build_syshost='$sysHost' AND
            xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND 
            jlo.obj_id = xo.obj_id AND 
            xo.syshost = '$sysHost' AND
            xo.module_name IS NOT NULL
            GROUP BY Modules
            ORDER BY Modules;
        ";

        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"},
    {\"id\":\"\",\"label\":\"UniqueUser\",\"pattern\":\"\",\"type\":\"number\"}
    ";

    } else if ($q == 2) {      /* Go deep to look for versions xalt_usage.html */

        $sql="
            SELECT DISTINCT SUBSTRING_INDEX(xo.module_name,'/',1) AS Modules,
                SUBSTRING_INDEX(xo.module_name,'/',-1) as Versions,
                COUNT(distinct ka.link_id) as Count
                FROM xalt_object xo           
                INNER JOIN 
                (SELECT distinct jlo.obj_id, jlo.link_id 
                FROM join_link_object jlo 
                INNER JOIN xalt_link xl   ON (jlo.link_id = xl.link_id)
                WHERE 
                xl.build_syshost='$sysHost' AND
                xl.date BETWEEN  '$startDate 00:00:00' AND '$endDate 23:59:59'
            ) 
            ka ON ka.obj_id = xo.obj_id
            WHERE xo.syshost='$sysHost' AND                             
            SUBSTRING_INDEX(xo.module_name,'/',1) = '$module'
            GROUP BY Modules, Versions                                             
            ORDER BY Modules
            ";

        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Versions\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}
    ";
    } 


    #    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    #print_r($result);


    $total_rows = $query->rowCount();
    $row_num = 0;

    echo "{ \"cols\": [$columns], \"rows\": [ ";

    foreach($result as $row){
        $row_num++;

        if ($row_num == $total_rows){

            if ($q == 1) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null},
            {\"v\":" . $row['UniqueUser'] . ",\"f\":null}
            ]}";
            } else if ($q ==2) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":\"" . $row['Versions'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null}
            ]}";
            }

        } else {
            if ($q == 1) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null},
            {\"v\":" . $row['UniqueUser'] . ",\"f\":null}
            ]}, ";
            } else if ($q == 2) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":\"" . $row['Versions'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null}
            ]}, ";
            } 
        }

    }
    echo " ] }";

}


catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>


