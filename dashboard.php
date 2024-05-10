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
    <?php include("./lib/nav-bar.php")?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <div class="header" style="height:50px;">
    <div class="button" style="margin-left: auto;">
        <!-- Year dropdown form -->
        <form id="YearForm" action="dashboard.php" method="GET">
            <select class="Filter-button" id="yearDropdown" name="selected_year" >
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
            <select class = "Filter-button" id="monthFilter" name="selected_month">
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
<div class="dashboard-content" style="margin-left:30px;">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 20px;">
        <div class="Number-box" style="grid-column: 1 / 2;">
            <div class="boxtotalpatient">
                <p class="boxtitle1">Total Patient</p>
                <div id="boxtotalpatient">
                    <?php include 'totalpatient.php'; ?>
                </div>
            </div>
            <div class="boxtotalbed">
                <p class="boxtitle">Total Beds vs Census</p>
                <div id="boxtotalIPD">
                    <?php include 'totalbed.php'; ?>
                </div>
            </div>
        </div>
        <div class="Meter-container" style="grid-column: 2 / 3;">
            <div id="donutContainer">
                <?php include 'donut.php'; ?>
            </div>
        </div>
        <div class="line-graphs-container" style="grid-column: 1 / 3;">
            <div class="line-graph">
                <?php include 'TotalPatientGraph.php'; ?>
            </div>
        </div>
    </div>
</div>



<!-- JavaScript for AJAX -->
<script>
function updateDonut(selectedYear, selectedMonth) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Update donutContainer with the new content
            document.getElementById("donutContainer").innerHTML = this.responseText;
            // Extract updated percentages from the response content
            var opdpercentage = parseFloat(document.getElementById("opdPercentage").innerText);
            var ipdPercentage = parseFloat(document.getElementById("ipdPercentage").innerText);
            var erPercentage = parseFloat(document.getElementById("erPercentage").innerText);
            // Call generateArrow() with updated data after content is updated
            generateArrow(opdpercentage, ipdPercentage, erPercentage);
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
        updateTotalPatients(selectedYear, selectedMonth); // Call to update total patients
        updateTotalIPD(selectedYear, selectedMonth); // Call to update IPD
    });
    // Function to handle month selection change without refreshing the page
    document.getElementById("monthFilter").addEventListener("change", function() {
        var selectedYear = document.getElementById("yearDropdown").value;
        var selectedMonth = this.value;
        updateDonut(selectedYear, selectedMonth);
        updateTotalPatients(selectedYear, selectedMonth); // Call to update total patients
        updateTotalIPD(selectedYear, selectedMonth); // Call to update IPD
    });
// Function to update the total patient count using AJAX
function updateTotalPatients(selectedYear, selectedMonth) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("boxtotalpatient").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "totalpatient.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
    xhttp.send();
}
// Function to update the total IPD count using AJAX
function updateTotalIPD(selectedYear, selectedMonth) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("boxtotalIPD").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "totalbed.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
    xhttp.send();
}
</script>
<footer></footer>
</body>
</html>

