<?php 
/* Return list of syshost from xalt_link table
 *
 * */
try {

    include (__DIR__ ."/conn.php");
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT DISTINCT(build_syshost) AS syshost, count(build_syshost) as count 
        FROM xalt_link 
        GROUP BY syshost 
        ORDER BY count desc;
    ";

    $query = $conn->prepare($sql);
    $query->execute();

    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
	    echo '<option value='. $row['syshost'] . '>'.
		    $row['syshost'] .
		    '</option>';
    }
}
catch(PDOException $e) {
	echo "Error: " . $e->getMessage();
}
$conn = null;
?>

