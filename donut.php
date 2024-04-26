<?php
require 'connection.php';

// Get selected year and month from URL parameters
if(isset($_GET['selected_year']) && isset($_GET['selected_month'])) {
    $selected_year = $_GET['selected_year'];
    $selected_month = $_GET['selected_month'];
} else {
    // Default to current year and month if not set
    $selected_year = date('Y');
    $selected_month = date('m');
}

// Assuming 'dashboard_census' is your table name
$sql = "SELECT COUNT(census_id) AS getOPD FROM dashboard_census WHERE patient_transaction_type = 'OPD' AND MONTH(transaction_date) = '$selected_month' AND YEAR(transaction_date) = '$selected_year'"; // GET OPD 
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalOPDCensus = $row['getOPD'];

$sql2 = "SELECT COUNT(census_id) AS getIPD FROM dashboard_census WHERE patient_transaction_type = 'IPD' AND MONTH(transaction_date) = '$selected_month' AND YEAR(transaction_date) = '$selected_year'"; // GET IPD
$result2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($result2);
$totalIPDCensus = $row2['getIPD'];

$sql3 = "SELECT COUNT(census_id) AS getER FROM dashboard_census WHERE patient_transaction_type = 'ER' AND MONTH(transaction_date) = '$selected_month' AND YEAR(transaction_date) = '$selected_year'"; // GET ER
$result3 = mysqli_query($conn, $sql3);
$row3 = mysqli_fetch_assoc($result3);
$totalERCensus = $row3['getER'];

$sqlTvalueOPD = "SELECT * FROM dashboard_target WHERE target_type ='OPD'";
$getOPD = mysqli_query($conn, $sqlTvalueOPD);

if ($getOPD) {

    $value1 = mysqli_fetch_assoc($getOPD);


    if ($value1) {

        $TValueOPD = $value1['target_value'];

    } else {

        echo "No data found for target_type = 'OPD'";
    }
} else {

    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueIPD = "SELECT * FROM dashboard_target WHERE target_type ='IPD'";
$getIPD = mysqli_query($conn, $sqlTvalueIPD);

if ($getIPD) {

    $value1 = mysqli_fetch_assoc($getIPD);


    if ($value1) {

        $TValueIPD = $value1['target_value'];

    } else {

        echo "No data found for target_type = 'IPD'";
    }
} else {

    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueER = "SELECT * FROM dashboard_target WHERE target_type ='ER'";
$getER = mysqli_query($conn, $sqlTvalueER);

if ($getER) {

    $value1 = mysqli_fetch_assoc($getER);


    if ($value1) {

        $TValueER = $value1['target_value'];

    } else {

        echo "No data found for target_type = 'ER'";
    }
} else {

    echo "Error: " . mysqli_error($conn);
}

$OPDpercentage = round($totalOPDCensus /  $TValueOPD* 100);
$IPDPercentage = round($totalIPDCensus / $TValueIPD * 100);
$ERPercentage = round($totalERCensus / $TValueER * 100);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Three meter charts</title>
    <style>
        .multi-graph {
            width: 300px;
            height: 150px;
            position: relative;
            color: #fff;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            box-sizing: border-box;
        }

        .multi-graph:before {
            content: '';
            width: 300px;
            height: 150px;
            border: 50px solid rgba(0, 0, 0, .15);
            border-bottom: none;
            position: absolute;
            box-sizing: border-box;
            transform-origin: 50% 0%;
            border-radius: 300px 300px 0 0;
            left: 0;
            top: 0;
        }
        .graph {
            width: 300px;
            height: 150px;
            border: 50px solid var(--fill);
            border-top: none;
            position: absolute;
            transform-origin: 50% 0% 0;
            border-radius: 0 0 300px 300px;
            left: 0; 
            top: 100%;
            z-index: 5;
            animation: 1s fillGraphAnimation ease-in;
            transform: rotate(calc(1deg * (var(--percentage) * 1.8)));
            box-sizing: border-box;
            cursor: pointer;
            
        }
        .graph:after {
            content: attr(data-name) ' ' counter(varible) '%';
            counter-reset: varible var(--percentage);
            background: var(--fill);
            box-sizing: border-box;
            border-radius: 2px;
            color: #fff;
            font-weight: 200;
            font-size: 12px;
            height: 20px;
            padding: 3px 5px;
            top: 0px;
            position: absolute;
            left: 0;
            transform: rotate(calc(-1deg * var(--percentage) * 1.8)) translate(-30px, 0px);
            transition: 0.2s ease-in;
            transform-origin: 0 50% 0;
            opacity: 0;
        }
.graph:not(.opd):hover:after {
    opacity: 0;
}

.graph.opd:hover:after {
    opacity: 1;
    left: 30px;
    color:#000000;
}
.graph.ipd:hover:after {
    opacity: 1;
    left: 30px;
    color:#000000;
}
.graph.er:hover:after {
    opacity: 1;
    left: 30px;
    color:#000000;
}

@keyframes fillAnimation{
  0%{transform : rotate(-45deg);}
  50%{transform: rotate(135deg);}
}

@keyframes fillGraphAnimation{
  0%{transform: rotate(0deg);}
  50%{transform: rotate(180deg);}
}
		.label {
            color: black;
            font-size: 12px;
            font-weight: bold;
            position: absolute;
        }

        .label.left {
            left: 60px;
            top: 213px;
            transform: translate(-50%, -50%);
        }

        .label.top {
            top: 120px;
            left: 150px;
            transform: translateX(-50%);
        }

        .label.right {
            right: 67px;
            top: 210px;
            transform: translate(50%, -50%);
        }
        .label.halfright {
            right: 89px;
            top: 155px;
            transform: translate(50%, -50%);
        }
        .label.halfleft {
            left: 90px;
            top: 155px;
            transform: translate(-50%, -50%);
        }

        .meter-graph1 {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .meter-graph2 {
            position:  absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .meter-graph3 {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
        }
        .arrow {
            position: absolute;
            width: 3px; 
            height: 60px;
            background-color: black;
            top: 150px;
            left: 50%;
            transform-origin: bottom center;
        }
        .chart-label{
            text-align: center;

        }
	</style>
	</head>
	<body>
        
		<div class="graph-container">
            <!-- FOR METER OPD  -->
			<div class="meter-graph1">
				<div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>
				<div class="label right">100%</div>
                <div class = "arrow"></div>
                <div class = "chart-label">
				<p style="color:black">OPD TARGET CENSUS</p>
				<?php 
                echo "Total OPD Census: " . $totalOPDCensus . "<br>";?>
                </div> <div class="multi-graph margin">
            <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
            <div class="graph opd" data-name="OPD" style="--percentage : <?php echo $OPDpercentage; ?>;"></div>
        </div>
			</div>
             <!-- FOR METER IPD  -->
			<div class="meter-graph2">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>
				<div class="label right">100%</div>
                <div class = "arrow"></div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">IPD TARGET CENSUS</p>
				<?php echo "Total IPD Census: " . $totalIPDCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
            <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph ipd" data-name="IPD" style="--percentage : <?php echo $IPDPercentage; ?>;"></div>
				</div>
			</div>
             <!-- FOR METER ER  -->
			<div class="meter-graph3">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "arrow"></div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">ER TARGET CENSUS</p>
				<?php echo "Total ER Census: " . $totalERCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $ERPercentage; ?>;"></div>
				</div>
			</div>
		</div>
    <script>
// default arrow
        var opdArrowPosition = <?php echo $OPDpercentage; ?>;
        var opdArrow = document.createElement('div');
        opdArrow.classList.add('arrow');
        document.querySelector('.meter-graph1').appendChild(opdArrow);

        // Calculate arrow position for IPD
        var ipdArrowPosition = <?php echo $IPDPercentage; ?>;
        var ipdArrow = document.createElement('div');
        ipdArrow.classList.add('arrow');
        document.querySelector('.meter-graph2').appendChild(ipdArrow);

        // Calculate arrow position for ER
        var erArrowPosition = <?php echo $ERPercentage; ?>;
        var erArrow = document.createElement('div');
        erArrow.classList.add('arrow');
        document.querySelector('.meter-graph3').appendChild(erArrow);

        // Adjust position of arrows based on percentage
        opdArrow.style.transform = `translate(-50%, 0) rotate(${opdArrowPosition * 1.8 - 90}deg)`;
        ipdArrow.style.transform = `translate(-50%, 0) rotate(${ipdArrowPosition * 1.8 - 90}deg)`;
        erArrow.style.transform = `translate(-50%, 0) rotate(${erArrowPosition * 1.8 - 90}deg)`;

        function generateArrow(){
        // Calculate arrow position for OPD
        var opdArrowPosition = <?php echo $OPDpercentage; ?>;
        var opdArrow = document.createElement('div');
        opdArrow.classList.add('arrow');
        document.querySelector('.meter-graph1').appendChild(opdArrow);

        // Calculate arrow position for IPD
        var ipdArrowPosition = <?php echo $IPDPercentage; ?>;
        var ipdArrow = document.createElement('div');
        ipdArrow.classList.add('arrow');
        document.querySelector('.meter-graph2').appendChild(ipdArrow);

        // Calculate arrow position for ER
        var erArrowPosition = <?php echo $ERPercentage; ?>;
        var erArrow = document.createElement('div');
        erArrow.classList.add('arrow');
        document.querySelector('.meter-graph3').appendChild(erArrow);

        // Adjust position of arrows based on percentage
        opdArrow.style.transform = `translate(-50%, 0) rotate(${opdArrowPosition * 1.8 - 90}deg)`;
        ipdArrow.style.transform = `translate(-50%, 0) rotate(${ipdArrowPosition * 1.8 - 90}deg)`;
        erArrow.style.transform = `translate(-50%, 0) rotate(${erArrowPosition * 1.8 - 90}deg)`;
        }

// Function to generate options for each month
function generateMonthOptions() {
    var select = document.getElementById("monthFilter");
    var currentMonth = new Date().getMonth() + 1; // Adding 1 because JavaScript months are 0-indexed


    for (var i = 1; i <= 12; i++) {
        var option = document.createElement("option");
        option.value = i < 10 ? "0" + i : "" + i; // Adding leading zero if needed
        option.text = monthNames[i - 1]; // Subtracting 1 because months are 0-indexed in JavaScript
        select.appendChild(option);
    }

    // Set default selected month
    select.value = currentMonth < 10 ? "0" + currentMonth : "" + currentMonth;
}

// Call the function to generate options when the page loads
generateMonthOptions();
    </script>
	</body>
	</html>
