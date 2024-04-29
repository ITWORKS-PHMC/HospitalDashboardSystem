        <?php
require 'connection.php';
// Check if selected_year and selected_month are set in the URL parameters
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// SQL query to count total Inpatient Department (IPD) IDs for the selected year and month
$sql = "SELECT COUNT(census_id) as totalcensus_id 
        FROM dashboard_census 
        WHERE YEAR(transaction_date) = $selected_year 
        AND MONTH(transaction_date) = $selected_month
        AND patient_transaction_type = 'IPD'";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch the total IPD count
    $row = $result->fetch_assoc();
    $totalIPD = $row["totalcensus_id"];

    // SQL query to get the target value for the selected year and month
    $sql2 = "SELECT SUM(target_value) as totaltarget FROM dashboard_target";
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {
        // Fetch the target value
        $row2 = $result2->fetch_assoc();
        $totalTarget = $row2["totaltarget"];
        echo "<div class='result'>$totalIPD/3000</div>";
    } else {
        echo "No target set for the selected year and month";
    }
} else {
    echo "No Inpatient Department (IPD) records for the selected year and month";
}
?>