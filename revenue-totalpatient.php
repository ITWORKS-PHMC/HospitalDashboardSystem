<?php
require 'connection.php';
// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to sum total revenue for the selected year and month
$sql = "SELECT SUM(revenue_totalAmount) AS totalRevenue
        FROM dashboard_revenue
        WHERE YEAR(revenue_date) = $selected_year 
        AND MONTH(revenue_date) = $selected_month";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch the total revenue
    $row = $result->fetch_assoc();
    $totalRevenue = $row["totalRevenue"];

    // SQL query to get the target value for the selected year and month
    $sql2 = "SELECT SUM(target_value) AS totalTarget FROM dashboard_target";
    $result2 = $conn->query($sql2);
 
    if ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totalTarget"];
        // Compare total revenue with the target value
        if ($totalRevenue >= $totalTarget) {
            // Display total revenue with green arrow
            echo "<div class='result' style='top:50px;left:70px;position: absolute; color:black;'>$totalRevenue<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
        } else {
            // Display total revenue with red arrow
            echo "<div class='result' style='top:50px;left:70px;position: absolute; color:black;'><span class='red-arrow' style='float: left;'>&#8595;</span>$totalRevenue</div>";
        }
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No revenue data for the selected year and month";
}
?>
