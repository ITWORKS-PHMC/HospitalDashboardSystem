<?php
$serverName = "uphmc-dc34";
$database = "phmc_reports";
$uid = "census";
$pass = "pass123";


$connection = [
"Database" => $database,
"Uid" => $uid,
"PWD" => $pass
];


$conn = sqlsrv_connect($serverName,$connection);
if (!$conn) {
    die("Connection failed: " . sqlsrv_errors());
}
?>    

