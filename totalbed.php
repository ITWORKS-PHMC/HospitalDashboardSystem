<?php
require 'connection.php';

// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total Inpatient Department (IPD) IDs for the selected year and month
$sql = "SELECT COUNT(census_transaction_id) as totalcensus_id 
        FROM dashboard_database 
        WHERE YEAR(census_date_admitted) = $selected_year 
        AND MONTH(census_date_admitted) = $selected_month
        AND census_transaction_type = 'I'";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch the total IPD count
    $row = $result->fetch_assoc();
    $totalIPD = $row["totalcensus_id"];

    // SQL query to get the target value for the selected year and month
    $sql2 = "SELECT SUM(target_value) as totaltarget 
             FROM dashboard_target 
             WHERE target_type = 'IPD'";
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totaltarget"];
        echo "<div class='result'>$totalIPD/$totalTarget</div>";
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No Inpatient Department (IPD) records for the selected year and month";
}
?>
