<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
$trench1 = $trench2 = [];
$pos1 = $pos2 = [0, 0];
$border1 = $border2 = 0;
while (($line = fgets($file)) != null) {
    $line = explode(' ', $line);
    $length = intval($line[1]);
    $border1 += $length;
    $newPos = match ($line[0]) {
        default => $pos1,
        'U' => [$pos1[0] - $length, $pos1[1]],
        'D' => [$pos1[0] + $length, $pos1[1]],
        'L' => [$pos1[0], $pos1[1] - $length],
        'R' => [$pos1[0], $pos1[1] + $length],
    };
    $trench1[] = $newPos;
    $pos1 = $newPos;
    $length = intval(substr($line[2], 2, 5), 16);
    $border2 += $length;
    $newPos = match ($line[2][7]) {
        default => $pos2,
        '3' => [$pos2[0] - $length, $pos2[1]],
        '1' => [$pos2[0] + $length, $pos2[1]],
        '2' => [$pos2[0], $pos2[1] - $length],
        '0' => [$pos2[0], $pos2[1] + $length],
    };
    $trench2[] = $newPos;
    $pos2 = $newPos;
}
foreach ($trench1 as $i => $point) {
    $other = $trench1[$i + 1] ?? $trench1[0];
    $sum1 += $point[0] * $other[1];
    $sum1 -= $point[1] * $other[0];
}
$sum1 = (abs($sum1) + $border1) / 2 + 1;
foreach ($trench2 as $i => $point) {
    $other = $trench2[$i + 1] ?? $trench2[0];
    $sum2 += $point[0] * $other[1];
    $sum2 -= $point[1] * $other[0];
}
$sum2 = (abs($sum2) + $border2) / 2 + 1;
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
