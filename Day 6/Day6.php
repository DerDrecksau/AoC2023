<?php
$start = microtime(true);
$sum1 = $sum2 = 1;
$file = fopen("input.txt", "r");
$t1 = (microtime(true) - $start) * 1000;
$times = array_values(array_filter(explode(' ', substr(fgets($file), 5))));
$records = array_values(array_filter(explode(' ', substr(fgets($file), 9))));
$t2 = (microtime(true) - $start) * 1000;
foreach ($times as $index => $time) {
    $root = sqrt($time * $time - 4 * $records[$index]);
    $windup = ceil(($time -  $root) / 2);
    $sum1 *= ($time - 2 * $windup + 1);
}
$t3 = (microtime(true) - $start) * 1000;
$time = intval(implode('', $times));
$record = intval(implode('', $records));
$t4 = (microtime(true) - $start) * 1000;
$windup = ceil(($time - (sqrt($time * $time - 4 * $record))) / 2);
$sum2 = ($time - 2 * $windup + 1);
$time = (microtime(true) - $start) * 1000;
echo $t1 . ' ' . $t2 . ' ' . $t3 . ' ' . $t4 . PHP_EOL;
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
