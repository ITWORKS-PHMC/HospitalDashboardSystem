<?php
// Require the database connection file
require 'connection.php';

// Fetch data from the database for the last three years
$sql = "SELECT YEAR(transaction_date) AS year, 
               SUM(CASE WHEN patient_transaction_type = 'OPD' THEN 1 ELSE 0 END) AS OPD,
               SUM(CASE WHEN patient_transaction_type = 'IPD' THEN 1 ELSE 0 END) AS IPD,
               SUM(CASE WHEN patient_transaction_type = 'ER' THEN 1 ELSE 0 END) AS ER
        FROM dashboard_census 
        WHERE YEAR(transaction_date) >= YEAR(CURRENT_DATE) - 2
        GROUP BY YEAR(transaction_date)";
$result = mysqli_query($conn, $sql);

// Initialize an array to hold data points
$dataPoints = array();

// Initialize data points for the last three years
$currentYear = date('Y');
for ($i = $currentYear - 2; $i <= $currentYear; $i++) {
    // Each year should have initial values for departments
    $dataPoints[$i] = array("label" => (string)$i, "OPD" => 0, "IPD" => 0, "ER" => 0);
}

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $OPD = $row['OPD'];
    $IPD = $row['IPD'];
    $ER = $row['ER'];
    
    // Assign data points directly to each department
    $dataPoints[$year]['OPD'] = $OPD;
    $dataPoints[$year]['IPD'] = $IPD;
    $dataPoints[$year]['ER'] = $ER;
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
                    text: "Total Patients by Department for Last Three Years"
                },
                axisY: {
                    title: "Number of Patients"
                },
                data: [
                    <?php
                    foreach (['OPD', 'IPD', 'ER'] as $department) {
                        echo "{";
                        echo "type: 'line',";
                        echo "showInLegend: true,";
                        echo "name: '$department',";
                        echo "dataPoints: [";
                        
                        // Loop through data points and add them to the series
                        foreach ($dataPoints as $dp) {
                            echo "{";
                            echo "y: " . $dp[$department] . ",";
                            echo "label: '" . $dp['label'] . "'";
                            echo "},";
                        }
                        
                        echo "]";
                        echo "},";
                    }
                    ?>
                ]
            });
            chart.render();
        }
    </script>
</head>
<body>
    <div id="chartContainer" style="height: 370px; width: 20%;"></div>
    <?php include'yearlygraph.php';?>
</body>
</html>

