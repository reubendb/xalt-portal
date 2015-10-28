<?php
$objPath=$_GET["objPath"];
// $o="fftw/3.3.0.4";

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "SELECT xl.build_user as Users, count(*) as Count,
        MIN(xl.date) AS fromdate, MAX(xl.date) AS todate
        FROM xalt_link xl 
        INNER JOIN 
        (SELECT DISTINCT jlo.link_id 
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
    xl.exec_path NOT LIKE '%.so.%'  -- exec filter ends 
    group by Users
    ORDER BY Count Desc
    ;";

#    print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": 
        [
{\"id\":\"\",\"label\":\"User\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Count\",\"pattern\":\"\",\"type\":\"number\"}, 
{\"id\":\"\",\"label\":\"From Date\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"To Date\",\"pattern\":\"\",\"type\":\"string\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;
    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Users'] . "\",\"f\":null},
    {\"v\":" . $row['Count'] . ",\"f\":null},
    {\"v\":\"" . $row['fromdate'] . "\",\"f\":null},
    {\"v\":\"" . $row['todate'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Users'] . "\",\"f\":null},
    {\"v\":" . $row['Count'] . ",\"f\":null},
    {\"v\":\"" . $row['fromdate'] . "\",\"f\":null},
    {\"v\":\"" . $row['todate'] . "\",\"f\":null}
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
