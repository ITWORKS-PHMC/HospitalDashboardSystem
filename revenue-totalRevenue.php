<?php
require 'old-connection.php';

$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

$sql = "SELECT SUM(revenue_totalAmount) AS totalRevenue
        FROM dashboard_revenue
        WHERE YEAR(revenue_date) = $selected_year 
        AND MONTH(revenue_date) = $selected_month";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalRevenue = $row["totalRevenue"];

    $sql2 = "SELECT SUM(target_value) AS totalTarget FROM dashboard_target";
    $result2 = $conn->query($sql2);
 
    if ($result2->num_rows > 0) {
         $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totalTarget"];
            if ($totalRevenue >= $totalTarget) {

    echo "<div class='result' style='top:50px;left:50px;position: absolute; color:black;'>₱" . number_format($totalRevenue) . "<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
} else {
     echo "<div class='result' style='top:50px;left:50px;position: absolute; color:black;'><span class='red-arrow' style='float: left;'>&#8595;</span>₱" . number_format($totalRevenue) . "</div>";
}
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No revenue data for the selected year and month";
}
?>
