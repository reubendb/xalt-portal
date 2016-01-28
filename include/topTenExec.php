<?php
/*
 * Get Top Ten Executables for given sysHost and date range. 
 */

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];

try {

    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT CASE 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'wrf' then 'WRF*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'chim' then 'CHIMERA*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'vasp' then 'VASP*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'namd' then 'NAMD*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'lmp' then 'LAMMPS*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'amber' then 'AMBER*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'charm' then 'CHARMM*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'enzo' then 'ENZO*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'gromacs' then 'GROMACS*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'cp2k' then 'CP2K*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'nwchem' then 'NWCHEM*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'ttmmd' then 'TTMMD*' 
        WHEN LOWER(xalt_run.exec_path)  REGEXP 'genasis' then 'GENASIS*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'engine_par' then 'VISIT*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'foam' then 'OPENFOAM*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'ph.x' then 'Q-ESPRESSO*' 
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'pw.x' then 'Q-ESPRESSO*'
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'gene_d' then 'GENE*'
        WHEN LOWER(SUBSTRING_INDEX(xalt_run.exec_path,'/',-1)) REGEXP 'abinit' then 'ABINIT*'
        ELSE SUBSTRING_INDEX(xalt_run.exec_path,'/',-1) END 
        AS execName, ROUND(SUM(run_time*num_cores/3600)) as totalcput, 
        COUNT(date) as n_jobs, COUNT(DISTINCT(user)) as n_users 
        FROM xalt_run 
        WHERE syshost = '$sysHost' 
        AND date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' 
        GROUP BY execName ORDER BY totalcput DESC 
        limit 10;
    ";

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    echo "{\"cols\":[
{\"id\":\"\",\"label\":\"execName\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"n_jobs\",\"pattern\":\"\",\"type\":\"number\"},
{\"id\":\"\",\"label\":\"totalcput\",\"pattern\":\"\",\"type\":\"number\"}
],
\"rows\": [ ";

        $total_rows = $query->rowCount();
        $row_num = 0;

        foreach($result as $row){
            $row_num++;
            if ($row_num == $total_rows){
                echo "{\"c\":[
            {\"v\":\"" . $row['execName'] . "\",\"f\":null},
            {\"v\":" . $row['n_jobs'] . ",\"f\":null},
            {\"v\":" . $row['totalcput'] . ",\"f\":null}
            ]}";
            } else {
                echo "{\"c\":[
            {\"v\":\"" . $row['execName'] . "\",\"f\":null},
            {\"v\":" . $row['n_jobs'] . ",\"f\":null},
            {\"v\":" . $row['totalcput'] . ",\"f\":null}
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
