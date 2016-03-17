<?php
/*
 * Get Executable Details for given ModuleName, sysHost, executableName and date range.
 */
$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$module     = $_GET["module"];
$version    = $_GET["version"];
$user       = $_GET["user"];
$exec       = $_GET["exec"];
$page       = $_GET["page"];
$totalNumRec= $_GET["totalNumRec"];
$moduleName = '';
$rec_limit  = 10;
$offset     = 0; 

try {
    include (__DIR__ ."/wrapper.php");
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

    if ($page == 0){
        $offset = 0;
    } else {
        $offset = $rec_limit * $page;
        if (($totalNumRec - $offset ) < 10 ){
            $lastPage = true;
        }
    }

    /* get Executable details (run/compile both) irrespective of build user) */

    $sql= "
        SELECT DISTINCT xl.uuid as Uuid,                                
        xl.date as Date,                                        
        xl.link_program as LinkProgram,                         
        xl.exit_code as ExitCode,
        xl.build_user as BuildUser,
        xl.exec_path as ExecPath
        FROM xalt_link xl ,  join_link_object jlo , xalt_object xo 
        WHERE jlo.obj_id = xo.obj_id AND
        xl.link_id = jlo.link_id AND
        xo.syshost='$sysHost' AND 
        xo.module_name LIKE '$moduleName' AND
        xl.build_user like CONCAT('%', '$user', '%') AND
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND
        SUBSTRING_INDEX(xl.exec_path, '/', -1) = '$exec' 
        ORDER BY Date desc
        LIMIT $offset, $rec_limit
        ;";

    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
    {\"id\":\"\",\"label\":\"Executable Path\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build Date\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Link Program\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Exit Code\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Build User\",\"pattern\":\"\",\"type\":\"string\"}, 
    {\"id\":\"\",\"label\":\"Job Run[T/F]\",\"pattern\":\"\",\"type\":\"boolean\"}, 
    {\"id\":\"\",\"label\":\"Unique Id\",\"pattern\":\"\",\"type\":\"string\"}
    ], 
    \"rows\": [ ";

    $row_num = 0;

    // Include JobRun to the result array 
    for($i=0; $i < sizeof($result); $i++){
        $uuid = '';                           
        $uuid = $result[$i]['Uuid'];
        $sql = "SELECT 
            IF ((SELECT COUNT(*) FROM xalt_run 
            WHERE uuid = '$uuid' >= 1), 'true', 'false') AS JobRun ";

        $q = $conn->prepare($sql);
        $q->execute();
        $r = $q->fetchAll(PDO:: FETCH_ASSOC);
        $result[$i]['JobRun'] = $r[0]['JobRun'];
    }

    // Control Process for paging Starts ++ //
    if ($lastPage) {
        $total_rows = $query->rowCount();
    }

    // reiterate same 10 records for given #pages to make jsonTableData complete
    if ($lastPage) {
        for($i=0; $i < $totalNumRec - $total_rows; $i++){
            $result[$total_rows + $i] = $result[$i];
        }               
    } else {
        for($i=0; $i < $totalNumRec - $rec_limit; $i++){
            $result[$rec_limit + $i] = $result[$i];
        }
    }

    $total_rows = sizeof($result);

    // Control Process for paging Ends -- //

    foreach($result as $row){
        $row_num++;
        $execPath = wrapper($row['ExecPath'],45);

        if ($row_num == $total_rows){
            echo "{\"c\":[
        {\"v\":\"" . $execPath . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
        {\"v\":\"" . $row['LinkProgram'] . "\",\"f\":null},
        {\"v\":\"" . $row['ExitCode'] . "\",\"f\":null},
        {\"v\":\"" . $row['BuildUser'] . "\",\"f\":null},
        {\"v\":" . $row['JobRun'] . ",\"f\":null},
        {\"v\":\"" . $row['Uuid'] . "\",\"f\":null}
        ]}";
        } else {
            echo "{\"c\":[
        {\"v\":\"" . $execPath . "\",\"f\":null},
        {\"v\":\"" . $row['Date'] . "\",\"f\":null},
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
