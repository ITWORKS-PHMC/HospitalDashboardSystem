<?php
require 'connection.php';

// Fetch distinct years from the dashboard_census table
$yearQuery = "SELECT DISTINCT YEAR(transaction_date) AS year FROM dashboard_census";
$yearResult = mysqli_query($conn, $yearQuery);

$deptQuery = "SELECT revenue_department FROM dashboard_revenue";
$deptResult = mysqli_query($conn, $deptQuery);
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
    </div>
</div>
<div class="dashboard-content" style="margin-left:30px;">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 20px; grid-row-gap: 20px;">
        <div class="Number-box" style="grid-column: 1 / 2; grid-row: 1/2;">
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
        <div class="dept-rev-box" style="grid-row: 2/3;">
        <form id="deptForm" >
            <p style="text-align:center; font-weight:bold;">SELECT  DEPARTMENT :</p>
        <select class="Filter-button" id="deptFilter" name="selected_department" style="width:270px;">
        <?php
        if ($deptResult) {
        $seenDepartments = array();  // Array to keep track of seen departments
            while ($deptRow = mysqli_fetch_assoc($deptResult)) {
                if (!in_array($deptRow['revenue_department'], $seenDepartments)) {
                // Add the department name to the array if it has not been seen before
                $seenDepartments[] = $deptRow['revenue_department'];
                echo '<option value="' . $deptRow['revenue_department'] . '">' . $deptRow['revenue_department'] . '</option>';
            }
        }
            } else {
                echo "Error: " . $deptQuery . "<br>" . mysqli_error($conn);
            }
        ?>

        </select>
    </form>
        <div class="perDept">
            <div id = "revDept">
            <?php include 'revenue-department.php';?>
            </div>
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

    // Function to handle year selection change without refreshing the page
    document.getElementById("yearDropdown").addEventListener("change", function() {
        var selectedYear = this.value;
        var selectedMonth = document.getElementById("monthFilter").value;
        var selectedDepartment = document.getElementById("deptFilter").value; // Add this line
        updateTotalPatients(selectedYear, selectedMonth, selectedDepartment); // Call to update total patients
        updateTotalIPD(selectedYear, selectedMonth, selectedDepartment); // Call to update IPD
    });
    // Function to handle month selection change without refreshing the page
    document.getElementById("monthFilter").addEventListener("change", function() {
        var selectedYear = document.getElementById("yearDropdown").value;
        var selectedMonth = this.value;
        var selectedDepartment = document.getElementById("deptFilter").value; // Add this line
        updateTotalPatients(selectedYear, selectedMonth, selectedDepartment); // Call to update total patients
        updateTotalIPD(selectedYear, selectedMonth, selectedDepartment); // Call to update IPD
    });

// Function to update the total patient count using AJAX
function updateTotalPatients(selectedYear, selectedMonth, selectedDepartment) {
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
function updateTotalIPD(selectedYear, selectedMonth, selectedDepartment) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("boxtotalIPD").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "revenue-totalbed.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth, true);
    xhttp.send();
}

// Function to handle department selection change without refreshing the page
document.getElementById("deptFilter").addEventListener("change", function() {
    var selectedYear = document.getElementById("yearDropdown").value;
    var selectedMonth = document.getElementById("monthFilter").value;
    var selectedDepartment = this.value;
    updateTotalRevenue(selectedYear, selectedMonth, selectedDepartment); // Call to update total revenue for the selected department
});

// Function to update the total revenue of the selected department using AJAX
function updateTotalRevenue(selectedYear, selectedMonth, selectedDepartment) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("revDept").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "revenue-department.php?selected_year=" + selectedYear + "&selected_month=" + selectedMonth + "&selected_department=" + selectedDepartment, true);
    xhttp.send();
    console.log (selectedDepartment)
}
</script>
<footer></footer>
</body>
</html>
