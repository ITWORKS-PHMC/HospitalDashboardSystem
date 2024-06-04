<?php
require 'connection.php';

// Helper function to check the result and handle errors
function checkQueryResult($result, $sql) {
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    return $result;
}

// Fetch data from the database
$sql = "SELECT YEAR(datetimeadmitted) AS year, MONTH(datetimeadmitted) AS month, COUNT(PK_psPatRegisters) AS total 
        FROM rptCensus
        GROUP BY YEAR(datetimeadmitted), MONTH(datetimeadmitted)";
$result = checkQueryResult(sqlsrv_query($conn, $sql), $sql);

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
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $year = $row['year'];
    $month = $row['month'];
    $total = $row['total'];
    $years[$year][$month] = $total;
}

// Fetch data from the database for the current year
$sql_currentYear = "SELECT MONTH(datetimeadmitted) AS month, COUNT(PK_psPatRegisters) AS total 
                    FROM rptCensus
                    WHERE YEAR(datetimeadmitted) = $currentYear
                    GROUP BY MONTH(datetimeadmitted)";
$result_currentYear = checkQueryResult(sqlsrv_query($conn, $sql_currentYear), $sql_currentYear);

$dataPoints_currentYear = array_fill(0, 12, array("y" => 0, "label" => ""));

// Initialize labels for all months
for ($i = 0; $i < 12; $i++) {
    $label = date("F", mktime(0, 0, 0, $i + 1, 1));
    $dataPoints_currentYear[$i]["label"] = $label;
}

// Process fetched data into format suitable for CanvasJS
while ($row = sqlsrv_fetch_array($result_currentYear, SQLSRV_FETCH_ASSOC)) {
    $month = $row['month'] - 1; // Adjust month to zero-based index for arrays
    $total = $row['total'];
    $dataPoints_currentYear[$month]["y"] = $total;
}

$sql_chart2 = "SELECT YEAR(datetimeadmitted) AS year,
                      COUNT(CASE WHEN pattrantype = 'O' THEN 1 END) AS total_opd,
                      COUNT(CASE WHEN pattrantype = 'I' THEN 1 END) AS total_ipd,
                      COUNT(CASE WHEN pattrantype = 'E' THEN 1 END) AS total_er
               FROM rptCensus
               GROUP BY YEAR(datetimeadmitted)";
               
$result_chart2 = checkQueryResult(sqlsrv_query($conn, $sql_chart2), $sql_chart2);

$dataPoints_opd = array();
$dataPoints_ipd = array();
$dataPoints_er = array();

// Check if any rows were returned for chart 2
if (sqlsrv_has_rows($result_chart2)) {
    // Loop through each row of data for chart 2
    while ($row = sqlsrv_fetch_array($result_chart2, SQLSRV_FETCH_ASSOC)) {
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
<script src="./lib/graphs.js"></script>
</head>
<body>
<div class="charts-container" style="display: flex;">
<div class="chart-1" style="flex: 1; margin-right: 10px;">
<div id="chartContainer" style="height: 370px; width: 1220px;box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></div>
</div>
<div class="chart-2" style="flex: 1; margin-left: 10px;">
<div id="chartContainer2" style="height: 370px; width: 620px;box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></div>
</div>
</div>

<script>
window.onload = function () {
    var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_opd = <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ipd = <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_er = <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>;
    
// Render chart1 with default data for the current year
var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    title: {
        text: "Total Patients for Year <?php echo $currentYear." & ".$previousYear; ?>"
    },
    axisY: {
        title: "Revenue",
        includeZero: false,
    },
    backgroundColor: "transparent",
    toolTip: {
        shared: true 
    },
    legend: {
        verticalAlign: "bottom",
        horizontalAlign: "center",

    },
    data: [{
        type: "line",
        showInLegend: true,
        name: "<?php echo $currentYear; ?>",
        color: "green",
        dataPoints: <?php echo json_encode($dataPoints_currentYear, JSON_NUMERIC_CHECK); ?>
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

chart.render();
    
var chart2 = new CanvasJS.Chart("chartContainer2", {
    animationEnabled: true,
    title: {
        text: "Yearly OPD, IPD, and ER Data"
    },
    axisY: {
        title: "Number of Patients"
    },
    legend: {
        verticalAlign: "bottom",
        horizontalAlign: "center",
    },
    backgroundColor: "transparent",
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
        },
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
