<?php
require 'connection.php';

// Get selected department from the URL parameter
$selected_department = isset($_GET['selected_department']) ? $_GET['selected_department'] : '';
// Get selected year from the URL parameter
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : '';
// Get selected month from the URL parameter
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : '';

if ($selected_department === 'all') {
    // Query to get the total revenue for all departments for the selected month and year
    $totalRevenueQuery = "SELECT SUM(revenue_totalAmount) AS total_revenue FROM dashboard_revenue WHERE YEAR(revenue_date) = '$selected_year' AND MONTH(revenue_date) = '$selected_month'";
    $departmentRevenueQuery = "SELECT revenue_department, SUM(revenue_totalAmount) AS department_revenue FROM dashboard_revenue WHERE YEAR(revenue_date) = '$selected_year' AND MONTH(revenue_date) = '$selected_month' GROUP BY revenue_department";
} else {
    // Query to get the total revenue of the selected department for the selected month and year
    $totalRevenueQuery = "SELECT SUM(revenue_totalAmount) AS total_revenue FROM dashboard_revenue WHERE revenue_department = '$selected_department' AND YEAR(revenue_date) = '$selected_year' AND MONTH(revenue_date) = '$selected_month'";
    $departmentRevenueQuery = ""; // No department query needed if a specific department is selected
}

$totalRevenueResult = mysqli_query($conn, $totalRevenueQuery);

if (!$totalRevenueResult) {
    // Error handling for query execution
    echo "Error: " . mysqli_error($conn);
} else {
    $totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
    $total_revenue = $totalRevenueRow['total_revenue'];

    // Display total revenue
    if (empty($total_revenue)) {
        echo "<p style='color:white; top:10px; position: absolute;'>Total Revenue: Not Available</p>";
    } else {
        echo "<p style='color:white; top:10px; position: absolute; margin: auto;'>Total Revenue: $total_revenue</p>";
    }

    // If "See All" is selected, display each department's revenue
    if ($selected_department === 'all') {
        $departmentRevenueResult = mysqli_query($conn, $departmentRevenueQuery);
        if (!$departmentRevenueResult) {
            echo "Error: " . mysqli_error($conn);
        } else {
                while ($departmentRevenueRow = mysqli_fetch_assoc($departmentRevenueResult)) {
                echo "<p>" . $departmentRevenueRow['revenue_department'] . ": " . $departmentRevenueRow['department_revenue'] . "</p>";
            }
            echo "</div>";
        }
    }
}
?>
