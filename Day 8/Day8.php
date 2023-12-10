<?php
$start = microtime(true);
$sum1 = $sum2 = $step = 0;
$tree = $nodes = [];
$file = fopen("input.txt", "r");
$instructions = trim(fgets($file));
fgets($file);
while (($line = fgets($file)) != null) {
    $node = substr($line, 0, 3);
    $tree[$node] = ['L' => substr($line, 7, 3), 'R' => substr($line, 12, 3)];
    if ($node[2] == 'A') {
        $nodes[] = $node;
    }
}
$node = 'AAA';
while ($node != 'ZZZ') {
    $node = $tree[$node][$instructions[$step]];
    ++$sum1;
    $step = ($step + 1) % strlen($instructions);
}
$step = 0;
$steps = [];
foreach ($nodes as $node) {
    $step = $sum2 = 0;
    while ($node[2] != 'Z') {
        $node = $tree[$node][$instructions[$step]];
        ++$sum2;
        $step = ($step + 1) % strlen($instructions);
    }
    $steps[] = $sum2;
}
$sum2 = array_reduce($steps, fn ($c, $i) => gmp_lcm($c, $i), 1);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
