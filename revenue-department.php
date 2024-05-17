<?php
require 'connection.php';

// Get selected department from the URL parameter
$selected_department = isset($_GET['selected_department']) ? $_GET['selected_department'] : '';
// Get selected year from the URL parameter
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : '';
// Get selected month from the URL parameter
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : '';

// Query to get the total revenue of the selected department for the selected month and year
$totalRevenueQuery = "SELECT SUM(revenue_totalAmount) AS total_revenue FROM dashboard_revenue WHERE revenue_department = '$selected_department' AND YEAR(revenue_date) = '$selected_year' AND MONTH(revenue_date) = '$selected_month'";


$totalRevenueResult = mysqli_query($conn, $totalRevenueQuery);

if (!$totalRevenueResult) {
    // Error handling for query execution
    echo "Error: " . mysqli_error($conn);
} else {
    $totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
    $total_revenue = $totalRevenueRow['total_revenue'];

    // Check if total revenue is empty
    if (empty($total_revenue)) {
        echo "<p style='color:white; top:50px; position: absolute;'>Total Revenue for $selected_department: Not Available</p>";
    } else {
        echo "<p style='color:white; top:50px; position: absolute; margin: auto;'>Total Revenue for $selected_department: $total_revenue</p>";
    }
}
?>
