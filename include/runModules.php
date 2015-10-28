<?php

$sys=$_GET["sys"];

function originalQuery($currYear) {
	return "(
		SELECT substring_index(xo.module_name, '/', 1) as Module, 
		COUNT(date) AS ". $currYear ."Count, 
		Year(date) AS ". $currYear ."Year 
		FROM xalt_run xr, join_run_object jro, xalt_object xo 
		WHERE xr.syshost='darter' AND 
		xo.module_name IS NOT NULL AND 
		xr.run_id = jro.run_id AND 
		jro.obj_id = xo.obj_id 
		GROUP BY Module, ". $currYear ."Year
	        HAVING ". $currYear ."Year = ". $currYear .	
		" ORDER BY Module 
		) as q$currYear ";
}

 try {

	include (__DIR__ ."/conn.php");


	$conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * " . "FROM " . originalQuery(2015) . " JOIN" 
		. originalQuery(2014) . 
		"ON q2015.Module = q2014.Module " . " 
		ORDER by 2015Count Desc, 2014Count Desc, q2015.Module LIMIT 10";


	$query = $conn->prepare($sql);
	$query->execute();

	$result = $query->fetchAll(PDO:: FETCH_ASSOC);

#	print_r($result);

	echo "{ \"cols\": [
 {\"id\":\"\",\"label\":\"Module\",\"pattern\":\"\",\"type\":\"string\"}, 
 {\"id\":\"\",\"label\":\"2015\",\"pattern\":\"\",\"type\":\"number\"}, 
 {\"id\":\"\",\"label\":\"2014\",\"pattern\":\"\",\"type\":\"number\"} 
 			], 
	\"rows\": [ ";

	$total_rows = $query->rowCount();
        $row_num = 0;
	
	foreach($result as $row){
		$row_num++;

		if ($row_num == $total_rows){
			echo "{\"c\":[
		{\"v\":\"" . $row['Module'] . "\",\"f\":null},
		{\"v\":" . $row['2015Count'] . ",\"f\":null},
		{\"v\":" . $row['2014Count'] . ",\"f\":null}
		]}";
		} else {
			echo "{\"c\":[
		{\"v\":\"" . $row['Module'] . "\",\"f\":null},
		{\"v\":" . $row['2015Count'] . ",\"f\":null},
		{\"v\":" . $row['2014Count'] . ",\"f\":null}
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
