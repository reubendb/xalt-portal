<?php

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$module     = $_GET["module"];
$version    = $_GET["version"];
$user       = $_GET["user"];
$exec       = $_GET["exec"];
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



    $sql= "SELECT DISTINCT xl.uuid as Uuid,                                
        xl.date as Date,                                        
        xl.link_program as LinkProgram,                         
        xl.exit_code as ExitCode,
        IF (
            (SELECT count(*) 
            from xalt_run xr1 
            where xr1.uuid = xl.uuid) >= 1, 'true', 'false'
        ) AS JobRun 
        FROM xalt_link xl 
        INNER JOIN (
            SELECT DISTINCT jlo.link_id 
            FROM join_link_object jlo 
            INNER JOIN xalt_object xo ON (jlo.obj_id = xo.obj_id)
            WHERE 
            xo.syshost='$sysHost' AND 
            xo.module_name LIKE '$moduleName' AND
            xo.timestamp BETWEEN '$startDate' AND '$endDate'
        ) 
        ka ON ka.link_id = xl.link_id  AND
        xl.exec_path NOT LIKE '%.so' AND -- exec filter starts                
        xl.exec_path NOT LIKE '%.o' AND                                       
        xl.exec_path NOT LIKE '%.o.%' AND                                     
        xl.exec_path NOT LIKE '%.so.%' AND -- exec filter ends 
        xl.build_user like CONCAT ('%', '$user', '%')  AND                            
        SUBSTRING_INDEX(xl.exec_path, '/', -1) = '$exec' 
        ORDER BY Date desc
        ;";


#    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);
#    print_r($result);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Unique Id\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build Date\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Link Program\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Exit Code\",\"pattern\":\"\",\"type\":\"string\"}, 
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
        {\"v\":\"" . $row['LinkProgram'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":\"" . $row['LinkProgram'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
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
