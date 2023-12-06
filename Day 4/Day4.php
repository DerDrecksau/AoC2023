<?php
$sum1 =  $sum2 = $game = 0;
$file = file("input.txt");
$start = microtime(true);
$counts = array_fill(1, count($file), 1);
foreach ($file as $line) {
    ++$game;
    if (preg_match_all('/.(?=( \d+) .*?\|.*?\1\b)/', $line, $matches)) {
        $winning = count($matches[0]);
    } else {
        $winning = 0;
    }
    if ($winning > 0) {
        foreach (range(1, $winning) as $i) {
            $counts[$game + $i] += $counts[$game];
        }
        $sum1 += 1 << ($winning - 1);
    }
    $sum2 += $counts[$game];
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
