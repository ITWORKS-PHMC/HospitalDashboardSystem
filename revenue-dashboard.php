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
        <form id="YearForm" action="revenue-dashboard.php" method="GET">
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
        <form id="deptForm" >
        <select class="Filter-button" id="deptFilter" name="selected_department">
            <option value="CT-SCAN">CT-SCAN</option>
            <option value="DIABETES CLINIC">DIABETES CLINIC</option>
            <option value="DIETARY">DIETARY</option>
            <option value="DOCTORS WING">DOCTORS WING</option>
            <option value="EMERGENCY ROOM">EMERGENCY ROOM</option>
            <option value="EYE CENTER">EYE CENTER</option>
            <option value="FIFTH FLOOR WING B1">FIFTH FLOOR WING B1</option>
            <option value="FIFTH FLOOR WING B2">FIFTH FLOOR WING B2</option>
            <option value="GHMS 2">GHMS 2</option>
            <option value="HEARING CENTER">HEARING CENTER</option>
        </select>
    </form>
    </div>
</div>
<div class="dashboard-content" style="margin-left:30px;">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 20px;">
        <div class="Number-box" style="grid-column: 1 / 2;">
            <div class="boxtotalpatient">
                <p class="boxtitle1">Total Revenue</p>
                <div id="boxtotalpatient">
                    <?php include 'revenue-totalpatient.php'; ?>
                </div>
            </div>
            <div class="boxtotalbed">
                <p class="boxtitle" style="left: 25px;">Total Revenue vs Target</p>
                <div id="boxtotalIPD">
                    <?php include 'revenue-totalbed.php'; ?>
                </div>
            </div>
        </div>
        <div class="Bar-graphs-container" style="grid-column: 2 / 3;">
            <div class="line-graph">
                <?php include 'revenue-TotalPatientGraph.php'; ?>
            </div>
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
            var xrayPercentage = parseFloat(document.getElementById("xrayPercentage").innerText);
            var mriPercentage = parseFloat(document.getElementById("mriPercentage").innerText);
            var pulmonaryPercentage = parseFloat(document.getElementById("pulmonaryPercentage").innerText);
            var ultrasoundPercentage = parseFloat(document.getElementById("ultrasoundPercentage").innerText);
            var icuPercentage = parseFloat(document.getElementById("icuPercentage").innerText);
            var laboratoryPercentage = parseFloat(document.getElementById("laboratoryPercentage").innerText);
            var csrPercentage = parseFloat(document.getElementById("csrPercentage").innerText);
            // Call generateArrow() with updated data after content is updated
            generateArrow(opdpercentage, ipdPercentage, erPercentage, xrayPercentage, mriPercentage. pulmonaryPercentage, ultrasoundPercentage, icuPercentage, laboratoryPercentage, csrPercentage);
        }
    };
    xhttp.open("GET", "revenue-donut.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
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
    xhttp.open("GET", "revenue-totalpatient.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
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
    xhttp.open("GET", "revenue-totalbed.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
    xhttp.send();
}
</script>
<footer></footer>
</body>
</html>

