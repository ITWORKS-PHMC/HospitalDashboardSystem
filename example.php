<?php
require 'connection.php';

// Fetch data from the database
$sql = "SELECT YEAR(transaction_date) AS year, MONTH(transaction_date) AS month, COUNT(census_id) AS total 
        FROM dashboard_census 
        GROUP BY YEAR(transaction_date), MONTH(transaction_date)";
$result = mysqli_query($conn, $sql);

$years = array();
$dataPoints = array();
$dataPoints_previousYear = array(); // Added array for previous year's data
$dataPoints_selectedYear = array(); // Added array for selected year's data

// Initialize data points for all months with count zero for the selected year and previous year
$currentYear = date('Y');
$previousYear = $currentYear - 1;
$selectedYear = ;
for ($i = 1; $i <= 12; $i++) {
    $dataPoints[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
    $dataPoints_previousYear[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
    $dataPoints_previousYear[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
    $dataPoints_selectedYear[] = array("y" => 0, "label" => date("F", mktime(0, 0, 0, $i, 1)));
}

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $month = $row['month'];
    $total = $row['total'];
    $years[$year][$month] = $total;
    // Populate dataPoints arrays for current and previous years
    if ($year == $currentYear) {
        $dataPoints[$month - 1]["y"] = $total;
    } elseif ($year == $previousYear) {
        $dataPoints_previousYear[$month - 1]["y"] = $total;
    }
}

// Query to fetch data for chart 2 (yearly data for OPD, IPD, and ER)
$sql_chart2 = "SELECT YEAR(transaction_date) AS year,
       SUM(CASE WHEN patient_transaction_type = 'OPD' THEN 1 ELSE 0 END) AS total_opd,
       SUM(CASE WHEN patient_transaction_type = 'IPD' THEN 1 ELSE 0 END) AS total_ipd,
       SUM(CASE WHEN patient_transaction_type = 'ER' THEN 1 ELSE 0 END) AS total_er
FROM dashboard_census
WHERE YEAR(transaction_date) BETWEEN YEAR(NOW()) - 2 AND YEAR(NOW())
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
<script src="lib/graphs.js"></script>
<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer1", {
        animationEnabled: true,
        title: {
            text: "Total Patient for Year <?php echo $currentYear; ?>" // Default title
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
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            showInLegend: true,
            name: "<?php echo $previousYear; ?>",
            color: "red",
            dataPoints: <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>
        }],
        toolTipContent: "{name}: {y} - {x}",
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
         legend: {
            verticalAlign: "center",
            horizontalAlign: "right",
            
        },
        data: [{
            type: "line",
            name: "OPD",
            showInLegend: true,
            color: "red",
            dataPoints: <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            name: "IPD",
            showInLegend: true,
            color: "orange",
            dataPoints: <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>
        },
        {
            type: "line",
            name: "ER",
            showInLegend: true,
            color: "green",
            dataPoints: <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>
        }]
    });
    
    chart2.render();


    function updateChartData(year) {
    // Update chart1 (Total Patient Chart) data
    var newDataPointsCurrentYear = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var newDataPointsPreviousYear = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
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
    
    }
function updateChart2Data(year) {
    // Fetch data for the selected year and the two previous years
    var selectedYearData = <?php echo json_encode($years[$year] ?? [], JSON_NUMERIC_CHECK); ?>;
    var previousYearData = <?php echo json_encode($years[$year - 1] ?? [], JSON_NUMERIC_CHECK); ?>;
    var twoYearsAgoData = <?php echo json_encode($years[$year - 2] ?? [], JSON_NUMERIC_CHECK); ?>;
    
    // Function to calculate the total count for a given year's data
    function calculateTotal(data) {
        var total = 0;
        for (var month in data) {
            if (data.hasOwnProperty(month)) {
                total += data[month];
            }
        }
        return total;
    }

    // Calculate the total counts for each transaction type for each year
    var selectedYearTotalOPD = calculateTotal(selectedYearData['OPD'] ?? {});
    var selectedYearTotalIPD = calculateTotal(selectedYearData['IPD'] ?? {});
    var selectedYearTotalER = calculateTotal(selectedYearData['ER'] ?? {});

    var previousYearTotalOPD = calculateTotal(previousYearData['OPD'] ?? {});
    var previousYearTotalIPD = calculateTotal(previousYearData['IPD'] ?? {});
    var previousYearTotalER = calculateTotal(previousYearData['ER'] ?? {});

    var twoYearsAgoTotalOPD = calculateTotal(twoYearsAgoData['OPD'] ?? {});
    var twoYearsAgoTotalIPD = calculateTotal(twoYearsAgoData['IPD'] ?? {});
    var twoYearsAgoTotalER = calculateTotal(twoYearsAgoData['ER'] ?? {});

    // Update chart2 (Yearly Chart - OPD, IPD, ER) data
    var newDataPointsOpd = [
        { y: selectedYearTotalOPD, label: "Selected Year" },
        { y: previousYearTotalOPD, label: "Previous Year" },
        { y: twoYearsAgoTotalOPD, label: "Two Years Ago" }
    ];
    var newDataPointsIpd = [
        { y: selectedYearTotalIPD, label: "Selected Year" },
        { y: previousYearTotalIPD, label: "Previous Year" },
        { y: twoYearsAgoTotalIPD, label: "Two Years Ago" }
    ];
    var newDataPointsEr = [
        { y: selectedYearTotalER, label: "Selected Year" },
        { y: previousYearTotalER, label: "Previous Year" },
        { y: twoYearsAgoTotalER, label: "Two Years Ago" }
    ];

    var title = "Yearly OPD, IPD, and ER Data (" + year + ")";
    chart2.options.title.text = title;
    chart2.options.data[0].dataPoints = newDataPointsOpd;
    chart2.options.data[1].dataPoints = newDataPointsIpd;
    chart2.options.data[2].dataPoints = newDataPointsEr;

    chart2.render();
}


        // Function to handle dropdown selection change
    document.getElementById("yearDropdown").onchange = function() {
        var selectedYear = this.value;
        updateChartData(selectedYear);
        updateChart2Data(selectedYear);
    };

};
</script>
</head>
<body>

<div id="chartContainer1" style="height: 370px; display: relative;"></div>
<div id="chartContainer2" style="height: 370px; display: relative;"></div> <!-- Changed chartContainer ID -->

</body>
</html>
