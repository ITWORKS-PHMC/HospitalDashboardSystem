<?php
require 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    <div class="header">
        <h1>DASHBOARD</h1>
        <div class="button">
        <button>Year Filter</button>
        <button>Month Filter</button>
        </div>
    </div>

    <div class="numbers">
    <div class="boxtotalpatient">
    <p class="boxtitle1">Total Patient</p>
    <?php

    // SQL query to count total patient IDs
$sql = "SELECT COUNT(census_id) as totalcensus_id FROM dashboard_census";
$result = $conn->query($sql);
$sql2 = "SELECT SUM(target_value) as totaltarget FROM dashboard_target";
$result2 = $conn->query($sql2);

if ($result->num_rows > 0 && $result2->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Reset the pointer of $result2 back to the beginning for each iteration of the outer loop
        $result2->data_seek(0);

        while ($row2 = $result2->fetch_assoc()) {
            if ($row["totalcensus_id"] >= $row2["totaltarget"]) {
                echo "<div class='result'>" . $row["totalcensus_id"] . "<span class='green-arrow' style='float: right;'>&#8593;</span></div>";
            } else { // If the total count is less than 14, display down arrow
                echo "<div class='result'><span class='red-arrow' style='float: left;'>&#8595;</span>" . $row["totalcensus_id"] . "</div>";
            }
        }
    }
} else {
    echo "No patients";
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
        <?php
            include 'donut.php';
        ?> 
    </div>
        </div>
<div class="graphs-bottom">
    <div class="line-graph">
        <?php include'TotalPatientGraph.php';?>
    </div>
    <div class="yearly-graph">
        <?php include'YearlyGraph.php';?>
    </div>
</div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>