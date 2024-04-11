<?php
// Data for the donut graph
$data = [
    ['label' => 'Category A', 'value' => 30, 'color' => '#3498db'],
    ['label' => 'Category B', 'value' => 20, 'color' => '#2ecc71'],
    ['label' => 'Category C', 'value' => 25, 'color' => '#f1c40f'],
    ['label' => 'Category D', 'value' => 15, 'color' => '#e74c3c'],
    ['label' => 'Category E', 'value' => 10, 'color' => '#9b59b6']
];

// Calculate total value
$total = array_reduce($data, function ($carry, $item) {
    return $carry + $item['value'];
}, 0);

// Generate CSS for each segment
$cssSegments = '';
$startDegree = 0;
foreach ($data as $segment) {
    $percentage = ($segment['value'] / $total) * 360;
    $cssSegments .= "
        .segment-{$segment['label']} {
            clip-path: polygon(50% 50%, 50% 0, 0 0, 0 50%, 50% 50%);
            transform: rotate({$startDegree}deg);
        }
        .segment-{$segment['label']}::after {
            clip-path: polygon(50% 50%, 50% 0, 0 0, 0 50%, 50% 50%);
            transform: rotate({$percentage}deg);
            background-color: {$segment['color']};
            content: '{$segment['label']}';
        }
    ";
    $startDegree += $percentage;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donut Graph</title>
    <style>
        .donut {
            width: 300px;
            height: 300px;
            position: relative;
            border-radius: 50%;
            background-color: #f1f1f1;
            overflow: hidden;
        }
        .segment {
            position: absolute;
            width: 100%;
            height: 100%;
            clip-path: polygon(50% 50%, 50% 0, 0 0, 0 50%, 50% 50%);
            transform-origin: center;
        }
        .segment::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #3498db;
            transform-origin: center;
        }
        <?php echo $cssSegments; ?>
    </style>
</head>
<body>
    <div class="donut">
        <?php foreach ($data as $segment): ?>
            <div class="segment segment-<?php echo str_replace(' ', '-', strtolower($segment['label'])); ?>"></div>
        <?php endforeach; ?>
    </div>
</body>
</html>