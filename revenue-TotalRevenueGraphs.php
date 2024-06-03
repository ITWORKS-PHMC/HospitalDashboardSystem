<?php
require 'old-connection.php';

// Fetch data for chart1 
$sql = "SELECT YEAR(revenue_date) AS year, MONTH(revenue_date) AS month, SUM(revenue_totalAmount) AS total 
        FROM dashboard_revenue
        GROUP BY YEAR(revenue_date), MONTH(revenue_date)";
$result = mysqli_query($conn, $sql);

$years = array();
$dataPoints = array();
$dataPoints_previousYear = array();

$currentYear = date('Y');
$previousYear = $currentYear - 1;
for ($i = 1; $i <= 12; $i++) {
    $dataPoints[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
    $dataPoints_previousYear[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
}

while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $month = $row['month'];
    $total = $row['total'];
    $years[$year][$month] = $total;
}

$sql_currentYear = "SELECT MONTH(revenue_date) AS month, SUM(revenue_totalAmount) AS total 
                    FROM dashboard_revenue
                    WHERE YEAR(revenue_date) = $currentYear
                    GROUP BY MONTH(revenue_date)";
$result_currentYear = mysqli_query($conn, $sql_currentYear);

$dataPoints_currentYear = array_fill(0, 12, array("y" => 0, "label" => ""));

for ($i = 0; $i < 12; $i++) {
    $label = date("F", mktime(0, 0, 0, $i + 1, 1));
    $dataPoints_currentYear[$i]["label"] = $label;
}

while ($row = mysqli_fetch_assoc($result_currentYear)) {
    $month = $row['month'] - 1;
    $total = $row['total'];
    $dataPoints_currentYear[$month]["y"] = $total;
}


// Fetch data for chart2
$selectedYear = date('Y');

if (isset($_GET['yearDropdown'])) {
    $selectedYear = $_GET['yearDropdown'];
}

$sql_departments = "SELECT DISTINCT revenue_department FROM dashboard_revenue";
$result_departments = $conn->query($sql_departments);
$departments = array();
while ($row = $result_departments->fetch_assoc()) {
    $departments[] = $row['revenue_department'];
}

$sql_chart2 = "SELECT DISTINCT YEAR(revenue_date) AS year, MONTH(revenue_date) AS month, ";
foreach ($departments as $department) {
    $departmentAlias = str_replace(' ', '_', $department);
    $sql_chart2 .= "SUM(CASE WHEN revenue_department = '$department' THEN revenue_totalAmount ELSE 0 END) AS total_$departmentAlias, ";
}
$sql_chart2 = rtrim($sql_chart2, ', ');
$sql_chart2 .= " FROM dashboard_revenue GROUP BY YEAR(revenue_date), MONTH(revenue_date)";

$result_chart2 = $conn->query($sql_chart2);

$dataPointsDepartments = array();
foreach ($departments as $department) {
    $dataPointsDepartments[$department] = array();
}

if ($result_chart2->num_rows > 0) {
    while ($row = $result_chart2->fetch_assoc()) {
        $year = $row["year"];
        $month = $row["month"];
        $monthLabel = date("F", mktime(0, 0, 0, $month, 1));
        
        foreach ($departments as $department) {
            $departmentAlias = str_replace(' ', '_', $department);
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.0.0"></script>
</head>
<body>
<div class="charts-container" style="display: block;">
<div class="chart-1" style="flex: 1; display:flex;">
<canvas id="chartContainer" style="height: 370px; width: 1550px; box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></canvas>
</div>
</div>
<div class="chart-2" style="flex: 1; margin-top: 40px; display:flex;">
<canvas id="chartContainer2" style="height: 370px; width: 1550px; box-shadow: 0 2px 4px rgba(51, 104, 54, 0.767), 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;"></canvas>
</div>
</div>

<script>
window.onload = function () {
    
    var dataPoints_currentYear = <?php echo json_encode($dataPoints_currentYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPointsDepartments = <?php echo json_encode($dataPointsDepartments, JSON_NUMERIC_CHECK); ?>;
    function formatDataPoints(dataPoints) {
        return dataPoints.map(dp => ({x: dp.label, y: dp.y}));
    }

  var ctx = document.getElementById('chartContainer').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dataPoints_currentYear.map(dp => dp.label),
        datasets: [{
                label: '<?php echo $currentYear; ?>',
                data: formatDataPoints(dataPoints_currentYear),
                borderColor: 'green',
                fill: false
            },
            {
                label: '<?php echo $previousYear; ?>',
                data: formatDataPoints(dataPoints_previousYear),
                borderColor: 'red',
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        title: { // display datapoints with the use of syntax 'title' in options bracket
            display: true,
            text: 'Total Revenue for Year <?php echo $currentYear . $previousYear; ?>',
            fontSize: 20
        },
        tooltips: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function(tooltipItem, data) {
                    return '₱' + tooltipItem.yLabel.toLocaleString();
                }
            }
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Month'
                }
            },
            y: {
                display: true,
                title: {
                    display: true,
                    text: 'Revenue'
                },

            }
        },
        plugins: {
            title:{ // display title as style for chart 1 in plugins bracket
                display : true,
                text : 'Total Revenue for Year <?php echo $currentYear ." & ". $previousYear?>' 
            },
            legend: {
                position: "bottom",
            },
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'y'
                },
                zoom: {
                    wheel: {
                        enabled: true,
                    },
                    pinch: {
                        enabled: true
                    },
                    mode: 'xy',
                    onZoom: function({chart}) {
                        const minY = chart.scales.y.min;
                        if (minY < 0) {
                            chart.options.scales.y.min = 0;
                            chart.update();
                        }
                    }
                }
            },
        },
    }
});

    // CHART 2
    var ctx2 = document.getElementById('chartContainer2').getContext('2d');
    var datasets = [];

    <?php foreach ($departments as $department): ?>
        datasets.push({
            label: "<?php echo $department; ?>",
            data: formatDataPoints(dataPointsDepartments["<?php echo $department; ?>"]),
            backgroundColor: getRandomColor()
        });
    <?php endforeach; ?>

var chart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: dataPointsDepartments["<?php echo $departments[0]; ?>"].map(dp => dp.label),
        datasets: datasets
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Monthly Revenue per Department'
        },
        tooltips: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function(tooltipItem, data) {
                    return data.datasets[tooltipItem.datasetIndex].label + ': ₱' + tooltipItem.yLabel.toLocaleString();
                }
            }
        },
        scales: {
            x: {
                stacked: false, 
                display: true,
                title: {
                    display: true,
                    text: 'Month'
                }
            },
            y: {
                stacked: false,
                display: true,
                title: {
                    display: true,
                    text: 'Revenue',
                },
                min: 0
            }
        },
        plugins: {
            legend: {
                display : false
            },
            title:{ // display title as style for chart 2 in plugins bracket
                display : true,
                text : 'Monthly Revenue per Department' 
            },
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'xy'
                },
                zoom: {
                    wheel: {
                        enabled: true,
                    },
                    pinch: {
                        enabled: true
                    },
                    mode: 'xy',
                    onZoom: function({chart}) {
                        const minY = chart.scales.y.min;
                        if (minY < 0) {
                            chart.options.scales.y.min = 0;
                            chart.update();
                        }
                    }
                }
            }
        }
    }
});


    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
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

function updateChartData(year) { // update display datapoints for chart 1 by selected year and previous year 
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

    chart.data.datasets[0].data = newDataPointsCurrentYear.map(dp => dp.y);
    chart.data.datasets[1].data = newDataPointsPreviousYear.map(dp => dp.y);
    chart.options.title.text = "Total Revenue for Year " + year + " & " + (year - 1).toString(); // display new updated datapoints based on selected year with the previous year
    chart.data.labels = newDataPointsCurrentYear.map(dp => dp.label);
    chart.update();

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

    chart2.data.labels = [monthLabel];

    chart2.data.datasets = [];

    for (var department in dataPointsDepartments) {
        if (dataPointsDepartments.hasOwnProperty(department)) {
            chart2.data.datasets.push({
                label: department.replace(/_/g, ' '),
                data: filterData(dataPointsDepartments[department], month, year).map(dp => dp.y),
                backgroundColor: getRandomColor()
            });
        }
    }

    chart2.update();
}
}
</script>

</body>
</html>