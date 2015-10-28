<?php

$sysHost    = $_GET["sysHost"];
$uuid       = $_GET["uuid"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql=" SELECT xo.object_path AS ObjPath, xo.module_name as ModuleName, 
        xo.timestamp AS ObjectDate, xo.lib_type AS LibType 
        FROM xalt_object xo
        INNER JOIN 
        (SELECT distinct jlo.obj_id 
        FROM join_link_object jlo 
        INNER JOIN xalt_link xl ON (jlo.link_id = xl.link_id)
        WHERE xl.uuid = '$uuid' 

    ) ka on ka.obj_id = xo.obj_id
    WHERE xo.object_path NOT LIKE '%usr%'; 
    ";

    
/*
    $sql=" SELECT xo.object_path AS ObjPath, xo.module_name as ModuleName, 
        xl.build_user AS BuildUser, xl.exit_code AS ExitCode, 
        Date(xl.date) AS BuildDate, xl.link_program AS LinkProgram 
        FROM xalt_object xo, join_link_object jlo, xalt_link xl 
        WHERE xl.uuid = '$uuid' AND 
        jlo.link_id = xl.link_id AND 
        xo.obj_id = jlo.obj_id;
    ";
 */
    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"ObjectPath\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"ModuleName\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"ObjectDate\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"LibType\",\"pattern\":\"\",\"type\":\"string\"}
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['ObjPath'] . "\",\"f\":null},
    {\"v\":\"" . $row['ModuleName'] . "\",\"f\":null},
    {\"v\":\"" . $row['ObjectDate'] . "\",\"f\":null},
    {\"v\":\"" . $row['LibType'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['ObjPath'] . "\",\"f\":null},
    {\"v\":\"" . $row['ModuleName'] . "\",\"f\":null},
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
