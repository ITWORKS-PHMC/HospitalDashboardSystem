<?php
require 'connection.php';

// Fetch distinct years from the dashboard_census table
$yearQuery = "SELECT DISTINCT YEAR(transaction_date) AS year FROM dashboard_census";
$yearResult = mysqli_query($conn, $yearQuery);

// Get selected month from the URL parameter
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : date('m');

// Get selected year from the URL parameter
$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include("home.php")?>
    <link rel="stylesheet" href="style.css">
    <style>
        .bar {
            display: inline-block;
            width: 20px;
            background-color: blue;
            margin-right: 10px;
            position: relative;
        }

        .bar::after {
            content: '';
            display: block;
            background-color: red;
            position: absolute;
            bottom: 0;
        }
    </style>
</head>
<body>
   <div class="header" style="height:50px;">
    <h1>DASHBOARD</h1>
    <div class="button">
        <!-- Year dropdown form -->
        <form id="YearForm" action="dashboard.php" method="GET">
            <select id="yearDropdown" name="selected_year">
                <option value="">Select Year</option>
                <?php
                while ($yearRow = mysqli_fetch_assoc($yearResult)) {
                    ?>
                    <option value="<?php echo $yearRow['year']; ?>" <?php if ($selected_year == $yearRow['year']) echo 'selected'; ?>><?php echo $yearRow['year']; ?></option>
                    <?php
                }
                ?>
            </select>
            <input type="hidden" name="selected_month" value="<?php echo $selected_month; ?>">
        </form>

        <form id="monthForm">
            <select id="monthFilter" name="selected_month">
                <?php
                // Generate options for each month
                for($i = 1; $i <= 12; $i++) {
                    $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $month_name = date('F', mktime(0, 0, 0, $i, 1));
                    echo "<option value=\"$month\"";
                    if($selected_month == $month) echo 'selected';
                    echo ">$month_name</option>";
                }
                ?>
            </select>
        </form>
    </div>
</div>

<div class="numbers">
    <div class="boxtotalpatient">
        <p class="boxtitle1">Total Patient</p>
        <?php
        // SQL query to count total patient IDs for the selected year
        $sql = "SELECT COUNT(census_id) as totalcensus_id FROM dashboard_census WHERE YEAR(transaction_date) = $selected_year";
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Fetch the total patient count
            $row = $result->fetch_assoc();
            $totalPatients = $row["totalcensus_id"];

            // SQL query to get the target value for the selected year
            $sql2 = "SELECT SUM(target_value) as totaltarget FROM dashboard_target";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows > 0) {
                // Fetch the target value
                $row2 = $result2->fetch_assoc();
                $totalTarget = $row2["totaltarget"];
               echo "<script>console.log('$totalPatients');</script>";
                // Compare total patients with the target value
                if ($totalPatients >= $totalTarget) {
                    // Display total patients with green arrow
                    echo "<div class='result'>$totalPatients<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
                } else {
                    // Display total patients with red arrow
                    echo "<div class='result'><span class='red-arrow' style='float: left;'>&#8595;</span>$totalPatients</div>";
                }
            } else {
                echo "No target set for the selected year";
            }
        } else {
            echo "No patients for the selected year";
        }
        ?>
    </div>

    <div class="boxtotalbed">
        <p class="boxtitle">Total Beds vs Census</p>
        <?php
        // SQL query to select all visits
        $sql = "SELECT * FROM dashboard_census";

        $result = $conn->query($sql);

        // Variable to store the count of IPD visits
        $ipdCount = 0;

        if ($result->num_rows > 0) {
            // Loop through each row of the result set
            while ($row = $result->fetch_assoc()) {
                // Check if the department type is IPD and increment the count if true
                if ($row['patient_transaction_type'] === 'IPD') {
                    $ipdCount++;
                }
            }
            echo "<div class='result'>" . $ipdCount . "/3000"."</div>";
        } else {
            echo "0 results";
        }
        ?>
    </div>
    
    <div class="boxgraph">
        <!-- Container to load donut.php content -->
        <div id="donutContainer">
            <?php include 'donut.php'; ?>
        </div>
    </div>
</div>

<div class="graphs-bottom">
    <div class="line-graph">
        <?php include 'TotalPatientGraph.php'; ?>
        <?php //include 'example.php'; ?>
    </div>
</div>

<!-- JavaScript for AJAX -->
<script>
    // Function to update the content of donut.php using AJAX
    function updateDonut(selectedYear, selectedMonth) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("donutContainer").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "donut.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
        xhttp.send();
    }

    // Function to handle year selection change without refreshing the page
    document.getElementById("yearDropdown").addEventListener("change", function() {
        var selectedYear = this.value;
        var selectedMonth = document.getElementById("monthFilter").value;
        updateDonut(selectedYear, selectedMonth);
    });

    // Function to handle month selection change without refreshing the page
    document.getElementById("monthFilter").addEventListener("change", function() {
        var selectedYear = document.getElementById("yearDropdown").value;
        var selectedMonth = this.value;
        updateDonut(selectedYear, selectedMonth);
    });
</script>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
