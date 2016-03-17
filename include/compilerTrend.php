<?php
/* 
 * Get totatSUs, though this query may not given the correct picture.
 */ 
$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];
try {

    print_r($sysHost, $startDate, $endDate);
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="
        select xl.link_program as Compiler,
        sum(case when month(xl.date) = 1 then 1 else 0 end) as Jan,
        sum(case when month(xl.date) = 2 then 1 else 0 end) as Feb,
        sum(case when month(xl.date) = 3 then 1 else 0 end) as Mar,
        sum(case when month(xl.date) = 4 then 1 else 0 end) as Apr,
        sum(case when month(xl.date) = 5 then 1 else 0 end) as May,
        sum(case when month(xl.date) = 6 then 1 else 0 end) as Jun,
        sum(case when month(xl.date) = 7 then 1 else 0 end) as Jul,
        sum(case when month(xl.date) = 8 then 1 else 0 end) as Aug,
        sum(case when month(xl.date) = 9 then 1 else 0 end) as Sep,
        sum(case when month(xl.date) = 10 then 1 else 0 end) as Oct,
        sum(case when month(xl.date) = 11 then 1 else 0 end) as Nov,
        sum(case when month(xl.date) = 12 then 1 else 0 end) as December
        from xalt_link xl
        WHERE xl.build_syshost='$sysHost' AND 
        xl.link_program IS NOT NULL AND 
        xl.link_program NOT like '' AND 
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        group by compiler
        order by compiler;
    ";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"Compiler\",\"pattern\":\"\",\"type\":\"string\"}, 
{\"id\":\"\",\"label\":\"Jan\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Feb\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Mar\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Apr\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"May\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Jun\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Jul\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Aug\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Sep\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Oct\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Nov\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"Dec\",\"pattern\":\"\",\"type\":\"number\"} 
], 
\"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['Compiler'] . "\",\"f\":null},
    {\"v\":" . $row['Jan'] . ",\"f\":null},
    {\"v\":" . $row['Feb'] . ",\"f\":null},
    {\"v\":" . $row['Mar'] . ",\"f\":null},
    {\"v\":" . $row['Apr'] . ",\"f\":null},
    {\"v\":" . $row['May'] . ",\"f\":null},
    {\"v\":" . $row['Jun'] . ",\"f\":null},
    {\"v\":" . $row['Jul'] . ",\"f\":null},
    {\"v\":" . $row['Aug'] . ",\"f\":null},
    {\"v\":" . $row['Sep'] . ",\"f\":null},
    {\"v\":" . $row['Oct'] . ",\"f\":null},
    {\"v\":" . $row['Nov'] . ",\"f\":null},
    {\"v\":" . $row['December'] . ",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['Compiler'] . "\",\"f\":null},
    {\"v\":" . $row['Jan'] . ",\"f\":null},
    {\"v\":" . $row['Feb'] . ",\"f\":null},
    {\"v\":" . $row['Mar'] . ",\"f\":null},
    {\"v\":" . $row['Apr'] . ",\"f\":null},
    {\"v\":" . $row['May'] . ",\"f\":null},
    {\"v\":" . $row['Jun'] . ",\"f\":null},
    {\"v\":" . $row['Jul'] . ",\"f\":null},
    {\"v\":" . $row['Aug'] . ",\"f\":null},
    {\"v\":" . $row['Sep'] . ",\"f\":null},
    {\"v\":" . $row['Oct'] . ",\"f\":null},
    {\"v\":" . $row['Nov'] . ",\"f\":null},
    {\"v\":" . $row['December'] . ",\"f\":null}
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
