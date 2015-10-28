<?php

$user=$_GET["user"];
$objPath=$_GET["objPath"];

try {
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql="SELECT SUBSTRING_INDEX(xl.exec_path, '/' ,-1) AS Executable, 
        count(*) as Count
        FROM xalt_link xl 
        INNER JOIN 
        (
            SELECT distinct jlo.link_id 
            FROM join_link_object jlo 
            INNER JOIN xalt_object xo   ON (jlo.obj_id = xo.obj_id)
            WHERE 
            xo.object_path like CONCAT('%','$objPath', '%')
        ) 
        ka ON ka.link_id = xl.link_id 

        WHERE     
        xl.exec_path NOT LIKE '%.so' AND -- exec filter starts                
        xl.exec_path NOT LIKE '%.o' AND                                       
        xl.exec_path NOT LIKE '%.o.%' AND                                     
        xl.exec_path NOT LIKE '%.so.%' AND -- exec filter ends 
        xl.build_user = '$user'
        GROUP BY Executable 
        ORDER BY Count Desc;";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Executable\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
    {\"v\":" . $row['Count'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Executable'] . "\",\"f\":null},
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
