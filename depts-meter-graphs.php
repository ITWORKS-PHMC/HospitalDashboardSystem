<?php
require 'connection.php';

$selected_year = isset($_GET['selected_year']) ? $_GET['selected_year'] : 2024;
$selected_month = isset($_GET['selected_month']) ? $_GET['selected_month'] : 02;
// // Get selected year and month from URL parameters
// if(isset($_GET['selected_year']) && isset($_GET['selected_month'])) {
//     $selected_year = $_GET['selected_year'];
//     $selected_month = $_GET['selected_month'];
// } else {
//     // Default to current year and month if not set
//     $selected_year = date('Y');
//     $selected_month = date('m');
// }

// Assuming 'dashboard_census' is your table name
$sql = "SELECT DISTINCT pattrantype FROM rptCensus WHERE MONTH(datetimeadmitted) = '$selected_month' AND YEAR(datetimeadmitted) = '$selected_year'"; // Get unique department types
$result = sqlsrv_query($conn, $sql);

$departmentPercentages = array();

while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC)) {
    $departmentType = $row['pattrantype'];

    $sql_department = "SELECT COUNT(PK_psPatRegisters) AS total FROM rptCensus WHERE pattrantype = '$departmentType' AND MONTH(datetimeadmitted) = '$selected_month' AND YEAR(datetimeadmitted) = '$selected_year'";
    $result_department = sqlsrv_query($conn, $sql_department);
    $row_department = sqlsrv_fetch_array($result_department, SQLSRV_FETCH_ASSOC);
    $totalDepartmentCensus = $row_department['total'];

    // Calculate percentage for the department
    $percentage = round($totalDepartmentCensus); // Adjust as needed

    // Store department type and percentage in the array
    $departmentPercentages[$departmentType] = $percentage;
}


sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Array charts</title>
	</head>
<style>
            .dept-multi-graph {
            width: 210px; /* Adjust width as needed */
            height: 105px; /* Adjust height as needed */
            position: relative;
            color: #fff;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            box-sizing: border-box;
            left:0px;
        }
        .dept-multi-graph:before {
            content: '';
            width: 210px; /* Adjust width as needed */
            height: 105px; /* Adjust height as needed */
            border: 30px solid rgba(0, 0, 0, .15);
            border-bottom: none;
            position: absolute;
            box-sizing: border-box;
            transform-origin: 50% 0%;
            border-radius: 300px 300px 0 0;
            left: 0;
            top: 0;
        }
        .dept-graph {
            width: 210px; /* Adjust width as needed */
            height: 105px; /* Adjust height as needed */
            border: 30px solid var(--fill);
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
        
        .dept-graph:after {
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
.graph.hover:hover:after {
    opacity: 1;
    left: 30px;
    color:#000000;
}
.graph.hover:hover:after {
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
		.dept-label {
            color: black;
            font-size: 12px;
            font-weight: bold;
            position: absolute;
        }
      /* Styles for meter graphs */
        .dept-graph-container {
            padding: 20px;
        }
        .departmentmeter-graph {
            display: grid;
            grid-template-columns: repeat(<?php echo count($departmentPercentages) ?>, 1fr);
            grid-gap: 20px;
            width:100px;
            grid-auto-flow: row;
        }
        .departmentmeter-graph > div {
            position: relative;
            text-align: center;
        }
        .departmentmeter-graph .dept-label {
            position: absolute;
            font-size: 12px;
            font-weight: bold;
        }
        .departmentmeter-graph .dept-label.left {
            left: 35px;
            top: 96%;
            transform: translateY(-50%);
        }
        .departmentmeter-graph .dept-label.halfleft {
            left: 50px;
            top: calc(100% - 25%);
            transform: translateY(-50%);
        }
        .departmentmeter-graph .dept-label.top {
            top: 105px;
            left: 51%;
            transform: translateX(-50%);
        }
        .departmentmeter-graph .dept-label.halfright {
            right: 50px;
            top: calc(99% - 25%);
            transform: translateY(-50%);
        }
        .departmentmeter-graph .dept-label.right {
            right: 35px;
            top: 96%;
            transform: translateY(-50%);
        }
        .departmentmeter-graph .dept-chart-label {
            text-align: center;
            margin-bottom: 10px;
        }
        .dept-multi-graph-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .dept-arrow {
            position: absolute;
            font-size: 16px;
            width: 3px; 
            height: 45px;
            background-color: black;
            top: 129px;
            left: 50%;
            transform-origin: bottom center;
            transition: transform 1s cubic-bezier(0.4, 0.0, 0.2, 1);
        }

    </style>
	<body>
<div id="depts-percentage" style="display: none;"><?php echo json_encode($departmentPercentages); ?></div> <!-- ginawang json para matransfer to dashboard.php and function sa baba-->
     <div class="dept-graph-container">
        <div class="departmentmeter-graph" style="display: grid; grid-template-columns: repeat(<?php echo count($departmentPercentages) ?>, 1fr); grid-gap: 20px;">
            <?php foreach ($departmentPercentages as $departmentType => $percentage): ?>
                <div class="departmentmeter-graph-<?php echo strtolower($departmentType) ?>">
                    <div class="dept-label left">0%</div>
                    <div class="dept-label halfleft">25%</div>
                    <div class="dept-label top">50%</div>
                    <div class="dept-label halfright">75%</div>
                    <div class="dept-label right">100%</div>

                    <div class="dept-chart-label">
                        <p class="dept-chart-label" style="color:black;"><strong><?php echo strtoupper($departmentType) ?> TARGET CENSUS</strong></p>
                        <span style="color: black; font-weight:bold;">Total <?php echo strtoupper($departmentType)?> Census: <?php echo $departmentPercentages[$departmentType] ?>/</span><br>
                    </div>
                    <div class="dept-multi-graph margin">
                        <div style="top: -10px; left: calc(80% - 5px);"></div>
                        <div class="dept-graph" style="--percentage : 100; --fill: #008000 ;"> </div>
                        <div style="top: -10px; left: calc(60% - 5px);"></div>
                        <div class="dept-graph" style="--percentage : 59; --fill:#FEDA3E  ;"></div>
                        <div style="top: -10px; left: calc(30% - 5px);"></div>
                        <div class="dept-graph" style="--percentage : 45; --fill: #ff0000  ;"> </div>
                        <div class="dept-graph-hover <?php echo strtolower($departmentType) ?>" data-name="<?php echo strtoupper($departmentType) ?>" style="--percentage : <?php echo $percentage; ?>;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<script>
// default function for arrow without monthly filter
<?php foreach ($departmentPercentages as $departmentType => $percentage): ?>
    var <?php echo strtolower($departmentType) ?>ArrowPosition = <?php echo $percentage ?>;
    var <?php echo strtolower($departmentType) ?>Arrow = document.createElement('div');
    <?php echo strtolower($departmentType) ?>Arrow.classList.add('dept-arrow');
    document.querySelector('.departmentmeter-graph-<?php echo strtolower($departmentType) ?>').appendChild(<?php echo strtolower($departmentType) ?>Arrow);
    var rotation = <?php echo strtolower($departmentType) ?>ArrowPosition * 1.8 - 90;
    <?php echo strtolower($departmentType) ?>Arrow.style.transform = 'translate(-50%, 0) rotate(' + rotation + 'deg)';
<?php endforeach; ?>

// function for generating arrows for monthly filter
function generateArrows(data) { 
    for (var key in data) {
        if (data.hasOwnProperty(key)) {
            var arrowPosition = data[key];
            var arrow = document.createElement('div');
            arrow.classList.add('dept-arrow');
            document.querySelector('.departmentmeter-graph-' + key.toLowerCase()).appendChild(arrow);
            var rotation = arrowPosition * 1.8 - 90;
            arrow.style.transform = 'translate(-50%, 0) rotate(' + rotation + 'deg)';
        }
    }
}

</script>


	</body>
    </html>