<?php
/*
 * Get List of users for given sysHost, ModuleName and date range. 
 */

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$module     = $_GET["module"];
$version    = $_GET["version"];
$moduleName = '';


try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch($version) {
    case $module:      # special case for this PAIN IN THE NECK bugger # 
        $moduleName = "%" . $module . "%";
        break;
    case "":           # in case no version #
        $moduleName = "%" . $module . "%";
        break;
    case !"":          # rest of the cases #
        $moduleName = "%" . $module . "/" . $version . "%";
        break;
    }


    $sql= "SELECT xl.build_user as Users, count(*) as Count,
        min(xl.date) as minDate, max(xl.date) as maxDate
        FROM xalt_link xl 
        INNER JOIN (
            SELECT DISTINCT jlo.link_id 
            FROM join_link_object jlo 
            INNER JOIN xalt_object xo ON (jlo.obj_id = xo.obj_id)
            WHERE xo.syshost='$sysHost' AND 
            xo.module_name LIKE '$moduleName' 
        ) 
        ka ON ka.link_id = xl.link_id 
        WHERE
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY Users
        ORDER BY Count Desc
        ;";

  #        print_r($sql);

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
