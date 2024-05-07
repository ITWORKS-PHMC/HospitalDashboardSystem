<?php
require 'connection.php';

// Fetch data from the database
$sql = "SELECT YEAR(census_date_admitted) AS year, MONTH(census_date_admitted) AS month, COUNT(census_transaction_id) AS total 
        FROM dashboard_database
        GROUP BY YEAR(census_date_admitted), MONTH(census_date_admitted)";
$result = mysqli_query($conn, $sql);

$years = array();
$dataPoints = array();
$dataPoints_previousYear = array(); // Added array for previous year's data

// Initialize data points for all months with count zero for the selected year and previous year
$currentYear = date('Y');
$previousYear = $currentYear - 1;
for ($i = 1; $i <= 12; $i++) {
    $dataPoints[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
    $dataPoints_previousYear[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
}

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $month = $row['month'];
    $total = $row['total'];
    $years[$year][$month] = $total;
}

// Query to fetch data for chart 2 (yearly data for OPD, IPD, and ER)
$sql_chart2 = "SELECT YEAR(census_date_admitted) AS year,
                      SUM(CASE WHEN census_transaction_type = 'O' THEN 1 ELSE 0 END) AS total_opd,
                      SUM(CASE WHEN census_transaction_type = 'I' THEN 1 ELSE 0 END) AS total_ipd,
                      SUM(CASE WHEN census_transaction_type = 'E' THEN 1 ELSE 0 END) AS total_er
               FROM dashboard_database
               GROUP BY YEAR(census_date_admitted)";

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
<script src="lib/graphs.js"></script>
</head>
<body>
<div class="charts-container" style=" width: 100%;height: 150px;left: 380px; bottom: 400px;position:relative;">
<div class="chart-1">
<div id="chartContainer2" style="height: 390px; width: 1050px;"></div>
</div></div>
<br>
<div class="chart2-container" style=" width: 100%;height: 150px;left: 10px; bottom: 170px;position:relative;">
<div class="chart-2">
<div id="chartContainer" style="height: 350px; width: 1420px;"></div>
</div>
</div>


<script>
window.onload = function () {
    var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_opd = <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ipd = <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_er = <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>;
    
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: "Total Patient for Year <?php echo $currentYear; ?>"
        },
        axisY: {
            title: "Number of Total Patient",
            includeZero: false,
        },
        toolTip: {
            shared: true 
        },
        legend: {
            verticalAlign: "center",
            horizontalAlign: "right",
            
        },
        data: [{
            type: "line",
            showInLegend: true,
            name: "<?php echo $currentYear; ?>",
            color: "green",
            dataPoints: dataPoints
        },
        {
            type: "line",
            showInLegend: true,
            name: "<?php echo $previousYear; ?>",
            color: "red",
            dataPoints: dataPoints_previousYear
        }],
        toolTipContent: "{name}: {y} - {x}",
    });
    
var chart2 = new CanvasJS.Chart("chartContainer2", {
    animationEnabled: true,
    title: {
        text: "Yearly OPD, IPD, and ER Data"
    },
    axisY: {
        title: "Number of Patients"
    },
    legend: {
        verticalAlign: "center",
        horizontalAlign: "right",
    },
    toolTip: {
        shared: true // This will ensure all series data is shown in the tooltip
    },
    data: [{
            type: "line",
            name: "OPD",
            showInLegend: true,
            color: "red",
            dataPoints: dataPoints_opd
        },
        {
            type: "line",
            name: "IPD",
            showInLegend: true,
            color: "orange",
            dataPoints: dataPoints_ipd
        },
        {
            type: "line",
            name: "ER",
            showInLegend: true,
            color: "green",
            dataPoints: dataPoints_er
        }
    ]
});

   
    chart.render();
    chart2.render();

    document.getElementById("yearDropdown").onchange = function() {
        var selectedYear = this.value;
        updateChartData(selectedYear);
    };
    
function updateChartData(year) {
    var newDataPointsCurrentYear = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var newDataPointsPreviousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    <?php
    foreach ($years as $chartYear => $months) {
        foreach ($months as $month => $total) {
            echo "if (year == $chartYear) newDataPointsCurrentYear[$month - 1].y = $total;\n";
            echo "if (year - 1 == $chartYear) newDataPointsPreviousYear[$month - 1].y = $total;\n";
        }
    }
    ?>
    chart.options.data[0].dataPoints = newDataPointsCurrentYear;
    chart.options.data[1].dataPoints = newDataPointsPreviousYear;
    chart.options.title.text = "Total Patient for Year " + year +" & "+ (year - 1).toString();
    chart.options.data[0].name = year.toString();
    chart.options.data[1].name = (year - 1).toString();
    chart.render();

    // Filter data for OPD, IPD, and ER separately based on the selected year and the previous two years
    var selectedYear = parseInt(year);
    var dataPoints_opd_filtered = <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_ipd_filtered = <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_er_filtered = <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });

    chart2.options.title.text = "Yearly OPD, IPD, and ER Data for " + year + ", " + (year - 1) + ", and " + (year - 2);
    chart2.options.data[0].dataPoints = dataPoints_opd_filtered;
    chart2.options.data[1].dataPoints = dataPoints_ipd_filtered;
    chart2.options.data[2].dataPoints = dataPoints_er_filtered;
    chart2.render();
}
}
</script>

</body>
</html>
