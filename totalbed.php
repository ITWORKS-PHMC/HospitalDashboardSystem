<?php
require 'connection.php';

// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total Inpatient Department (IPD) IDs for the selected year and month
$sql = "SELECT COUNT(PK_psPatRegisters) as totalcensus_id 
        FROM rptCensus
        WHERE YEAR(datetimeadmitted) = $selected_year 
        AND MONTH(datetimeadmitted) = $selected_month
        AND pattrantype = 'I'";

$result = sqlsrv_query($conn, $sql);

// Check if there are results
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
} elseif (sqlsrv_has_rows($result)) {
    // Fetch the total IPD count
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    $totalIPD = $row["totalcensus_id"];

//     // SQL query to get the target value for the selected year and month
//     $sql2 = "SELECT SUM(target_value) as totaltarget 
//              FROM dashboard_target 
//              WHERE target_type = 'IPD'
//              AND YEAR(target_date) = $selected_year 
//              AND MONTH(target_date) = $selected_month";
             
//     $result2 = sqlsrv_query($conn, $sql2);

//  if ($result2 === false) {
//         // Handle SQL error
//         die(print_r(sqlsrv_errors(), true));
//     } elseif (sqlsrv_has_rows($result2)) {
//         // Fetch the target value
//         $row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC);
//         $totalTarget = $row2["totaltarget"];
//         echo "<div class='result' style='top:70px;left: 75px;position: absolute; color:black;'>$totalIPD/$totalTarget</div>";
//     } else {
//         echo "No target set for the selected year and month";
//     }
// } else {
//     echo "No Inpatient Department (IPD) records for the selected year and month";
// 
}
echo "<div class='result' style='top:70px;left: 75px;position: absolute; color:black;'> $totalIPD</div>";
?>