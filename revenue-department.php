<?php
require 'old-connection.php';

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

    // Display total revenue in a separate div with inline CSS
    if (empty($total_revenue)) {
        echo "<div id='total-revenue' style='color:black; font-weight:bold; margin-bottom: 20px;'>Total Revenue: Not Available</div>";
    } else {
echo "<div id='total-revenue' style='color:black; font-weight:bold; margin-bottom: 20px;text-align:center;border:1px;background-color: rgba(51, 135, 131, 0.8); padding: 10px;'>Total Revenue: ₱" . format_revenue($total_revenue) . "</div>";
    }
    // If "See All" is selected, display each department's revenue in a table
    if ($selected_department === 'all') {
        $departmentRevenueResult = mysqli_query($conn, $departmentRevenueQuery);
        if (!$departmentRevenueResult) {
            echo "Error: " . mysqli_error($conn);
        } else {
            // Start table with inline CSS
            echo "<div id='department-revenue-table' style='overflow-x: auto; overflow-y: auto; max-height: 280px;'>"; // Add horizontal and vertical scroll if needed
            echo "<table>";
            // Table header
            echo "<tr><th>Department</th><th>Revenue</th></tr>";
            while ($departmentRevenueRow = mysqli_fetch_assoc($departmentRevenueResult)) {
                // Table rows for each department's revenue
                echo "<tr><td>" . $departmentRevenueRow['revenue_department'] . "</td><td>₱" . format_revenue($departmentRevenueRow['department_revenue']) . "</td></tr>";
            }
            // End table
            echo "</table>";
            echo "</div>"; // Close department-revenue-table div
        }
    }
}

// Function to format revenue with peso sign and abbreviate if more than a million or thousand
function format_revenue($revenue) {
    if ($revenue >= 1000000) {
        return number_format($revenue / 1000000, 1) . 'M';
    } elseif ($revenue >= 1000) {
        return number_format($revenue / 1000, 1) . 'k';
    } else {
        return number_format($revenue);
    }
}
?>
