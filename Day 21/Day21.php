<?php
$start = microtime(true);
const STEPS_PART_1 = 64;
const STEPS_PART_2 = 26501365;
function MOD(int $num, int $mod): int
{
    return ($mod + ($num % $mod)) % $mod;
}
$sum1 = $sum2 = 0;
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
define('WIDTH', strlen($file[0]));
define('HEIGHT', count($file));
$positions = new SplMinHeap();
$cache = $coeff = [];
$counter = [0, 0];
foreach ($file as $x => $line) {
    if (($spos = strpos($line, 'S')) !== false) {
        $positions->insert([0, $x, $spos]);
        $cache[$x][$spos] = true;
    }
}
while (count($positions)) {
    [$steps, $x, $y] = $positions->extract();
    if (($steps - 1) % WIDTH == STEPS_PART_2 % WIDTH && !isset($coeff[$steps - 1])) {
        $coeff[$steps - 1] = $counter[MOD($steps - 1, 2)];
    }
    if ($steps - 1 == STEPS_PART_1) {
        $sum1 = $counter[($steps - 1) % 2];
    }
    if ($steps > STEPS_PART_1 && count($coeff) >= 3) {
        break;
    }
    ++$counter[$steps % 2];
    foreach ([[$x + 1, $y], [$x - 1, $y], [$x, $y + 1], [$x, $y - 1]] as [$nextX, $nextY]) {
        if ($file[MOD($nextX, HEIGHT)][MOD($nextY, WIDTH)] == "#" || isset($cache[$nextX][$nextY])) continue;
        $cache[$nextX][$nextY] = true;
        $positions->insert([$steps + 1, $nextX, $nextY]);
    }
}
$n = intdiv(STEPS_PART_2, WIDTH);
[$c1, $c2, $c3] = array_values($coeff);

$a = ($c3 - 2 * $c2 + $c1) / 2;
$b = $c2 - $c1 - $a;
$c = $c1;

$sum2 = $a * $n * $n + $b * $n + $c;
// echo json_encode($positions) . PHP_EOL;
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
