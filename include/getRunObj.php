<?php
/*
 * Get Object/Library details for given runID at runtime.
 */
$runId       = $_GET["runId"];

try {
    include (__DIR__ ."/wrapper.php");
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql= "SELECT xo.object_path AS ObjPath, xo.module_name as ModuleName, 
        xo.timestamp AS ObjectDate, xo.lib_type AS LibType 
        FROM xalt_object xo
        INNER JOIN 
        (SELECT distinct jro.obj_id 
        FROM join_run_object jro 
        INNER JOIN xalt_run xr ON (jro.run_id = xr.run_id)
        WHERE xr.run_id = '$runId' 
    ) ka on ka.obj_id = xo.obj_id
    WHERE xo.object_path NOT LIKE '%usr%'
    ORDER BY xo.module_name desc;
    ";

    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Object Path\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Module Name\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Object Date\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Object Type\",\"pattern\":\"\",\"type\":\"string\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    $objPath    = wrapper($row['ObjPath'], 60);
    $moduleName = isset($row['ModuleName']) ? $row['ModuleName'] : 'N/A';

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $objPath . "\",\"f\":null},
    {\"v\":\"" . $moduleName . "\",\"f\":null},
    {\"v\":\"" . $row['ObjectDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['LibType'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $objPath . "\",\"f\":null},
    {\"v\":\"" . $moduleName . "\",\"f\":null},
    {\"v\":\"" . $row['ObjectDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['LibType'] . "\",\"f\":null}
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
