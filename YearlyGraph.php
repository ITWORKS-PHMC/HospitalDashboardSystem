<?php
require 'connection.php';

// Fetch data from the database for chart1
$sql = "SELECT YEAR(transaction_date) AS year, MONTH(transaction_date) AS month, COUNT(census_id) AS total 
        FROM dashboard_census 
        GROUP BY YEAR(transaction_date), MONTH(transaction_date)";
$result = mysqli_query($conn, $sql);

$years = array();
$dataPoints = array();

// Initialize data points for all months with count zero for the selected year and previous year
$currentYear = date('Y');
$previousYear = $currentYear - 1;
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
// Query to fetch data for chart 2 (yearly data for OPD, IPD, and ER)
$sql_chart2 = "SELECT YEAR(transaction_date) AS year,
                      SUM(CASE WHEN patient_transaction_type = 'OPD' THEN 1 ELSE 0 END) AS total_opd,
                      SUM(CASE WHEN patient_transaction_type = 'IPD' THEN 1 ELSE 0 END) AS total_ipd,
                      SUM(CASE WHEN patient_transaction_type = 'ER' THEN 1 ELSE 0 END) AS total_er
               FROM dashboard_census
               GROUP BY YEAR(transaction_date)";

$result_chart2 = $conn->query($sql_chart2);

$dataPoints_opd = array();
$dataPoints_ipd = array();
$dataPoints_er = array();

// Check if any rows were returned for chart 2
if ($result_chart2->num_rows > 0) {
    // Loop through each row of data for chart 2
    while($row = $result_chart2->fetch_assoc()) {
        // Populate dataPoints arrays with fetched data for OPD, IPD, and ER
        $year = $row["year"];
        $dataPoints_opd[] = array("y" => $row["total_opd"], "label" => $year);
        $dataPoints_ipd[] = array("y" => $row["total_ipd"], "label" => $year);
        $dataPoints_er[] = array("y" => $row["total_er"], "label" => $year);
    }
} else {
    echo "0 results";
}
?>

<!DOCTYPE HTML>
<html>
<head>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: "Total Patient for Year <?php echo $currentYear; ?>" // Default title
        },
        axisY: {
            title: "Number of Total Patient",
            includeZero: false,
        },
        data: [{
            type: "line",
            showInLegend: true,
            name: "<?php echo $currentYear; ?>",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            showInLegend: true,
            name: "<?php echo $previousYear; ?>",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart.render();

var chart2 = new CanvasJS.Chart("chartContainer2", {
    animationEnabled:true,
        title: {
            text: "Yearly OPD, IPD, and ER Data"
        },
        axisY: {
            title: "Number of Patients"
        },
        data: [{
            type: "line",
            name: "OPD",
            showInLegend: true,
            dataPoints: <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            name: "IPD",
            showInLegend: true,
            dataPoints: <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            name: "ER",
            showInLegend: true,
            dataPoints: <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart2.render();
}
function toogleDataSeries(e) {
    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else {
        e.dataSeries.visible = true;
    }
    e.chart.render();
}
    // Function to update chart1 data based on selected year
    function updateChartData(year) {
        var newDataPointsCurrentYear = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        var newDataPointsPreviousYear = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        <?php
        foreach ($years as $year => $months) {
            foreach ($months as $month => $total) {
                echo "if (year == $year) newDataPointsCurrentYear[$month - 1].y = $total;\n";
                echo "if (year - 1 == $year) newDataPointsPreviousYear[$month - 1].y = $total;\n";
            }
        }
        ?>
        chart.options.data[0].dataPoints = newDataPointsCurrentYear;
        chart.options.data[1].dataPoints = newDataPointsPreviousYear;
        chart.options.title.text = "Total Patient for Year " + year +" & "+ (year - 1).toString();
        chart.options.data[0].name = year.toString();
    chart.options.data[1].name = (year - 1).toString();
        chart.render();
    }

    // Function to handle dropdown selection change
    document.getElementById("yearDropdown").onchange = function() {
        var selectedYear = this.value;
        updateChartData(selectedYear);
    };

</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 45%; display: inline-block;"></div>
<div id="chartContainer2" style="height: 370px; width: 45%; display: inline-block;"></div>
</body>
</html>