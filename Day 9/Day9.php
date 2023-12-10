<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    $seq = array_map('intval', explode(' ', $line));
    $forwardDiffs = [$seq];
    $backwardsDiffs = [array_reverse($seq)];
    do {
        $newSeq = [];
        for ($i = 1; $i < count($seq); ++$i) {
            $newSeq[] = $seq[$i] - $seq[$i - 1];
        }
        $forwardDiffs[] = $newSeq;
        $backwardsDiffs[] = array_reverse($newSeq);
        $seq = $newSeq;
    } while (array_reduce($seq, fn ($c, $i) => $c || $i != 0, false));
    $sum1 += array_sum(array_column($backwardsDiffs, 0));
    $first = array_reduce(array_reverse(array_column($forwardDiffs, 0)), fn ($c, $i) => $i - $c, 0);
    $sum2 += $first;
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
