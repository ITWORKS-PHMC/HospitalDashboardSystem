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
$sql = "SELECT COUNT(census_transaction_id) AS getOPD FROM dashboard_database WHERE census_transaction_type = 'O' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET OPD 
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalOPDCensus = $row['getOPD'];

$sql2 = "SELECT COUNT(census_transaction_id) AS getIPD FROM dashboard_database WHERE census_transaction_type = 'I' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET IPD
$result2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($result2);
$totalIPDCensus = $row2['getIPD'];

$sql3 = "SELECT COUNT(census_transaction_id) AS getER FROM dashboard_database WHERE census_transaction_type = 'E' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET ER
$result3 = mysqli_query($conn, $sql3);
$row3 = mysqli_fetch_assoc($result3);
$totalERCensus = $row3['getER'];

$sql4 = "SELECT COUNT(census_transaction_id) AS getXRAY FROM dashboard_database WHERE census_transaction_type = 'XRAY' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result4 = mysqli_query($conn, $sql4);
$row4 = mysqli_fetch_assoc($result4);
$totalXRAYCensus = $row4['getXRAY'];

$sql5 = "SELECT COUNT(census_transaction_id) AS getMRI FROM dashboard_database WHERE census_transaction_type = 'MRI' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result5 = mysqli_query($conn, $sql5);
$row5 = mysqli_fetch_assoc($result5);
$totalMRICensus = $row5['getMRI'];

$sql6 = "SELECT COUNT(census_transaction_id) AS getPULMONARY FROM dashboard_database WHERE census_transaction_type = 'PULMONARY' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result6 = mysqli_query($conn, $sql6);
$row6 = mysqli_fetch_assoc($result6);
$totalPULMONARYCensus = $row6['getPULMONARY'];

$sql7 = "SELECT COUNT(census_transaction_id) AS getULTRASOUND FROM dashboard_database WHERE census_transaction_type = 'ULTRASOUND' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result7 = mysqli_query($conn, $sql7);
$row7 = mysqli_fetch_assoc($result7);
$totalULTRASOUNDCensus = $row7['getULTRASOUND'];

$sql8 = "SELECT COUNT(census_transaction_id) AS getICU FROM dashboard_database WHERE census_transaction_type = 'ICU' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result8 = mysqli_query($conn, $sql8);
$row8 = mysqli_fetch_assoc($result8);
$totalICUCensus = $row8['getICU'];

$sql9 = "SELECT COUNT(census_transaction_id) AS getLABORATORY FROM dashboard_database WHERE census_transaction_type = 'LABORATORY' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result9 = mysqli_query($conn, $sql9);
$row9 = mysqli_fetch_assoc($result9);
$totalLABORATORYCensus = $row9['getLABORATORY'];

$sql10 = "SELECT COUNT(census_transaction_id) AS getCSR FROM dashboard_database WHERE census_transaction_type = 'CSR' AND MONTH(census_date_admitted) = '$selected_month' AND YEAR(census_date_admitted) = '$selected_year'"; // GET XRAY
$result10 = mysqli_query($conn, $sql10);
$row10 = mysqli_fetch_assoc($result10);
$totalCSRCensus = $row10['getCSR'];


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


$sqlTvalueXRAY = "SELECT * FROM dashboard_target WHERE target_type ='X-RAY'";
$getXRAY = mysqli_query($conn, $sqlTvalueXRAY);
if ($getXRAY) {
    $value1 = mysqli_fetch_assoc($getXRAY);
    if ($value1) {
        $TValueXRAY = $value1['target_value'];

    } else {
        echo "No data found for target_type = 'X-RAY'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueMRI = "SELECT * FROM dashboard_target WHERE target_type ='MRI'";
$getMRI = mysqli_query($conn, $sqlTvalueMRI);
if ($getMRI) {
    $value1 = mysqli_fetch_assoc($getMRI);
    if ($value1) {
        $TValueXRAY = $value1['target_value'];

    } else {
        echo "No data found for target_type = 'MRI'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$sqlTvaluePULMONARY = "SELECT * FROM dashboard_target WHERE target_type ='PULMONARY'";
$getPULMONARY = mysqli_query($conn, $sqlTvaluePULMONARY);

if ($getPULMONARY) {

    $value1 = mysqli_fetch_assoc($getPULMONARY);
   if ($value1) {
        $TValuePULMONARY = $value1['target_value'];
    } else {
        echo "No data found for target_type = 'OPD'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueULTRASOUND = "SELECT * FROM dashboard_target WHERE target_type ='ULTRASOUND'";
$getULTRASOUND = mysqli_query($conn, $sqlTvalueULTRASOUND);

if ($getULTRASOUND) {
    $value1 = mysqli_fetch_assoc($getULTRASOUND);
    if ($value1) {
        $TValueULTRASOUND = $value1['target_value'];
    } else {
        echo "No data found for target_type = 'ULTRASOUND'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueICU = "SELECT * FROM dashboard_target WHERE target_type ='ICU'";
$getICU = mysqli_query($conn, $sqlTvalueICU);

if ($getICU) {
    $value1 = mysqli_fetch_assoc($getICU);
    if ($value1) {
        $TValueICU = $value1['target_value'];

    } else {
        echo "No data found for target_type = 'ER'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}


$sqlTvalueLABORATORY = "SELECT * FROM dashboard_target WHERE target_type ='LABORATORY'";
$getLABORATORY = mysqli_query($conn, $sqlTvalueLABORATORY);
if ($getLABORATORY) {
    $value1 = mysqli_fetch_assoc($getLABORATORY);
    if ($value1) {
        $TValueLABORATORY = $value1['target_value'];

    } else {
        echo "No data found for target_type = 'X-RAY'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$sqlTvalueCSR = "SELECT * FROM dashboard_target WHERE target_type ='CSR'";
$getCSR = mysqli_query($conn, $sqlTvalueCSR);
if ($getCSR) {
    $value1 = mysqli_fetch_assoc($getCSR);
    if ($value1) {
        $TValueCSR = $value1['target_value'];

    } else {
        echo "No data found for target_type = 'MRI'";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

$OPDpercentage = round(75);
$IPDPercentage = round($totalIPDCensus / $TValueIPD * 100);
$ERPercentage = round($totalERCensus / $TValueER * 100);
$XRAYPercentage = round(25);
$MRIPercentage = round(88);
$PULMONARYPercentage = round(69);
$ULTRASOUNDPercentage = round(69);
$ICUPercentage = round(69);
$LABORATORYPercentage = round(69);
$CSRPercentage = round(69);

mysqli_close($conn);
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
            width: 200px; /* Adjust width as needed */
            height: 100px; /* Adjust height as needed */
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
            width: 200px; /* Adjust width as needed */
            height: 100px; /* Adjust height as needed */
            border: 35px solid rgba(0, 0, 0, .15);
            border-bottom: none;
            position: absolute;
            box-sizing: border-box;
            transform-origin: 50% 0%;
            border-radius: 300px 300px 0 0;
            left: 0;
            top: 0;
        }
        .graph {
            width: 200px; /* Adjust width as needed */
            height: 100px; /* Adjust height as needed */
            border: 35px solid var(--fill);
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
            left: 45px;
            top: 165px;
            transform: translate(-50%, -50%);
            font-size: 11px;
        }
        .label.top {
            top: 110px;
            left: 100px;
            transform: translateX(-50%);
            font-size: 11px;
        }
        .label.right {
            right: 50px;
            top: 165px;
            transform: translate(50%, -50%);
            font-size: 11px;
        }
        .label.halfright {
            right: 59px;
            top: 135px;
            transform: translate(50%, -50%);
            font-size: 11px;
        }
        .label.halfleft {
            left: 63px;
            top: 133px;
            transform: translate(-50%, -50%);
            font-size: 11px;
        }
        .meter-graph1 {
            position: absolute;
            left: 0;
            top: 18%;
            transform: translateY(-50%);
        }
        .meter-graph2 {
            position:  absolute;
            left: 50%;
            top: 18%;
            transform: translate(-50%, -50%);
        }
        .meter-graph3 {
            position: absolute;
            right: 0;
            top: 18%;
            transform: translateY(-50%);
        }
        .meter-graph4 {
            position: absolute;
            right: 65%;
            top: 18%;
            transform: translateY(-50%);
        }
        .meter-graph5 {
            position: absolute;
            right: 21%;
            top: 18%;
            transform: translateY(-50%);
        }
        .meter-graph6 {
            position: absolute;
            left: 0;
            top: 71%;
            transform: translateY(-50%);
        }
        .meter-graph7 {
            position:  absolute;
            left: 50%;
            top: 71%;
            transform: translate(-50%, -50%);
        }
        .meter-graph8 {
            position: absolute;
            right: 0;
            top: 71%;
            transform: translateY(-50%);
        }
        .meter-graph9 {
            position: absolute;
            right: 65%;
            top: 71%;
            transform: translateY(-50%);
        }
        .meter-graph10 {
            position: absolute;
            right: 21%;
            top: 71%;
            transform: translateY(-50%);
        }
        .arrow {
            position: absolute;
            font-size: 16px;
            width: 3px; 
            height: 40px;
            background-color: black;
            top: 128px;
            left: 50%;
            transform-origin: bottom center;
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
        <div class = "Row1-graph-container">
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
                    <!-- FOR METER XRAY  -->
			<div class="meter-graph4">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">XRAY TARGET CENSUS</p>
				<?php echo "Total XRAY Census: " . $totalXRAYCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $XRAYercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER ER  -->
			<div class="meter-graph5">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">MRI TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalMRICensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $MRIPercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER PULMONARY  -->
			<div class="meter-graph6">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">PULMONARY TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalPULMONARYCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $PULMONARYPercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER ULTRASOUND  -->
			<div class="meter-graph7">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black; font-size:14.3px">ULTRASOUND TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalICUCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $ICUPercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER ICU  -->
			<div class="meter-graph8">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">ICU TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalICUCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $ICUPercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER LABORATORY  -->
			<div class="meter-graph9">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">LABORATORY TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalLABORATORYCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $LABORATORYPercentage; ?>;"></div>
				</div>
			</div>
                    <!-- FOR METER CSR  -->
			<div class="meter-graph10">
                <div class="label left">0%</div>
				<div class="label halfleft">25%</div>
				<div class="label top">50%</div>
				<div class="label halfright">75%</div>  
				<div class="label right">100%</div>
                <div class = "chart-label">
				<p class = "chart-label" style="color:black">CSR TARGET CENSUS</p>
				<?php echo "Total MRI Census: " . $totalCSRCensus . "<br>";?>
                </div>
				<div class="multi-graph margin">
                      <div style="top: -10px; left: calc(80% - 5px);"></div>
            <div class="graph" style="--percentage : 100; --fill: #008000 ;"> </div>
            <div style="top: -10px; left: calc(60% - 5px);"></div>
            <div class="graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
            <div style="top: -10px; left: calc(30% - 5px);"></div>
            <div class="graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
					<div class="graph er" data-name="ER" style="--percentage : <?php echo $CSRPercentage; ?>;"></div>
				</div>
			</div>
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

        // Calculate arrow position for ER
        var xrayArrowPosition = <?php echo $XRAYPercentage; ?>;
        var xrayArrow = document.createElement('div');
        xrayArrow.classList.add('arrow');
        document.querySelector('.meter-graph4').appendChild(xrayArrow);

        var mriArrowPosition = <?php echo $MRIPercentage; ?>;
        var mriArrow = document.createElement('div');
        mriArrow.classList.add('arrow');
        document.querySelector('.meter-graph5').appendChild(mriArrow);

        // Adjust position of arrows based on percentage
        opdArrow.style.transform = `translate(-50%, 0) rotate(${opdArrowPosition * 1.8 - 90}deg)`;
        ipdArrow.style.transform = `translate(-50%, 0) rotate(${ipdArrowPosition * 1.8 - 90}deg)`;
        erArrow.style.transform = `translate(-50%, 0) rotate(${erArrowPosition * 1.8 - 90}deg)`;
        xrayArrow.style.transform = `translate(-50%, 0) rotate(${xrayArrowPosition * 1.8 - 90}deg)`;
        mriArrow.style.transform = `translate(-50%, 0) rotate(${mriArrowPosition * 1.8 - 90}deg)`;

          function generateArrow(OPDpercentage, IPDPercentage, ERPercentage) {
            console.log("OPD Percentage: ", OPDpercentage);
            console.log("IPD Percentage: ", IPDPercentage);
            console.log("ER Percentage: ", ERPercentage);

            // Remove existing arrows if they exist
            var existingArrows = document.querySelectorAll('.arrow');
            existingArrows.forEach(function(arrow) {
                arrow.remove();
            });

            // Calculate arrow position for OPD
            var opdArrow = document.createElement('div');
            opdArrow.classList.add('arrow');
            document.querySelector('.meter-graph1').appendChild(opdArrow);
            opdArrow.style.transform = `translate(-50%, 0) rotate(${OPDpercentage * 1.8 - 90}deg)`;

            // Calculate arrow position for IPD
            var ipdArrow = document.createElement('div');
            ipdArrow.classList.add('arrow');
            document.querySelector('.meter-graph2').appendChild(ipdArrow);
            ipdArrow.style.transform = `translate(-50%, 0) rotate(${IPDPercentage * 1.8 - 90}deg)`;

            // Calculate arrow position for ER
            var erArrow = document.createElement('div');
            erArrow.classList.add('arrow');
            document.querySelector('.meter-graph3').appendChild(erArrow);
            erArrow.style.transform = `translate(-50%, 0) rotate(${ERPercentage * 1.8 - 90}deg)`;

            // Calculate arrow position for ER
            var erArrow = document.createElement('div');
            erArrow.classList.add('arrow');
            document.querySelector('.meter-graph3').appendChild(erArrow);
            erArrow.style.transform = `translate(-50%, 0) rotate(${ERPercentage * 1.8 - 90}deg)`;

            // Calculate arrow position for ER
            var erArrow = document.createElement('div');
            erArrow.classList.add('arrow');
            document.querySelector('.meter-graph3').appendChild(erArrow);
            erArrow.style.transform = `translate(-50%, 0) rotate(${ERPercentage * 1.8 - 90}deg)`;
        }
        
    </script>
	</body>
	</html>