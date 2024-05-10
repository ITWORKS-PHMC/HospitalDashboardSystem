<?php
require 'connection.php';
// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total patient IDs for the selected year and month
$sql = "SELECT COUNT(census_transaction_id) as totalcensus_id 
        FROM dashboard_database 
        WHERE YEAR(census_date_admitted) = $selected_year 
        AND MONTH(census_date_admitted) = $selected_month";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch the total patient count
    $row = $result->fetch_assoc();
    $totalPatients = $row["totalcensus_id"];

    // SQL query to get the target value for the selected year and month
    $sql2 = "SELECT SUM(target_value) as totaltarget FROM dashboard_target";
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totaltarget"];
        // Compare total patients with the target value
        if ($totalPatients >= $totalTarget) {
            // Display total patients with green arrow
            echo "<div class='result' style='top:50px;left:100px;position: absolute; color:black;>$totalPatients<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
        } else {
            // Display total patients with red arrow
            echo "<div class='result' style='top:50px;left:100px;position: absolute; color:black;><span class='red-arrow' style='float: left;'>&#8595;</span>$totalPatients</div>";
        }
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No patients for the selected year and month";
}
?>
