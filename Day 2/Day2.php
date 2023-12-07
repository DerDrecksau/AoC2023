<?php
$start = microtime(true);
$sum1 = $sum2 = $game = 0;
$max = ['red' => 12, 'green' => 13, 'blue' => 14];
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    $possible = 1;
    ++$game;
    $min = ['red' => 0, 'green' => 0, 'blue' => 0];
    if (preg_match_all("/(\d+) (red|green|blue)/", $line, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $min[$match[2]] = max(intval($match[1]), $min[$match[2]]);
            if (intval($match[1]) > $max[$match[2]]) {
                $possible = 0;
            }
        }
    }
    $sum1 += $possible * $game;
    $sum2 += $min['red'] * $min['green'] * $min['blue'];
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;

