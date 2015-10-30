<?php

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

    if ($q == 0) {        /* if called from xalt_usp.html  *User Software Provenance*  */ 
        $sql="
            SELECT CASE                                                       
            WHEN substring_index(xo.module_name, '/', 1) like '%-%'           
            then SUBSTRING_INDEX(SUBSTRING_INDEX(xo.module_name,'/',1), '-',1)
            WHEN substring_index(xo.module_name, '/', 1) NOT like '%-%'       
            then SUBSTRING_INDEX(xo.module_name,'/',1)                        
            WHEN substring_index(xo.module_name, '/', 2) = ''                 
            then xo.module_name                                               
            ELSE xo.module_name END AS Modules,                               
            COUNT(xo.timestamp) as Count                                           
            FROM xalt_object xo           
            INNER JOIN 
            (SELECT distinct jlo.obj_id 
            FROM join_link_object jlo 
            INNER JOIN xalt_link xl   ON (jlo.link_id = xl.link_id)
            WHERE 
            xl.build_user like CONCAT('%','$userId','%') AND
            xl.build_syshost='$sysHost' AND
            xl.exec_path NOT LIKE '%.so' AND -- exec filter starts 
            xl.exec_path NOT LIKE '%.o' AND 
            xl.exec_path NOT LIKE '%.o.%' AND 
            xl.exec_path NOT LIKE '%.so.%' -- exec filter ends
        ) 
        ka ON ka.obj_id = xo.obj_id
        WHERE xo.syshost='$sysHost' AND                             
        xo.module_name IS NOT NULL AND
        xo.timestamp BETWEEN '$startDate' AND '$endDate' 
        GROUP BY Modules                                                  
        ORDER BY count desc, Modules
        ;";

        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}";

    } else if ($q == 1) {  /* if called from xalt_usage.html */

        $sql="
            SELECT DISTINCT SUBSTRING_INDEX(xo.module_name,'/',1) AS Modules,
                COUNT(distinct ka.link_id) as Count
                FROM xalt_object xo           
                INNER JOIN 
                (SELECT distinct jlo.obj_id, jlo.link_id 
                FROM join_link_object jlo 
                INNER JOIN xalt_link xl   ON (jlo.link_id = xl.link_id)
                WHERE 
                xl.build_syshost='$sysHost' AND
                xl.date BETWEEN  '$startDate' AND '$endDate'
            ) 
            ka ON ka.obj_id = xo.obj_id
            WHERE xo.syshost='$sysHost' AND                             
            xo.module_name IS NOT NULL
            GROUP BY Modules                                             
            ORDER BY Modules
            ";
/*        $sql="
            SELECT CASE                                                       
            WHEN substring_index(xo.module_name, '/', 1) like '%-%'           
            then SUBSTRING_INDEX(SUBSTRING_INDEX(xo.module_name,'/',1), '-',1)
            WHEN substring_index(xo.module_name, '/', 1) NOT like '%-%'       
            then SUBSTRING_INDEX(xo.module_name,'/',1)                        
            WHEN substring_index(xo.module_name, '/', 2) = ''                 
            then xo.module_name                                               
            ELSE xo.module_name END AS Modules,                               
            COUNT(xo.timestamp) as Count                                           
            FROM xalt_object xo           
            WHERE xo.syshost='$sysHost' AND                             
            xo.module_name IS NOT NULL AND
            xo.timestamp BETWEEN '$startDate' AND '$endDate'
            GROUP BY Modules                                                  
            ORDER BY count desc, Modules
            ;";
 */
        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}";

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
                xl.date BETWEEN  '$startDate' AND '$endDate'
            ) 
            ka ON ka.obj_id = xo.obj_id
            WHERE xo.syshost='$sysHost' AND                             
            SUBSTRING_INDEX(xo.module_name,'/',1) = '$module'
            GROUP BY Modules, Versions                                             
            ORDER BY Modules
            ";
/*
        $sql="
            SELECT                                                          
            SUBSTRING_INDEX(xo.module_name,'/',1) as Modules,                 
            SUBSTRING_INDEX(xo.module_name,'/',-1) as Versions,               
            MIN(DATE(xo.timestamp)) as MinDate,                               
            MAX(DATE(xo.timestamp)) as MaxDate,                               
            count(2) as Count                                                 
            FROM xalt_object xo           
            WHERE xo.syshost='$sysHost' AND                             
            SUBSTRING_INDEX(SUBSTRING_INDEX(xo.module_name,'/',1), '-',1) = '$module' AND
            xo.timestamp BETWEEN '$startDate' AND '$endDate'
            GROUP BY Modules, Versions                                        
            ORDER BY Modules, Count Desc
            ;";
    {\"id\":\"\",\"label\":\"MinDate\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"MaxDate\",\"pattern\":\"\",\"type\":\"string\"}, 
 */
        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Versions\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}
    ";

    } else if ($q == 3) {      /* Go deep to look for versions  xalt_usp.html */

        $sql="
            SELECT                                                       
            SUBSTRING_INDEX(xo.module_name,'/',1) as Modules,                 
            SUBSTRING_INDEX(xo.module_name,'/',-1) as Versions,               
            MIN(DATE(xo.timestamp)) as MinDate,                               
            MAX(DATE(xo.timestamp)) as MaxDate,                               
            count(2) as Count                                                 
            FROM xalt_object xo           
            INNER JOIN 
            (SELECT distinct jlo.obj_id 
            FROM join_link_object jlo 
            INNER JOIN xalt_link xl   ON (jlo.link_id = xl.link_id)
            WHERE 
            xl.build_user like CONCAT('%','$userId','%') AND
            xl.build_syshost='$sysHost' AND
            xl.exec_path NOT LIKE '%.so' AND -- exec filter starts 
            xl.exec_path NOT LIKE '%.o' AND 
            xl.exec_path NOT LIKE '%.o.%' AND 
            xl.exec_path NOT LIKE '%.so.%' -- exec filter ends
        ) 
        ka ON ka.obj_id = xo.obj_id
        WHERE xo.syshost='$sysHost' AND                             
        SUBSTRING_INDEX(SUBSTRING_INDEX(xo.module_name,'/',1), '-',1) = '$module' AND
        xo.timestamp BETWEEN '$startDate' AND '$endDate' 
        GROUP BY Modules, Versions                                                  
        ORDER BY Modules, Count desc
        ;";

        $columns = "
    {\"id\":\"\",\"label\":\"Modules\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Versions\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"MinDate\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"MaxDate\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}";

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

            if ($q == 1 || $q == 0) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null}
            ]}";
            } else if ($q ==2 || $q == 3 ) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":\"" . $row['Versions'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null}
            ]}";
            }

        } else {
            if ($q == 1 || $q == 0) {
                echo "{\"c\":[
            {\"v\":\"" . $row['Modules'] . "\",\"f\":null},
            {\"v\":" . $row['Count'] . ",\"f\":null}
            ]}, ";
            } else if ($q == 2 || $q == 3) {
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


