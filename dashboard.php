<?php
require 'connection.php';

// Fetch distinct years from the dashboard_census table
$yearQuery = "SELECT DISTINCT YEAR(datetimeadmitted) AS year FROM rptCensus";
$yearResult = sqlsrv_query($conn, $yearQuery);

// Get selected month from the URL parameter
$selected_month = isset($_GET['selected_month']) ? intval($_GET['selected_month']) : date('m');


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
    <script src="./lib/script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .nav-buttonCensus {
            background-color: rgba(0, 56, 68, 1);
            color: white; 
            border: none;
            cursor: pointer;
    }
</style>
<body>
   <div class="header" style="height:50px; display: flex; align-items: center;flex-wrap: wrap;">
    <div class="button" style="display: flex; align-items: center; margin-left:1375px;">
            <button onclick="nextPage()" style="margin-top:12px ;margin-right:5px;height: 30px;width: 200px;text-align: center; color: rgb(255, 255, 255); background-color: rgba(0, 56, 68, 1); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 4px 10px rgba(0, 0, 0, 0.1);border-radius: 25px;cursor: pointer;">Open Departments</button>
        <!-- Year dropdown form -->
        <form id="YearForm" action="dashboard.php" method="GET">
            <select class="Filter-button" id="yearDropdown" name="selected_year" >
                <?php
                while ($yearRow = sqlsrv_fetch_array($yearResult,SQLSRV_FETCH_ASSOC)) {
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
        <!---Display no. of Patients and Targets -->
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
        <!-- Display OPD, IPD, ER Meter graphs--->
        <div class="Meter-container" id="Meter-containers"style="grid-column: 2 / 3;"> 
            <div id="donutContainer">
                <?php include 'donut.php'; ?>
            </div>
        </div>
        <!-- Display 13 departments Meter graphs--->
        <div class="Depts-container" id="depts-Containers"style="display:none; grid-column: 2 / 3;"> 
            <div id="donutContainer2">
                <?php  include 'depts-meter-graphs.php'; ?>
            </div>
        </div>
        <!-- Display Total Patients graph & OPD, IPD, ER Yearly graphs--->
        <div class="line-graphs-container" id="line-graphs" style="grid-column: 1 / 3;">
            <div class="line-graph">
                <?php include 'TotalPatientGraph.php'; ?>
            </div> 
        </div>
    </div>
</div>

<!-- JavaScript for AJAX -->
<script>

function nextPage(){
     var meterContainer = document.getElementById('Meter-containers');
     var deptContainer = document.getElementById('depts-Containers');
     var lineContainer = document.getElementById('line-graphs');

        if (meterContainer.style.display === 'none') {
            meterContainer.style.display = 'grid';
            deptContainer.style.display = 'none';
            lineContainer.style.display='grid';
        } else {
            meterContainer.style.display = 'none';
            deptContainer.style.display = 'grid';
            lineContainer.style.display='none';
        }
}
function updateDonut(selectedYear, selectedMonth) { // Function for updating donuts by monthly filter 
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
function updateDonut2(selectedYear, selectedMonth) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("donutContainer2").innerHTML = this.responseText;
            var departmentPercentages = JSON.parse(document.getElementById("depts-percentage").innerText);
            generateArrows(departmentPercentages);
        }
    };
    xhttp.open("GET", "depts-meter-graphs.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
    xhttp.send();
}
    // Function to handle year selection change without refreshing the page
    document.getElementById("yearDropdown").addEventListener("change", function() {
        var selectedYear = this.value;
        var selectedMonth = document.getElementById("monthFilter").value;
        updateDonut(selectedYear, selectedMonth);
        updateDonut2(selectedYear, selectedMonth);
        updateTotalPatients(selectedYear, selectedMonth); // Call to update total patients
        updateTotalIPD(selectedYear, selectedMonth); // Call to update IPD
    });
    // Function to handle month selection change without refreshing the page
    document.getElementById("monthFilter").addEventListener("change", function() {
        var selectedYear = document.getElementById("yearDropdown").value;
        var selectedMonth = this.value;
        updateDonut(selectedYear, selectedMonth);
        updateDonut2(selectedYear, selectedMonth);
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