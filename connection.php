<?php
$serverName = "uphmc-dc33";
$database = "phmc_reports";
$uid = "census";
$pass = "pass123";


$connection = [
"Database" => $database,
"Uid" => $uid,
"PWD" => $pass
];




$conn = sqlsrv_connect($serverName,$connection);
if(!$conn)
die(print_r(sqlsrv_errors(),true));

else // this for making sure its connected
echo 'connection established'; // this for making sure its connected
?>    

