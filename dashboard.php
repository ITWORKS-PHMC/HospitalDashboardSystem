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
    <p class="boxtitle1">Total Beds vs Census</p>
    <?php

    // SQL query to count total patient IDs
    $sql = "SELECT COUNT(id) as patient_id FROM patient";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<div class='result'>" . $row["patient_id"] ."</div>";
        }
    } else {
    echo "0 results";
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
    
    <div class="boxgraph"></div>

    </div>
</div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>