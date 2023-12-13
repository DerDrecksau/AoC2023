<?php
$start = microtime(true);
const PART2_MULTIPLIER = 1000000;
$rows = [];
$file = fopen("input.txt", "r");
$line = fgets($file);
$cols = array_fill(0, strlen($line) - 2, 0);
do {
    $row = $col = 0;
    while (($col = strpos($line, '#', $col)) !== false) {
        ++$cols[$col++];
        ++$row;
    }
    $rows[] = $row;
} while (($line = fgets($file)) != null);
$above = $left = $sum = $gaps = 0;
$below = $right = array_sum($rows);
for ($current = 0; $current < count($rows); ++$current) {
    $sum += $above * $below + $left * $right;
    $gaps += $above * $below * intval($rows[$current] == 0) + $left * $right * intval($cols[$current] == 0);
    $above += $rows[$current];
    $below -= $rows[$current];
    $left += $cols[$current];
    $right -= $cols[$current];
}
$sum1 = $sum + $gaps;
$sum2 = $sum + $gaps * (PART2_MULTIPLIER - 1);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
