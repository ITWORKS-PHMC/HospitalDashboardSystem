<?php
require 'connection.php';

// Fetch data from the database
$sql = "SELECT YEAR(revenue_date) AS year, MONTH(revenue_date) AS month, SUM(revenue_totalAmount) AS total 
        FROM dashboard_revenue
        GROUP BY YEAR(revenue_date), MONTH(revenue_date)";
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

// Fetch data from the database for the current year
$sql_currentYear = "SELECT MONTH(revenue_date) AS month, SUM(revenue_totalAmount) AS total 
                    FROM dashboard_revenue
                    WHERE YEAR(revenue_date) = $currentYear
                    GROUP BY MONTH(revenue_date)";
$result_currentYear = mysqli_query($conn, $sql_currentYear);

$dataPoints_currentYear = array_fill(0, 12, array("y" => 0, "label" => ""));

// Initialize labels for all months
for ($i = 0; $i < 12; $i++) {
    $label = date("F", mktime(0, 0, 0, $i + 1, 1));
    $dataPoints_currentYear[$i]["label"] = $label;
}

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result_currentYear)) {
    $month = $row['month'] - 1; // Adjust month to zero-based index for arrays
    $total = $row['total'];
    $dataPoints_currentYear[$month]["y"] = $total;
}

// Query to fetch data for chart 2 (monthly data for various departments)
$sql_chart2 = "SELECT MONTH(revenue_date) AS month,
              SUM(CASE WHEN revenue_department = 'CT-SCAN' THEN revenue_totalAmount ELSE 0 END) AS total_ctscan,
              SUM(CASE WHEN revenue_department = 'DIABETES CL' THEN revenue_totalAmount ELSE 0 END) AS total_diabetes_cl,
              SUM(CASE WHEN revenue_department = 'DIETARY' THEN revenue_totalAmount ELSE 0 END) AS total_dietary,
              SUM(CASE WHEN revenue_department = 'DOCTORS WIN' THEN revenue_totalAmount ELSE 0 END) AS total_doctors_win,
              SUM(CASE WHEN revenue_department = 'EMERGENCY R' THEN revenue_totalAmount ELSE 0 END) AS total_emergency_r,
              SUM(CASE WHEN revenue_department = 'EYE CENTER' THEN revenue_totalAmount ELSE 0 END) AS total_eye_center,
              SUM(CASE WHEN revenue_department = 'FIFTH FLOOR' THEN revenue_totalAmount ELSE 0 END) AS total_fifth_floor,
              SUM(CASE WHEN revenue_department = 'FIFTh FL' THEN revenue_totalAmount ELSE 0 END) AS total_fifth_fl,
              SUM(CASE WHEN revenue_department = 'GHMS 2' THEN revenue_totalAmount ELSE 0 END) AS total_ghms,
              SUM(CASE WHEN revenue_department = 'HEARING CEN' THEN revenue_totalAmount ELSE 0 END) AS total_hearing_cen,
              SUM(revenue_totalAmount) AS total_revenue 
              FROM dashboard_revenue
              GROUP BY MONTH(revenue_date)";

$result_chart2 = $conn->query($sql_chart2);
$dataPoints_ctscan = array();
$dataPoints_diabetes_cl = array();
$dataPoints_dietary = array();
$dataPoints_doctors_win = array();
$dataPoints_emergency_r = array();
$dataPoints_eye_center = array();
$dataPoints_fifth_floor = array();
$dataPoints_fifth_fl = array();
$dataPoints_ghms = array();
$dataPoints_hearing_cen = array();
// Check if any rows were returned for chart 2
if ($result_chart2->num_rows > 0) {
    // Loop through each row of data for chart 2
    while($row = $result_chart2->fetch_assoc()) {
        // Populate dataPoints arrays with fetched data for each department
        $month = date("F", mktime(0, 0, 0, $row["month"], 1)); // Get the name of the month
        $dataPoints_ctscan[] = array("y" => $row["total_ctscan"], "label" => $month);
        $dataPoints_diabetes_cl[] = array("y" => $row["total_diabetes_cl"], "label" => $month);
        $dataPoints_dietary[] = array("y" => $row["total_dietary"], "label" => $month);
        $dataPoints_doctors_win[] = array("y" => $row["total_doctors_win"], "label" => $month);
        $dataPoints_emergency_r[] = array("y" => $row["total_emergency_r"], "label" => $month);
        $dataPoints_eye_center[] = array("y" => $row["total_eye_center"], "label" => $month);
        $dataPoints_fifth_floor[] = array("y" => $row["total_fifth_floor"], "label" => $month);
        $dataPoints_fifth_fl[] = array("y" => $row["total_fifth_fl"], "label" => $month);
        $dataPoints_ghms[] = array("y" => $row["total_ghms"], "label" => $month);
        $dataPoints_hearing_cen[] = array("y" => $row["total_hearing_cen"], "label" => $month);
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
<div class="charts-container" style="display: block;">
<div class="chart-1" style="flex: 1; display:flex;">
<div id="chartContainer" style="height: 370px; width: 1550px; box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></div>
</div>

</div>
<div class="chart-2" style="flex: 1; margin-top: 40px; display:flex;">
<div id="chartContainer2" style="height: 370px; width: 1550px; box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></div>
</div>
</div>


<script>
window.onload = function () {
    var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ctscan = <?php echo json_encode($dataPoints_ctscan, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_diabetes_cl = <?php echo json_encode($dataPoints_diabetes_cl, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_dietary = <?php echo json_encode($dataPoints_dietary, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_doctors_win = <?php echo json_encode($dataPoints_doctors_win, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_emergency_r = <?php echo json_encode($dataPoints_emergency_r, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_eye_center = <?php echo json_encode($dataPoints_eye_center, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_fifth_floor = <?php echo json_encode($dataPoints_fifth_floor, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_fifth_fl = <?php echo json_encode($dataPoints_fifth_fl, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ghms = <?php echo json_encode($dataPoints_ghms, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_hearing_cen = <?php echo json_encode($dataPoints_hearing_cen, JSON_NUMERIC_CHECK); ?>;
    
// Render chart1 with default data for the current year
var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    title: {
        text: "Total Revenue for Year <?php echo $currentYear; ?>"
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
        verticalAlign: "center",
        horizontalAlign: "right",
        
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
    
var chart2 = new CanvasJS.Chart("chartContainer2", {
    animationEnabled: true,
    title: {
        text: "Monthly Revenue per Department"
    },
    axisY: {
        title: "Revenue"
    },
    legend: {
        verticalAlign: "center",
        horizontalAlign: "right",
    },
    backgroundColor: "transparent",
    toolTip: {
        shared: true // This will ensure all series data is shown in the tooltip
    },
    data: [{
            type: "bar",
            name: "CT-Scan",
            showInLegend: true,
            color: "red",
            dataPoints: dataPoints_ctscan
        },
        {
            type: "bar",
            name: "Diabetes",
            showInLegend: true,
            color: "orange",
            dataPoints: dataPoints_diabetes_cl
        },
        {
            type: "bar",
            name: "Dietary",
            showInLegend: true,
            color: "green",
            dataPoints: dataPoints_dietary
        },
        {
            type: "bar",
            name: "Doctors Wing",
            showInLegend: true,
            color: "black",
            dataPoints: dataPoints_doctors_win
        },
        {
            type: "bar",
            name: "Emergency Room",
            showInLegend: true,
            color: "teal",
            dataPoints: dataPoints_emergency_r
        },
        {
            type: "bar",
            name: "Eye Center",
            showInLegend: true,
            color: "aqua",
            dataPoints: dataPoints_eye_center
        },
        {
            type: "bar",
            name: "Fifth Floor Wing B1",
            showInLegend: true,
            color: "blue",
            dataPoints: dataPoints_fifth_floor
        },
        {
            type: "bar",
            name: "Fifth Floor Wing B2",
            showInLegend: true,
            color: "purple",
            dataPoints: dataPoints_fifth_fl
        },
        {
            type: "bar",
            name: "GHMS",
            showInLegend: true,
            color: "yellow",
            dataPoints: dataPoints_ghms
        },
        {
            type: "bar",
            name: "HEARING CENTER",
            showInLegend: true,
            color: "white",
            dataPoints: dataPoints_hearing_cen
        },
        
    ]
});

    chart.render();
    chart2.render();

    document.getElementById("yearDropdown").onchange = function() {
        var selectedYear = this.value;
        updateChartData(selectedYear);
    };

    document.getElementById("monthFilter").onchange = function() {
        var selectedMonth = this.value;
        updateChartData2(selectedMonth);
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
    chart.options.title.text = "Total Revenue for Year " + year +" & "+ (year - 1).toString();
    chart.options.data[0].name = year.toString();
    chart.options.data[1].name = (year - 1).toString();
    chart.render();
}

function updateChartData2(month) {
    var monthLabel = "<?php echo date('F', mktime(0, 0, 0, $month, 1)); ?>"; // Convert month number to month name

    // Filter data for each department based on the selected month
    var filterData = function(dataPoints) {
        return dataPoints.filter(function(dataPoint) {
            return dataPoint.label === monthLabel;
        });
    };

    var dataPoints_ctscan_filtered = filterData(<?php echo json_encode($dataPoints_ctscan, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_diabetes_cl_filtered = filterData(<?php echo json_encode($dataPoints_diabetes_cl, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_dietary_filtered = filterData(<?php echo json_encode($dataPoints_dietary, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_doctors_win_filtered = filterData(<?php echo json_encode($dataPoints_doctors_win, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_emergency_r_filtered = filterData(<?php echo json_encode($dataPoints_emergency_r, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_eye_center_filtered = filterData(<?php echo json_encode($dataPoints_eye_center, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_fifth_floor_filtered = filterData(<?php echo json_encode($dataPoints_fifth_floor, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_fifth_fl_filtered = filterData(<?php echo json_encode($dataPoints_fifth_fl, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_ghms_filtered = filterData(<?php echo json_encode($dataPoints_ghms, JSON_NUMERIC_CHECK); ?>);
    var dataPoints_hearing_cen_filtered = filterData(<?php echo json_encode($dataPoints_hearing_cen, JSON_NUMERIC_CHECK); ?>);

    // Check if any department has data for the selected month
    var hasData = dataPoints_ctscan_filtered.length > 0 || dataPoints_diabetes_cl_filtered.length > 0 ||
                  dataPoints_dietary_filtered.length > 0 || dataPoints_doctors_win_filtered.length > 0 ||
                  dataPoints_emergency_r_filtered.length > 0 || dataPoints_eye_center_filtered.length > 0 ||
                  dataPoints_fifth_floor_filtered.length > 0 || dataPoints_fifth_fl_filtered.length > 0 ||
                  dataPoints_ghms_filtered.length > 0 || dataPoints_hearing_cen_filtered.length > 0;

    // If no department has data for the selected month, display a message
    if (!hasData) {
        alert("No data available for " + monthLabel);
        return;
    }

    // Update chart options with the filtered data
    chart2.options.title.text = "Monthly Revenue per Department for " + monthLabel;
    chart2.options.data[0].dataPoints = dataPoints_ctscan_filtered;
    chart2.options.data[1].dataPoints = dataPoints_diabetes_cl_filtered;
    chart2.options.data[2].dataPoints = dataPoints_dietary_filtered;
    chart2.options.data[3].dataPoints = dataPoints_doctors_win_filtered;
    chart2.options.data[4].dataPoints = dataPoints_emergency_r_filtered;
    chart2.options.data[5].dataPoints = dataPoints_eye_center_filtered;
    chart2.options.data[6].dataPoints = dataPoints_fifth_floor_filtered;
    chart2.options.data[7].dataPoints = dataPoints_fifth_fl_filtered;
    chart2.options.data[8].dataPoints = dataPoints_ghms_filtered;
    chart2.options.data[9].dataPoints = dataPoints_hearing_cen_filtered;

    // Render the updated chart
    chart2.render();
}


}  
</script>

</body>
</html>
