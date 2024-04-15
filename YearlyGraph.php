<!DOCTYPE html>
<html>
<head>
    <title>Transaction Type Spline Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="chart-container" style="width: 750px; height: 900px;">
        <canvas id="myChart"></canvas>
    </div>

<?php
require 'connection.php'; // Make sure to include your database connection script here
    
// Initialize variables to store counts for each transaction type
$opd_counts = array();
$ipd_counts = array();
$er_counts = array();

$sql = "SELECT * FROM dashboard_census";
$result = $conn->query($sql);

// Iterate through each row in the result set
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Increment counts based on the patient_transaction_type and transaction_date
        $year = date("Y", strtotime($row["transaction_date"])); // Extract year from transaction_date
        switch($row["patient_transaction_type"]) {
            case "OPD":
                $opd_counts[$year] = ($opd_counts[$year] ?? 0) + 1;
                break;
            case "IPD":
                $ipd_counts[$year] = ($ipd_counts[$year] ?? 0) + 1;
                break;
            case "ER":
                $er_counts[$year] = ($er_counts[$year] ?? 0) + 1;
                break;
        }
    }
}

// Count all years in the range of available data
$earliest_year = min(array_keys($opd_counts + $ipd_counts + $er_counts));
$current_year = date("Y");
$all_years = range($earliest_year, $current_year);

// Fill in missing years with zero counts
foreach ($all_years as $year) {
    $opd_counts[$year] = $opd_counts[$year] ?? 0;
    $ipd_counts[$year] = $ipd_counts[$year] ?? 0;
    $er_counts[$year] = $er_counts[$year] ?? 0;
}

// Sort the arrays by keys (years) in ascending order
ksort($opd_counts);
ksort($ipd_counts);
ksort($er_counts);

// Prepare datasets for Chart.js
$datasets = array();
if (!empty($opd_counts)) {
    $datasets[] = array(
        'label' => 'OPD',
        'data' => array_values($opd_counts),
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        'tension' => 0.4,
        'fill' => false
    );
}
if (!empty($ipd_counts)) {
    $datasets[] = array(
        'label' => 'IPD',
        'data' => array_values($ipd_counts),
        'borderColor' => 'rgba(54, 162, 235, 1)',
        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
        'tension' => 0.4,
        'fill' => false
    );
}
if (!empty($er_counts)) {
    $datasets[] = array(
        'label' => 'ER',
        'data' => array_values($er_counts),
        'borderColor' => 'rgba(255, 206, 86, 1)',
        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
        'tension' => 0.4,
        'fill' => false
    );
}
?>



    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($opd_counts + $ipd_counts + $er_counts)); ?>,
                datasets: <?php echo json_encode($datasets); ?>
            },
            options: {
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Transaction Count'
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
