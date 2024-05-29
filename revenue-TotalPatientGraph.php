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


// Fetch departments from the database
$sql_departments = "SELECT DISTINCT revenue_department FROM dashboard_revenue";
$result_departments = $conn->query($sql_departments);
$departments = array();
while ($row = $result_departments->fetch_assoc()) {
    $departments[] = $row['revenue_department'];
}

// Constructing dynamic SQL query based on available departments
$sql_chart2 = "SELECT DISTINCT YEAR(revenue_date) AS year, MONTH(revenue_date) AS month, ";
foreach ($departments as $department) {
    $departmentAlias = str_replace(' ', '_', $department); // Replace spaces with underscores for alias
    $sql_chart2 .= "SUM(CASE WHEN revenue_department = '$department' THEN revenue_totalAmount ELSE 0 END) AS total_$departmentAlias, ";
}
$sql_chart2 = rtrim($sql_chart2, ', '); // Remove the trailing comma
$sql_chart2 .= " FROM dashboard_revenue GROUP BY YEAR(revenue_date), MONTH(revenue_date)";


$result_chart2 = $conn->query($sql_chart2);

$dataPointsDepartments = array();
foreach ($departments as $department) {
    $dataPointsDepartments[$department] = array();
}

// Check if any rows were returned for chart 2
if ($result_chart2->num_rows > 0) {
    // Loop through each row of data for chart 2
    while ($row = $result_chart2->fetch_assoc()) {
        $year = $row["year"];
        $month = $row["month"];
        $monthLabel = date("F", mktime(0, 0, 0, $month, 1)); // Get the name of the month
        
        // Populate data points arrays for each department
        foreach ($departments as $department) {
            $departmentAlias = str_replace(' ', '_', $department); // Use the same alias as in SQL query
            $dataPointsDepartments[$department][] = array(
                "y" => $row["total_$departmentAlias"],
                "label" => $monthLabel,
                "year" => $year,
                "month" => $month
            );
        }
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
  <div style="margin-top: 10px;">

    </div>
</div>
</div>


<script>
    
window.onload = function () {
    var dataPoints_currentYear = <?php echo json_encode($dataPoints_currentYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPointsDepartments = <?php echo json_encode($dataPointsDepartments, JSON_NUMERIC_CHECK); ?>;

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
            },
        data: []
    });

    // Add data for each department
    <?php foreach ($departments as $department): ?>
        chart2.options.data.push({
            type: "column",
            name: "<?php echo $department; ?>",
            dataPoints: dataPointsDepartments["<?php echo $department; ?>"]
        });
    <?php endforeach; ?>

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

    chart2.options.data = [];

    for (var department in dataPointsDepartments) {
        if (dataPointsDepartments.hasOwnProperty(department)) {
            chart2.options.data.push({
                type: "column",
                name: department.replace(/_/g, ' '), 
                showInLegend: true,
                dataPoints: filterData(dataPointsDepartments[department], month, year)
            });
        }
    }

    chart2.options.animationEnabled = true;
    chart2.render();
}

}
</script>

</body>
</html>
