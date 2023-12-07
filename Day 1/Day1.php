<?php
$start = microtime(true);
$values = array_flip(range(0, 9)) + array_flip(['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine']);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    if (preg_match("/^[^\d]*(\d).*?(?:(\d)[^\d]*)?$/", $line, $matches)) {
        $sum1 += intval($matches[1]) * 10 + intval($matches[2] ?? $matches[1]);
    }
    if (preg_match_all("/(?=(\d|one|two|three|four|five|six|seven|eight|nine))/", $line, $matches2)) {
        $d = $values[$matches2[1][0]] * 10 + $values[array_pop($matches2[1])];
        $sum2 += $d;
    }
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
