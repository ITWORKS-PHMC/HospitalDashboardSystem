<?php
require 'connection.php';
// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total patient IDs for the selected year and month by transaction type
$sql = "SELECT patient_transaction_type, SUM(total_census) as totalcensus_id 
        FROM dashboard_census 
        WHERE YEAR(transaction_date) = $selected_year 
        AND MONTH(transaction_date) = $selected_month 
        GROUP BY patient_transaction_type";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Initialize variables to hold total counts for each transaction type
    $opdCount = 0;
    $ipdCount = 0;
    $erCount = 0;

    // Loop through the results and sum up counts for each transaction type
    while ($row = $result->fetch_assoc()) {
        if ($row["patient_transaction_type"] == "O") {
            $opdCount = $row["totalcensus_id"];
        } elseif ($row["patient_transaction_type"] == "I") {
            $ipdCount = $row["totalcensus_id"];
        } elseif ($row["patient_transaction_type"] == "E") {
            $erCount = $row["totalcensus_id"];
        }
    }

    // SQL query to get the target values for the selected year and month
    $sql2 = "SELECT SUM(target_value) as totaltarget FROM dashboard_target";
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totaltarget"];
        
        // Calculate total patients from all transaction types
        $totalPatients = $opdCount + $ipdCount + $erCount;

        // Compare total patients with the target value
        if ($totalPatients >= $totalTarget) {
            // Display total patients with green arrow
            echo "<div class='result' style='top:50px;left:100px;position: absolute; color:black;'>$totalPatients<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
        } else {
            // Display total patients with red arrow
            echo "<div class='result' style='top:50px;left:100px;position: absolute; color:black;'><span class='red-arrow' style='float: left;'>&#8595;</span>$totalPatients</div>";
        }
    } else {
        echo "<p style='font-weight:bold;color:black; top:50px; position: absolute;'>No target set for the selected year and month</p>";
    }
} else {
    echo "<p style='font-weight:bold;color:black; top:50px; position: absolute;'>No patients for the selected year and month</p>";
}
?>
