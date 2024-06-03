<?php
require 'connection.php';
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

$sql = "SELECT pattrantype, COUNT(PK_psPatRegisters) as totalcensus_id 
        FROM rptCensus
        WHERE YEAR(datetimeadmitted) = $selected_year
        AND MONTH(datetimeadmitted) = $selected_month
        GROUP BY pattrantype";

$result = sqlsrv_query($conn, $sql);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true)); // Print any errors if query execution fails
}

// Check if there are results
if (sqlsrv_has_rows($result)) {
    // Initialize variables to hold total counts for each transaction type
    $opdCount = 0;
    $ipdCount = 0;
    $erCount = 0;

    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        if ($row["pattrantype"] == "O") {
            $opdCount = $row["totalcensus_id"];
        } elseif ($row["pattrantype"] == "I") {
            $ipdCount = $row["totalcensus_id"];
        } elseif ($row["pattrantype"] == "E") {
            $erCount = $row["totalcensus_id"];
        }
    }

    $totalTarget = 10000;

    $totalPatients = $opdCount + $ipdCount + $erCount;
    // Compare total patients with the target value
    if ($totalPatients >=  $totalTarget) {
        // Display total patients with green arrow
        echo "<div class='result' style='top:50px;left:85px;position: absolute; color:black;'>$totalPatients<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
    } else {
        // Display total patients with red arrow
        echo "<div class='result' style='top:50px;left:85px;position: absolute; color:black;'><span class='red-arrow' style='float: left;'>&#8595;</span>$totalPatients</div>";
    }
} else {
    echo "<p style='font-weight:bold;color:black; top:50px; position: absolute;'>No target set for the selected year and month</p>";
}

?>
