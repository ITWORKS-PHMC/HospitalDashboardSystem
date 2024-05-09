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
$sql_chart2 = "SELECT YEAR(census_date_admitted) AS year,".
              "SUM(CASE WHEN census_transaction_type = 'O' THEN 1 ELSE 0 END) AS total_opd,".
              "SUM(CASE WHEN census_transaction_type = 'I' THEN 1 ELSE 0 END) AS total_ipd,".
              "SUM(CASE WHEN census_transaction_type = 'E' THEN 1 ELSE 0 END) AS total_er,".
              "SUM(CASE WHEN census_department = 'X-RAY' THEN 1 ELSE 0 END) AS total_xray,".
              "SUM(CASE WHEN census_department = 'CSR DISPENSING' THEN 1 ELSE 0 END) AS total_csr_dispensing,".
              "SUM(CASE WHEN census_department = 'PULMONARY' THEN 1 ELSE 0 END) AS total_pulmonary,".
              "SUM(CASE WHEN census_department = 'EMERGENCY ROOM' THEN 1 ELSE 0 END) AS total_emergency_room,".
              "SUM(CASE WHEN census_department = 'MEDICAL RECORDS' THEN 1 ELSE 0 END) AS total_medical_records,".
              "SUM(CASE WHEN census_department = 'PHARMANCY DISPENSING' THEN 1 ELSE 0 END) AS total_pharmacy_dispensing,".
              "SUM(CASE WHEN census_department = 'ICU' THEN 1 ELSE 0 END) AS total_icu,".
              "SUM(CASE WHEN census_department = 'MRI' THEN 1 ELSE 0 END) AS total_mri,".
              "SUM(CASE WHEN census_department = 'ULTRASOUND' THEN 1 ELSE 0 END) AS total_ultrasound,".
              "SUM(CASE WHEN census_department = 'LABORATORY' THEN 1 ELSE 0 END) AS total_laboratory,".
              "SUM(CASE WHEN census_department = 'RAD. ONCO LINAC' THEN 1 ELSE 0 END) AS total_rad_onco_linac,".
              "SUM(CASE WHEN census_department = 'HEMODIALYSIS' THEN 1 ELSE 0 END) AS total_hemodialysis".
               " FROM dashboard_database".
               " GROUP BY YEAR(census_date_admitted)";

$result_chart2 = $conn->query($sql_chart2);
$dataPoints_opd = array();
$dataPoints_ipd = array();
$dataPoints_er = array();
$dataPoints_xray = array();
$dataPoints_csr_dispensing = array();
$dataPoints_pulmonary = array();
$dataPoints_emergency_room = array();
$dataPoints_medical_records = array();
$dataPoints_pharmacy_dispensing = array();
$dataPoints_icu = array();
$dataPoints_mri = array();
$dataPoints_ultrasound = array();
$dataPoints_laboratory = array();
$dataPoints_rad_onco_linac = array();
$dataPoints_hemodialysis = array();

// Check if any rows were returned for chart 2
if ($result_chart2->num_rows > 0) {
    // Loop through each row of data for chart 2
    while($row = $result_chart2->fetch_assoc()) {
        // Populate dataPoints arrays with fetched data for OPD, IPD, ER, and departments
        $year = $row["year"];
        $dataPoints_opd[] = array("y" => $row["total_opd"], "label" => $year);
        $dataPoints_ipd[] = array("y" => $row["total_ipd"], "label" => $year);
        $dataPoints_er[] = array("y" => $row["total_er"], "label" => $year);
        $dataPoints_xray[] = array("y" => $row["total_xray"], "label" => $year);
        $dataPoints_csr_dispensing[] = array("y" => $row["total_csr_dispensing"], "label" => $year);
        $dataPoints_pulmonary[] = array("y" => $row["total_pulmonary"], "label" => $year);
        $dataPoints_emergency_room[] = array("y" => $row["total_emergency_room"], "label" => $year);
        $dataPoints_medical_records[] = array("y" => $row["total_medical_records"], "label" => $year);
        $dataPoints_pharmacy_dispensing[] = array("y" => $row["total_pharmacy_dispensing"], "label" => $year);
        $dataPoints_icu[] = array("y" => $row["total_icu"], "label" => $year);
        $dataPoints_mri[] = array("y" => $row["total_mri"], "label" => $year);
        $dataPoints_ultrasound[] = array("y" => $row["total_ultrasound"], "label" => $year);
        $dataPoints_laboratory[] = array("y" => $row["total_laboratory"], "label" => $year);
        $dataPoints_rad_onco_linac[] = array("y" => $row["total_rad_onco_linac"], "label" => $year);
        $dataPoints_hemodialysis[] = array("y" => $row["total_hemodialysis"], "label" => $year);
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
<div class="charts-container" style="display: flex;">
<div class="chart-1" style="flex: 1; margin-right: 10px;">
<div id="chartContainer" style="height: 370px; width: 1200px;"></div>
</div>
<div class="chart-2" style="flex: 1; margin-left: 10px;">
<div id="chartContainer2" style="height: 370px; width: 610px;"></div>
</div>
</div>


<script>
window.onload = function () {
    var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_previousYear = <?php echo json_encode($dataPoints_previousYear, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_opd = <?php echo json_encode($dataPoints_opd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ipd = <?php echo json_encode($dataPoints_ipd, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_er = <?php echo json_encode($dataPoints_er, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_xray = <?php echo json_encode($dataPoints_xray, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_csr_dispensing = <?php echo json_encode($dataPoints_csr_dispensing, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_pulmonary = <?php echo json_encode($dataPoints_pulmonary, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_emergency_room = <?php echo json_encode($dataPoints_emergency_room, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_medical_records = <?php echo json_encode($dataPoints_medical_records, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_pharmacy_dispensing = <?php echo json_encode($dataPoints_pharmacy_dispensing, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_icu = <?php echo json_encode($dataPoints_icu, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_mri = <?php echo json_encode($dataPoints_mri, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_ultrasound = <?php echo json_encode($dataPoints_ultrasound, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_laboratory = <?php echo json_encode($dataPoints_laboratory, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_rad_onco_linac = <?php echo json_encode($dataPoints_rad_onco_linac, JSON_NUMERIC_CHECK); ?>;
    var dataPoints_hemodialysis = <?php echo json_encode($dataPoints_hemodialysis, JSON_NUMERIC_CHECK); ?>;
    
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
        },
        {
            type: "line",
            name: "X-RAY",
            showInLegend: true,
            color: "#000080",
            dataPoints: dataPoints_xray
        },
        {
            type: "line",
            name: "CSR DISPENSING",
            showInLegend: true,
            color: "teal",
            dataPoints: dataPoints_csr_dispensing
        },
        {
            type: "line",
            name: "PULMONARY",
            showInLegend: true,
            color: "yellow",
            dataPoints: dataPoints_pulmonary
        },
        {
            type: "line",
            name: "EMERGENCY ROOM",
            showInLegend: true,
            color: "green",
            dataPoints: dataPoints_emergency_room
        },
        {
            type: "line",
            name: "MEDICAL RECORDS",
            showInLegend: true,
            color: "blue",
            dataPoints: dataPoints_medical_records
        },
        {
            type: "line",
            name: "PHARMANCY DISPENSING",
            showInLegend: true,
            color: "purple",
            dataPoints: dataPoints_pharmacy_dispensing
        },
        {
            type: "line",
            name: "ICU",
            showInLegend: true,
            color: "brown",
            dataPoints: dataPoints_icu
        },
        {
            type: "line",
            name: "MRI",
            showInLegend: true,
            color: "pink",
            dataPoints: dataPoints_mri
        },
        {
            type: "line",
            name: "ULTRASOUND",
            showInLegend: true,
            color: "cyan",
            dataPoints: dataPoints_ultrasound
        },
        {
            type: "line",
            name: "LABORATORY",
            showInLegend: true,
            color: "magenta",
            dataPoints: dataPoints_laboratory
        },
        {
            type: "line",
            name: "RAD. ONCO LINAC",
            showInLegend: true,
            color: "lime",
            dataPoints: dataPoints_rad_onco_linac
        },
        {
            type: "line",
            name: "HEMODIALYSIS",
            showInLegend: true,
            color: "aqua",
            dataPoints: dataPoints_rad_onco_linac
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
    var dataPoints_xray_filtered = <?php echo json_encode($dataPoints_xray, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_csr_dispensing_filtered = <?php echo json_encode($dataPoints_csr_dispensing, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_pulmonary_filtered = <?php echo json_encode($dataPoints_pulmonary, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_emergency_room_filtered = <?php echo json_encode($dataPoints_emergency_room, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_medical_records_filtered = <?php echo json_encode($dataPoints_medical_records, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_pharmacy_dispensing_filtered = <?php echo json_encode($dataPoints_pharmacy_dispensing, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_icu_filtered = <?php echo json_encode($dataPoints_icu, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_mri_filtered = <?php echo json_encode($dataPoints_mri, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_ultrasound_filtered = <?php echo json_encode($dataPoints_ultrasound, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_laboratory_filtered = <?php echo json_encode($dataPoints_laboratory, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_rad_onco_linac_filtered = <?php echo json_encode($dataPoints_xray, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    var dataPoints_hemodialysis_filtered = <?php echo json_encode($dataPoints_hemodialysis, JSON_NUMERIC_CHECK); ?>.filter(function(dataPoint) {
        return dataPoint.label == selectedYear || dataPoint.label == (selectedYear - 1) || dataPoint.label == (selectedYear - 2);
    });
    chart2.options.title.text = "Yearly OPD, IPD, and ER Data for " + year + ", " + (year - 1) + ", and " + (year - 2);
    chart2.options.data[0].dataPoints = dataPoints_opd_filtered;
    chart2.options.data[1].dataPoints = dataPoints_ipd_filtered;
    chart2.options.data[2].dataPoints = dataPoints_er_filtered;
    chart2.options.data[2].dataPoints = dataPoints_xray_filtered;
    chart2.options.data[4].dataPoints = dataPoints_csr_dispensing_filtered;
    chart2.options.data[5].dataPoints = dataPoints_pulmonary_filtered;
    chart2.options.data[6].dataPoints = dataPoints_emergency_room_filtered;
    chart2.options.data[7].dataPoints = dataPoints_medical_records_filtered;
    chart2.options.data[8].dataPoints = dataPoints_pharmacy_dispensing_filtered;
    chart2.options.data[9].dataPoints = dataPoints_icu_filtered;
    chart2.options.data[10].dataPoints = dataPoints_mri_filtered;
    chart2.options.data[11].dataPoints = dataPoints_ultrasound_filtered;
    chart2.options.data[12].dataPoints = dataPoints_laboratory_filtered;
    chart2.options.data[13].dataPoints = dataPoints_rad_onco_linac_filtered;
    chart2.options.data[14].dataPoints = dataPoints_hemodialysis_filtered;
    chart2.render();
}
}
</script>

</body>
</html>
