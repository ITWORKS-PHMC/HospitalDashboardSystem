<?php
require 'connection.php';

// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total Inpatient Department (IPD) IDs for the selected year and month
$sql = "SELECT SUM(total_census) as totalcensus_id 
        FROM dashboard_census 
        WHERE YEAR(transaction_date) = $selected_year 
        AND MONTH(transaction_date) = $selected_month
        AND patient_transaction_type = 'I'";

$result = $conn->query($sql);

// Check if there are results
if ($result === false) {
} elseif ($result->num_rows > 0) {
    // Fetch the total IPD count
    $row = $result->fetch_assoc();
    $totalIPD = $row["totalcensus_id"];

    // SQL query to get the target value for the selected year and month
    $sql2 = "SELECT SUM(target_value) as totaltarget 
             FROM dashboard_target 
             WHERE target_type = 'IPD'
             AND YEAR(target_date) = $selected_year 
             AND MONTH(target_date) = $selected_month";
             
    $result2 = $conn->query($sql2);

    if ($result2 === false) {
        // Handle SQL error
        echo "Error executing SQL query: " . $conn->error;
    } elseif ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totaltarget"];
        echo "<div class='result' style='top:70px;left: 75px;position: absolute; color:black;'>$totalIPD/$totalTarget</div>";
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No Inpatient Department (IPD) records for the selected year and month";
}
?>
