<!DOCTYPE html>
<html>
<head>
    <title>Transaction Type Spline Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="chart-container" style="width: 750px; height: 500px;">
        <canvas id="myChart"></canvas>
    </div>

    <!-- Dropdown select element for selecting years -->
    <select id="yearDropdown">
        <option value="">Select Year</option>
        <?php
        require 'connection.php'; // Include your database connection script

        // Query distinct years from the database
        $sql_years = "SELECT DISTINCT YEAR(transaction_date) AS year FROM dashboard_census";
        $result_years = $conn->query($sql_years);
        if ($result_years->num_rows > 0) {
            while($row_year = $result_years->fetch_assoc()) {
                $year = $row_year["year"];
                echo "<option value=\"$year\">$year</option>";
            }
        }
        ?>
    </select>

<?php
// Initialize variables to store counts for each transaction type
$opd_counts = array();
$ipd_counts = array();
$er_counts = array();

$sql = "SELECT patient_transaction_type, YEAR(transaction_date) AS transaction_year, COUNT(*) AS transaction_count 
        FROM dashboard_census 
        WHERE patient_transaction_type IN ('OPD', 'IPD', 'ER')
        GROUP BY patient_transaction_type, transaction_year";
$result = $conn->query($sql);

// Iterate through each row in the result set
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Increment counts based on the patient_transaction_type and transaction_date
        switch($row["patient_transaction_type"]) {
            case "OPD":
                $opd_counts[$row["transaction_year"]] = $row["transaction_count"];
                break;
            case "IPD":
                $ipd_counts[$row["transaction_year"]] = $row["transaction_count"];
                break;
            case "ER":
                $er_counts[$row["transaction_year"]] = $row["transaction_count"];
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

// Prepare datasets for Chart.js
$datasets = array(
    array(
        'label' => 'OPD',
        'data' => array_values($opd_counts),
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        'tension' => 0.4,
        'fill' => false
    ),
    array(
        'label' => 'IPD',
        'data' => array_values($ipd_counts),
        'borderColor' => 'rgba(54, 162, 235, 1)',
        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
        'tension' => 0.4,
        'fill' => false
    ),
    array(
        'label' => 'ER',
        'data' => array_values($er_counts),
        'borderColor' => 'rgba(255, 206, 86, 1)',
        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
        'tension' => 0.4,
        'fill' => false
    )
);
?>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart;

    // Function to initialize chart with data
    function initializeChart(selectedYear) {
        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [selectedYear, selectedYear - 1, selectedYear - 2],
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
    }

    // Function to update chart based on selected year
    function updateChart(selectedYear) {
        // Update chart with data for the selected year
        myChart.data.labels = [selectedYear, selectedYear - 1, selectedYear - 2];
        myChart.data.datasets.forEach(function(dataset, index) {
            switch (dataset.label) {
                case 'OPD':
                    dataset.data = [
                        <?php 
                            echo isset($opd_counts[$selectedYear]) ? $opd_counts[$selectedYear] : 0;
                            echo isset($opd_counts[$selectedYear - 1]) ? ',' . $opd_counts[$selectedYear - 1] : ',0';
                            echo isset($opd_counts[$selectedYear - 2]) ? ',' . $opd_counts[$selectedYear - 2] : ',0';
                        ?> 
                    ];
                    break;
                case 'IPD':
                    dataset.data = [
                        <?php 
                            echo isset($ipd_counts[$selectedYear]) ? $ipd_counts[$selectedYear] : 0;
                            echo isset($ipd_counts[$selectedYear - 1]) ? ',' . $ipd_counts[$selectedYear - 1] : ',0';
                            echo isset($ipd_counts[$selectedYear - 2]) ? ',' . $ipd_counts[$selectedYear - 2] : ',0';
                        ?> 
                    ];
                    break;
                case 'ER':
                    dataset.data = [
                        <?php 
                            echo isset($er_counts[$selectedYear]) ? $er_counts[$selectedYear] : 0;
                            echo isset($er_counts[$selectedYear - 1]) ? ',' . $er_counts[$selectedYear - 1] : ',0';
                            echo isset($er_counts[$selectedYear - 2]) ? ',' . $er_counts[$selectedYear - 2] : ',0';
                        ?> 
                    ];
                    break;
            }
        });
        myChart.update();
    }
    
    // Event listener for dropdown change
    document.getElementById('yearDropdown').addEventListener('change', function() {
        var selectedYear = this.value;
        if (selectedYear !== '') {
            // Update chart based on selected year
            updateChart(selectedYear);
        }
    });

    // Initialize chart with initial data
    var selectedYear = '<?php echo isset($_GET["yearDropdown"]) ? $_GET["yearDropdown"] : ""; ?>';
    initializeChart(selectedYear);

</script>

</body>
</html>
