<?php
require 'connection.php';

// Fetch data for chart1 
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
// ----------------------------------------------------------------------------------------------
// FETCH DATA FOR CHART2
$selectedYear = date('Y');

// Update selected year based on dropdown menu selection
if (isset($_GET['yearDropdown'])) {
    $selectedYear = $_GET['yearDropdown'];
}

// Query to fetch data for chart 2 (monthly data for various departments)
$sql_chart2 = "SELECT DISTINCT YEAR(revenue_date) AS year,
              MONTH(revenue_date) AS month,
              SUM(CASE WHEN revenue_department = 'CT-SCAN' THEN revenue_totalAmount ELSE 0 END) AS total_ctscan,
              SUM(CASE WHEN revenue_department = 'DIABETES CLINIC' THEN revenue_totalAmount ELSE 0 END) AS total_diabetes_clinic,
              SUM(CASE WHEN revenue_department = 'DIETARY' THEN revenue_totalAmount ELSE 0 END) AS total_dietary,
              SUM(CASE WHEN revenue_department = 'DOCTORS WING' THEN revenue_totalAmount ELSE 0 END) AS total_doctors_wing,
              SUM(CASE WHEN revenue_department = 'EMERGENCY ROOM' THEN revenue_totalAmount ELSE 0 END) AS total_emergency_room,
              SUM(CASE WHEN revenue_department = 'EYE CENTER' THEN revenue_totalAmount ELSE 0 END) AS total_eye_center,
              SUM(CASE WHEN revenue_department = 'FIFTH FLOOR WING B1' THEN revenue_totalAmount ELSE 0 END) AS total_fifth_floor_b1,
              SUM(CASE WHEN revenue_department = 'FIFTH FLOOR WING B2' THEN revenue_totalAmount ELSE 0 END) AS total_fifth_floor_b2,
              SUM(CASE WHEN revenue_department = 'GHMS 2' THEN revenue_totalAmount ELSE 0 END) AS total_ghms,
              SUM(CASE WHEN revenue_department = 'HEARING CENTER' THEN revenue_totalAmount ELSE 0 END) AS total_hearing_center
              FROM dashboard_revenue
              GROUP BY YEAR(revenue_date), MONTH(revenue_date)";

$result_chart2 = $conn->query($sql_chart2);

$dataPoints_ctscan = array();
$dataPoints_diabetes_clinic = array();
$dataPoints_dietary = array();
$dataPoints_doctors_wing = array();
$dataPoints_emergency_room = array();
$dataPoints_eye_center = array();
$dataPoints_fifth_floor_b1 = array();
$dataPoints_fifth_floor_b2 = array();
$dataPoints_ghms = array();
$dataPoints_hearing_center = array();

// Check if any rows were returned for chart 2
if ($result_chart2->num_rows > 0) {
    // Loop through each row of data for chart 2
    while ($row = $result_chart2->fetch_assoc()) {
        $year = $row["year"];
        $month = $row["month"];
        $monthLabel = date("F", mktime(0, 0, 0, $month, 1)); // Get the name of the month
        
        // Populate data points arrays for each department
        $dataPoints_ctscan[] = array("y" => $row["total_ctscan"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_diabetes_clinic[] = array("y" => $row["total_diabetes_clinic"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_dietary[] = array("y" => $row["total_dietary"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_doctors_wing[] = array("y" => $row["total_doctors_wing"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_emergency_room[] = array("y" => $row["total_emergency_room"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_eye_center[] = array("y" => $row["total_eye_center"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_fifth_floor_b1[] = array("y" => $row["total_fifth_floor_b1"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_fifth_floor_b2[] = array("y" => $row["total_fifth_floor_b2"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_ghms[] = array("y" => $row["total_ghms"], "label" => $monthLabel, "year" => $year, "month" => $month);
        $dataPoints_hearing_center[] = array("y" => $row["total_hearing_center"], "label" => $monthLabel, "year" => $year, "month" => $month);
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
    var dataPoints_currentYear = <?php echo json_encode($dataPoints_currentYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
   var dataPoints_ctscan = <?php echo json_encode($dataPoints_ctscan, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_diabetes_clinic = <?php echo json_encode($dataPoints_diabetes_clinic, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_dietary = <?php echo json_encode($dataPoints_dietary, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_doctors_wing = <?php echo json_encode($dataPoints_doctors_wing, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_emergency_room = <?php echo json_encode($dataPoints_emergency_room, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_eye_center = <?php echo json_encode($dataPoints_eye_center, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_fifth_floor_b1 = <?php echo json_encode($dataPoints_fifth_floor_b1, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_fifth_floor_b2 = <?php echo json_encode($dataPoints_fifth_floor_b2, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ghms = <?php echo json_encode($dataPoints_ghms, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_hearing_center = <?php echo json_encode($dataPoints_hearing_center, JSON_NUMERIC_CHECK); ?>;

    function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
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
            shared: true,
             contentFormatter: function (e) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                var dataPoint = e.entries[i].dataPoint;
                content += "<strong>" + e.entries[i].dataSeries.name + "</strong>: <span style='color:" + e.entries[i].dataSeries.color + "'>₱" + numberWithCommas(dataPoint.y) + "</span><br/>";
            }
            return content;
        }
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
            dataPoints: dataPoints_currentYear
        },
        {
            type: "line",
            showInLegend: true,
            name: "<?php echo $previousYear; ?>",
            color: "red",
            dataPoints: dataPoints_previousYear
        }]
    });

    // Render chart2 with default data
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
            shared: true,
            contentFormatter: function (e) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                var dataPoint = e.entries[i].dataPoint;
                content += "<strong>" + e.entries[i].dataSeries.name + "</strong>: <span style='color:" + e.entries[i].dataSeries.color + "'>₱" + numberWithCommas(dataPoint.y) + "</span><br/>";
            }
            return content;
        }
        },
        data: [{
            type: "column",
            name: "CT-Scan",
            showInLegend: true,
            color: "red",
            dataPoints: dataPoints_ctscan
        },
        {
            type: "column",
            name: "Diabetes",
            showInLegend: true,
            color: "orange",
            dataPoints: dataPoints_diabetes_clinic
        },
        {
            type: "column",
            name: "Dietary",
            showInLegend: true,
            color: "green",
            dataPoints: dataPoints_dietary
        },
        {
            type: "column",
            name: "Doctors Wing",
            showInLegend: true,
            color: "black",
            dataPoints: dataPoints_doctors_wing
        },
        {
            type: "column",
            name: "Emergency Room",
            showInLegend: true,
            color: "teal",
            dataPoints: dataPoints_emergency_room
        },
        {
            type: "column",
            name: "Eye Center",
            showInLegend: true,
            color: "aqua",
            dataPoints: dataPoints_eye_center
        },
        {
            type: "column",
            name: "Fifth Floor Wing B1",
            showInLegend: true,
            color: "blue",
            dataPoints: dataPoints_fifth_floor_b1
        },
        {
            type: "column",
            name: "Fifth Floor Wing B2",
            showInLegend: true,
            color: "purple",
            dataPoints: dataPoints_fifth_floor_b2
        },
        {
            type: "column",
            name: "GHMS",
            showInLegend: true,
            color: "black",
            dataPoints: dataPoints_ghms
        },
        {
            type: "column",
            name: "Hearing Center",
            showInLegend: true,
            color: "cyan",
            dataPoints: dataPoints_hearing_center
        }]
    });

    chart.render();
    chart2.render();
    // Update chart data when year dropdown changes
    document.getElementById("yearDropdown").addEventListener("change", function () {
        var selectedYear = this.value;
        updateChartData(selectedYear);
        var selectedMonth = document.getElementById("monthFilter").value;
        updateChartData2(selectedYear, selectedMonth); 
    });

    // Update chart data when month filter changes
    document.getElementById("monthFilter").addEventListener("change", function () {
        var selectedMonth = this.value;
        var selectedYear = document.getElementById("yearDropdown").value; // Get selected year
        updateChartData2(selectedYear, selectedMonth);
    });

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
        chart.options.title.text = "Total Revenue for Year " + year + " & " + (year - 1).toString();
        chart.options.data[0].name = year.toString();
        chart.options.data[1].name = (year - 1).toString();
        chart.render();
    }
function updateChartData2(year, month) {
    var monthLabel = new Date(year, month - 1).toLocaleString('default', { month: 'long' });
    var yearLabel = year.trim();

    console.log("Selected Year:", yearLabel);
    console.log("Selected Month:", month);
    console.log("Selected Month Label:", monthLabel);

    function filterData(dataPoints, selectedMonth, selectedYear) {
        selectedYear = selectedYear.trim();
        selectedMonth = parseInt(selectedMonth);
        return dataPoints.filter(function(dataPoint) {
            var monthMatches = dataPoint.month === selectedMonth;
            var yearMatches = dataPoint.year.toString() === selectedYear;
            return monthMatches && yearMatches;
        });
    }

    chart2.options.title.text = "Monthly Revenue for " + monthLabel + " " + yearLabel;
    
    chart2.options.data[0].dataPoints = filterData(dataPoints_ctscan, month, year);
    chart2.options.data[1].dataPoints = filterData(dataPoints_diabetes_clinic, month, year);
    chart2.options.data[2].dataPoints = filterData(dataPoints_dietary, month, year);
    chart2.options.data[3].dataPoints = filterData(dataPoints_doctors_wing, month, year);
    chart2.options.data[4].dataPoints = filterData(dataPoints_emergency_room, month, year);
    chart2.options.data[5].dataPoints = filterData(dataPoints_eye_center, month, year);
    chart2.options.data[6].dataPoints = filterData(dataPoints_fifth_floor_b1, month, year);
    chart2.options.data[7].dataPoints = filterData(dataPoints_fifth_floor_b2, month, year);
    chart2.options.data[8].dataPoints = filterData(dataPoints_ghms, month, year);
    chart2.options.data[9].dataPoints = filterData(dataPoints_hearing_center, month, year);
    chart2.options.animationEnabled = true;
    chart2.render();
}

}
</script>

</body>
</html>
