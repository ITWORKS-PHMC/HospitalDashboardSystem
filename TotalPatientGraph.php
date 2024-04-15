<?php
require 'connection.php';

// Fetch data from the database
$sql = "SELECT YEAR(transaction_date) AS year, MONTH(transaction_date) AS month, COUNT(census_id) AS total 
        FROM dashboard_census 
        GROUP BY YEAR(transaction_date), MONTH(transaction_date)";
$result = mysqli_query($conn, $sql);

$years = array();
$dataPoints = array();

// Initialize data points for all months with count zero
for ($i = 1; $i <= 12; $i++) {
    $dataPoints[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
}

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $month = $row['month'];
    $total = $row['total'];
    $years[$year][$month] = $total;
}

?>

<!DOCTYPE HTML>
<html>
<head>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
        title: {
            text: "Total Patient for Year 2023" // Default title
        },
        axisY: {
            title: "Number of Total Patient"
        },
        data: [{
            type: "line",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart.render();

    // Function to update chart data based on selected year
    function updateChartData(year) {
        var newDataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        <?php
        foreach ($years as $year => $months) {
            foreach ($months as $month => $total) {
                echo "if (year == $year) newDataPoints[$month - 1].y = $total;\n";
            }
        }
        ?>
        chart.options.data[0].dataPoints = newDataPoints;
        chart.options.title.text = "Total Patient for Year " + year;
        chart.render();
    }

    // Function to handle dropdown selection change
    document.getElementById("yearDropdown").onchange = function() {
        var selectedYear = this.value;
        updateChartData(selectedYear);
    };
}
</script>
</head>
<body>
<!-- Dropdown for selecting year -->
<select id="yearDropdown">
    <?php
        // Populate dropdown with available years
        foreach ($years as $year => $months) {
            echo "<option value='$year'>$year</option>";
        }
    ?>
</select>
<div id="chartContainer"></div>
</body>
</html>
