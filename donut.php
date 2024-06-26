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
$sql = "SELECT COUNT(PK_psPatRegisters) AS totalOPD FROM rptCensus WHERE pattrantype = 'O' AND MONTH(datetimeadmitted) = '$selected_month' AND YEAR(datetimeadmitted) = '$selected_year'"; // GET OPD 
$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC);
$totalOPDCensus = $row['totalOPD'];

$sql2 = "SELECT COUNT(PK_psPatRegisters) AS totalIPD FROM rptCensus WHERE pattrantype = 'I' AND MONTH(datetimeadmitted) = '$selected_month' AND YEAR(datetimeadmitted) = '$selected_year'"; // GET IPD
$result2 = sqlsrv_query($conn, $sql2);
$row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC);
$totalIPDCensus = $row2['totalIPD'];

$sql3 = "SELECT COUNT(PK_psPatRegisters) AS totalER FROM rptCensus WHERE pattrantype = 'E' AND MONTH(datetimeadmitted) = '$selected_month' AND YEAR(datetimeadmitted) = '$selected_year'"; // GET ER
$result3 = sqlsrv_query($conn, $sql3);
$row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC);
$totalERCensus = $row3['totalER'];

// // Assuming 'dashboard_target' is your table name for target values
// $sqlTvalueOPD = "SELECT * FROM rptCensus WHERE target_type ='OPD' AND YEAR(target_date) = $selected_year AND MONTH(target_date) = $selected_month";
// $getOPD = sqlsrv_query($conn, $sqlTvalueOPD);

// if ($getOPD) {
//     $value1 = sqlsrv_fetch($getOPD);
//     if ($value1) {
//         $TValueOPD = $value1['target_value'];
//     } 
// } else {
//     echo "Error: " . sqlsrv_errors($conn);
// }

// $sqlTvalueIPD = "SELECT * FROM dashboard_target WHERE target_type ='IPD' AND YEAR(target_date) = $selected_year AND MONTH(target_date) = $selected_month";
// $getIPD = sqlsrv_query($conn, $sqlTvalueIPD);

// if ($getIPD) {
//     $value1 = sqlsrv_fetch($getIPD);
//     if ($value1) {
//         $TValueIPD = $value1['target_value'];
//     } 
// } else {
//     echo "Error: " . sqlsrv_errors($conn);
// }

// $sqlTvalueER = "SELECT * FROM dashboard_target WHERE target_type ='ER' AND YEAR(target_date) = $selected_year AND MONTH(target_date) = $selected_month";
// $getER = sqlsrv_query($conn, $sqlTvalueER);

// if ($getER) {
//     $value1 = sqlsrv_fetch($getER);
//     if ($value1) {
//         $TValueER = $value1['target_value'];
//     } 
// } else {
//     echo "Error: " . sqlsrv_errors($conn);
// }

// Assuming 'dashboard_target' is your table name for target values
// Check if the target value for OPD is available
// if (isset($TValueOPD) && $TValueOPD != 0) {
    $OPDpercentage = round($totalOPDCensus /  15000 * 100);
// } else {
//     $OPDpercentage = 0; // or any default value you prefer
// }

// Check if the target value for IPD is available
// if (isset($TValueIPD) && $TValueIPD != 0) {
    $IPDPercentage = round($totalIPDCensus / 1000 * 100);

// } else {
//     $IPDPercentage = 0; // or any default value you prefer
// }

// // Check if the target value for ER is available
// if (isset($TValueER) && $TValueER != 0) {
    $ERPercentage = round($totalERCensus / 2000 * 100);
// } else {
//     $ERPercentage = 0; // or any default value you prefer
// }

sqlsrv_close($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Three meter charts</title>
	</head>
    <style>
/* STYLES FOR 3 METER GRAPHS */
.multi-graph {
            width: 350px; /* Adjust width as needed */
    height: 175px; /* Adjust height as needed */
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
            width: 350px; /* Adjust width as needed */
    height: 175px; /* Adjust height as needed */
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
            width: 350px; /* Adjust width as needed */
    height: 175px; /* Adjust height as needed */
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
            top: 240px;
            transform: translate(-50%, -50%);
        }
        .label.top {
            top: 120px;
            left: 175px;
            transform: translateX(-50%);
        }
        .label.right {
            right: 65px;
            top: 240px;
            transform: translate(50%, -50%);
        }
        .label.halfright {
            right: 83px;
            top: 177px;
            transform: translate(50%, -50%);
        }
        .label.halfleft {
            left: 93px;
            top: 167px;
            transform: translate(-50%, -50%);
        }
        .meter-graph1 {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 30px;
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
            margin-right: 30px;
        }
        .arrow {
            position: absolute;
            width: 0; 
            height: 0; 
            border-left: 1px solid transparent;
            border-right: 1px solid transparent;
            border-bottom: 160px solid black; 
            top: 85px; 
            left: 50%;
            transform-origin: bottom center;
            transition: transform 1s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
        .chart-label{
            text-align: center;
        }
    .meter-graph{
      width: 500px;
      
    }
    </style>
	<body>
            <!-- Your HTML content -->
        <div id="opdPercentage" style="display: none;"><?php echo $OPDpercentage; ?></div>
        <div id="ipdPercentage" style="display: none;"><?php echo $IPDPercentage; ?></div>
        <div id="erPercentage" style="display: none;"><?php echo $ERPercentage; ?></div>
		<div class="graph-container">
            <!-- FOR METER OPD  -->
            <div class="meter-graph" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 20px;">
			<div class="meter-graph1">
				<div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>
				<div class="label right">100%</div>

                <div class = "chart-label">
				<p style="color:black"><strong>OPD TARGET CENSUS</strong></p>
				<?php 
                echo "<span style='color: black; font-weight:bold;'>Total OPD Census: " . $totalOPDCensus ."/". "</span><br>"; ?>
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
                <div class = "chart-label">
				<p class = "chart-label" style="color:black"><strong>IPD TARGET CENSUS</strong></p>
				<?php echo "<span style='color: black; font-weight:bold;'>Total IPD Census: " . $totalIPDCensus ."/". "</span><br>"; ?>
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
                <div class = "chart-label">
				<p class = "chart-label" style="color:black"><strong>ER TARGET CENSUS</strong></p>
				<?php echo "<span style='color: black; font-weight:bold;'>Total ER Census: " . $totalERCensus ."/". "</span><br>"; ?>
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

          function generateArrow(OPDpercentage, IPDPercentage, ERPercentage) {
            console.log("OPD Percentage: ", OPDpercentage);
            console.log("IPD Percentage: ", IPDPercentage);
            console.log("ER Percentage: ", ERPercentage);

            // Remove existing arrows if they exist
            var existingArrows = document.querySelectorAll('.arrow');
            existingArrows.forEach(function(arrow) {
                arrow.remove();
            });

          var opdArrow = document.createElement('div');
    opdArrow.classList.add('arrow');
    document.querySelector('.meter-graph1').appendChild(opdArrow);
    opdArrow.style.transform=`translate(-50%, 0) rotate(-90deg)`;
    setTimeout(() => {
        opdArrow.style.transform = `translate(-50%, 0) rotate(${OPDpercentage * 1.8 - 90}deg)`;
    }, 100); // Adding a delay to ensure the arrow is added to the DOM before applying the transformation

    // Calculate arrow position for IPD
    var ipdArrow = document.createElement('div');
    ipdArrow.classList.add('arrow');
    document.querySelector('.meter-graph2').appendChild(ipdArrow);
    ipdArrow.style.transform=`translate(-50%, 0) rotate(-90deg)`;
    setTimeout(() => {
        ipdArrow.style.transform = `translate(-50%, 0) rotate(${IPDPercentage * 1.8 - 90}deg)`;
    }, 100); // Adding a delay to ensure the arrow is added to the DOM before applying the transformation

    // Calculate arrow position for ER
    var erArrow = document.createElement('div');
    erArrow.classList.add('arrow');
    document.querySelector('.meter-graph3').appendChild(erArrow);
    erArrow.style.transform=`translate(-50%, 0) rotate(-90deg)`;
    setTimeout(() => {
        erArrow.style.transform = `translate(-50%, 0) rotate(${ERPercentage * 1.8 - 90}deg)`;
    }, 100); 
        }
        
    </script>
	</body>
	</html>