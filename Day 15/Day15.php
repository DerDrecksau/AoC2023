<?php
$start = microtime(true);
$cache = [];
function myhash(string $input): int
{
    global $cache;
    if (isset($cache[$input])) {
        return $cache[$input];
    }
    $sum = 0;
    for ($i = 0; $i < strlen($input); ++$i) {
        $sum += ord($input[$i]);
        $sum = ($sum * 17) % 256;
    }
    $cache[$input] = $sum;
    return $sum;
}
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
$line = fgets($file);
$hashmap = [];
foreach (explode(',', $line) as $instruction) {
    $sum1 += myhash($instruction);
    if (str_ends_with($instruction, '-')) {
        $label = substr($instruction, 0, -1);
        $hash = myhash($label);
        unset($hashmap[$hash][$label]);
    } else {
        $label = substr($instruction, 0, -2);
        $lens = intval(substr($instruction, -1));
        $hash = myhash($label);
        $hashmap[$hash][$label] = $lens;
    }
}
$sum1 += myhash($instruction);
foreach ($hashmap as $box => $lenses) {
    $pos = 0;
    foreach ($lenses as $lens) {
        $sum2 += ($box + 1) * $lens * (++$pos);
    }
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
