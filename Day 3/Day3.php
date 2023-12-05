<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$gears = [];
function test(array $file, int $x, int $y, int &$add, array &$gear)
{
    $test = $file[$x][$y];
    // echo $x . ':' . $y . '=>' . $test . PHP_EOL;
    if ($test != '.' && !is_numeric($test)) {
        $add = 1;
        if ($test == '*') {
            $gear[$x][$y] = true;
        }
    }
}
$file = file("input.txt");
foreach ($file as $index => $line) {
    if (preg_match_all("/(\d+)/", $line, $matches, PREG_OFFSET_CAPTURE)) {
        //var_dump($matches[1]);break;
        foreach ($matches[1] as $match) {
            $add = 0;
            $gear = [];
            foreach (range($index - 1, $index + 1) as $x) {
                test($file, $x, $match[1] - 1, $add, $gear);
                test($file, $x, $match[1] + strlen($match[0]), $add, $gear);
            }
            foreach (range($match[1], $match[1] + strlen($match[0]) - 1) as $y) {
                test($file, $index - 1, $y, $add, $gear);
                test($file, $index + 1, $y, $add, $gear);
            }
            foreach ($gear as $row => $columns) {
                foreach ($columns as $column => $val) {
                    $gears[$row][$column][] = intval($match[0]);
                }
            }
            $sum1 += $add * intval($match[0]);
        }
    }
}
foreach ($gears as $line) {
    foreach ($line as $gear) {
        if (count($gear) == 2) {
            $sum2 += $gear[0] * $gear[1];
        }
    }
}
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo (microtime(true) - $start) * 1000 . 'ms' . PHP_EOL;
