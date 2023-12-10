<?php
$start = microtime(true);
$sum1 = $sum2 = 1;
$file = fopen("input.txt", "r");
$times = array_values(array_filter(explode(' ', substr(fgets($file), 5))));
$records = array_values(array_filter(explode(' ', substr(fgets($file), 9))));
foreach ($times as $index => $time) {
    $root = sqrt($time * $time - 4 * $records[$index]);
    $windup = ceil(($time -  $root) / 2);
    $sum1 *= ($time - 2 * $windup + 1);
}
$time = intval(implode('', $times));
$record = intval(implode('', $records));
$windup = ceil(($time - (sqrt($time * $time - 4 * $record))) / 2);
$sum2 = ($time - 2 * $windup + 1);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
