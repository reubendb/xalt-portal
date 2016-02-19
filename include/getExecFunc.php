<?php
/*
 * Get Functions Called for given UUID.
 */
$uuid       = $_GET["uuid"];

try {
    include (__DIR__ ."/wrapper.php");
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT xf.function_name AS Function
        FROM xalt_function xf, join_link_function jlf, xalt_link xl
        WHERE xl.uuid = '$uuid' AND
        jlf.link_id = xl.link_id AND
        xf.func_id  = jlf.func_id
        ORDER BY Function;
    ";

    # print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Function Calls\",\"pattern\":\"\",\"type\":\"string\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    $objPath    = wrapper($row['ObjPath'], 80);
    $moduleName = isset($row['ModuleName']) ? $row['ModuleName'] : 'N/A';

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Function'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Function'] . "\",\"f\":null}
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
