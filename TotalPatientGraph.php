<?php
require 'connection.php';


// Assuming you have a table named 'patients' with columns 'month' and 'total'
// Fetch data from the database
$sql = "SELECT * FROM totalpatients";
$result = mysqli_query($conn, $sql);

$dataPoints = array();

// Process fetched data into format suitable for CanvasJS
while ($row = mysqli_fetch_assoc($result)) {
    $dataPoints[] = array("y" => $row['total'], "label" => $row['month']);
}

?>

<!DOCTYPE HTML>
<html>
<head>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	title: {
		text: "Total Patient for Year 2023"
	},
	axisY: {
		title: "Number of Total Patient"
	},
	data: [{
		type: "line",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer"></div>
</body>
</html>