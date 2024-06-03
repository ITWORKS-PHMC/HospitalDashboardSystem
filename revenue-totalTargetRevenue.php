<?php
require 'connection.php';

$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

$sql = "SELECT SUM(revenue_totalAmount) as totalrevenue_id 
        FROM dashboard_revenue 
        WHERE YEAR(revenue_date) = $selected_year 
        AND MONTH(revenue_date) = $selected_month";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalRevenue = $row["totalrevenue_id"];
    $totalRevenueFormatted = formatNumber($totalRevenue);

    $sql2 = "SELECT SUM(total_revenue) as totalTarget 
             FROM revenue_target
             WHERE YEAR(target_date) = $selected_year 
             AND MONTH(target_date) = $selected_month";
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totalTarget"];
        $totalTargetFormatted = formatNumber($totalTarget);
        echo "<div class='result' style='top:70px;left: 35px;position: absolute; color:black; font-size: 22px;'>₱$totalRevenueFormatted / ₱$totalTargetFormatted</div>";
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No Revenue records for the selected year and month";
}

function formatNumber($number) {
    if ($number >= 1000000) {
        // Convert to millions and add 'M' suffix
        return number_format($number / 1000000, 1) . ' M';
    } else {
        return number_format($number);
    }
}
?>
