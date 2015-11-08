<?php

$runId=$_GET["runId"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="
        SELECT xen.env_name AS EnvName, jre.env_value AS EnvValue 
        FROM join_run_env jre, xalt_env_name xen 
        WHERE 
        jre.run_id = '$runId' AND 
        xen.env_id = jre.env_id AND 
        jre.env_value IS NOT NULL
        ORDER BY EnvName;
    ;";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Environment Variable\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Value\",\"pattern\":\"\",\"type\":\"string\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    $envValue = wordwrap($row['EnvValue'], 80, '<br />', true);

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['EnvName'] . "\",\"f\":null},
    {\"v\":\"" . $envValue . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['EnvName'] . "\",\"f\":null},
    {\"v\":\"" . $envValue . "\",\"f\":null}
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
