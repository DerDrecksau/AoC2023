<?php
$start = microtime(true);
$cache = [];
function r(string $springs, array $config): int
{
    global $cache;
    if (isset($cache[$springs . ' ' . implode(',', $config)])) {
        return $cache[$springs . ' ' . implode(',', $config)];
    }
    // echo $springs . ' ' . implode(',', $config) . PHP_EOL;
    $target = array_shift($config);
    $sum = 0;
    if (preg_match_all('/(?=(?<=[.?]|^)[#?]{' . $target . '}(?:[.?]|$))/', $springs, $matches, PREG_OFFSET_CAPTURE)) {
        // print_r($matches[0]);
        foreach (array_column($matches[0], 1) as $offset) {
            if (!str_contains(substr($springs, 0, $offset), '#')) {
                if (empty($config)) {
                    $sum += str_contains(substr($springs, $offset + $target + 1), '#') ? 0 : 1;
                } else {
                    $sum += r(substr($springs, $offset + $target + 1), $config);
                }
            }
        }
    }
    $cache[$springs . ' ' . implode(',', array_merge([$target], $config))] = $sum;
    return $sum;
}
$sum1 = $sum2 = $count = 0;
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    $line = explode(' ', trim($line));
    $springs = $line[0];
    $config = explode(',', $line[1]);
    $sum1 += r($springs, $config) . PHP_EOL;
    $sum2 += r(
        implode('?', [$springs, $springs, $springs, $springs, $springs]),
        array_merge($config, $config, $config, $config, $config)
    );
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
