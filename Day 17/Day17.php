<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
define('HEIGHT', count($file));
define('WIDTH', strlen($file[0]));
$file[0][0] = 0;
$heatloss = array_fill(0, HEIGHT, array_fill(0, WIDTH, array_fill(0, 4, array_fill(0, 10, 9999))));
$heatloss[0][0] = array_fill(0, 4, [0, 9999, 9999, 9999, 9999, 9999, 9999, 9999, 9999, 9999]);
do {
    $done = true;
    for ($i = 0; $i < HEIGHT; ++$i) {
        for ($j = 0; $j < WIDTH; ++$j) {
            if ($i == 0 && $j == 0) {
                continue;
            }
            if ($i > 0) {
                $up = $heatloss[$i - 1][$j];
                $min = min(min(array_slice($up[0], 3)), min(array_slice($up[2], 3)));
                for ($k = 0; $k < 9; ++$k) {
                    $loss = intval($file[$i][$j]) + $up[1][$k];
                    if ($heatloss[$i][$j][1][$k + ($i+$j == 1 ? 0 : 1)] > $loss) {
                        $done = false;
                        $heatloss[$i][$j][1][$k + ($i + $j == 1 ? 0 : 1)] = $loss;
                    }
                }
                $loss = intval($file[$i][$j]) + $min;
                if ($heatloss[$i][$j][1][0] > $loss) {
                    $done = false;
                    $heatloss[$i][$j][1][0] = $loss;
                }
                // echo implode(PHP_EOL, array_map(fn ($v) => json_encode($v), $heatloss)) . PHP_EOL;
                // exit;
            }
            if ($i < HEIGHT - 1) {
                $down = $heatloss[$i + 1][$j];
                $min = min(min(array_slice($down[0], 3)), min(array_slice($down[2], 3)));
                for ($k = 0; $k < 9; ++$k) {
                    $loss = intval($file[$i][$j]) + $down[3][$k];
                    if ($heatloss[$i][$j][3][$k + ($i + $j == 1 ? 0 : 1)] > $loss) {
                        $done = false;
                        $heatloss[$i][$j][3][$k + ($i + $j == 1 ? 0 : 1)] = $loss;
                    }
                }
                $loss = intval($file[$i][$j]) + $min;
                if ($heatloss[$i][$j][3][0] > $loss) {
                    $done = false;
                    $heatloss[$i][$j][3][0] = $loss;
                }
            }
            if ($j > 0) {
                $left = $heatloss[$i][$j - 1];
                $min = min(min(array_slice($left[1], 3)), min(array_slice($left[3], 3)));
                for ($k = 0; $k < 9; ++$k) {
                    $loss = intval($file[$i][$j]) + $left[0][$k];
                    if ($heatloss[$i][$j][0][$k + ($i + $j == 1 ? 0 : 1)] > $loss) {
                        $done = false;
                        $heatloss[$i][$j][0][$k + ($i + $j == 1 ? 0 : 1)] = $loss;
                    }
                }
                $loss = intval($file[$i][$j]) + $min;
                if ($heatloss[$i][$j][0][0] > $loss) {
                    $done = false;
                    $heatloss[$i][$j][0][0] = $loss;
                }
            }
            if ($j < WIDTH - 1) {
                $right = $heatloss[$i][$j + 1];
                $min = min(min(array_slice($right[1], 3)), min(array_slice($right[3], 3)));
                for ($k = 0; $k < 9; ++$k) {
                    $loss = intval($file[$i][$j]) + $right[2][$k];
                    if ($heatloss[$i][$j][2][$k + ($i + $j == 1 ? 0 : 1)] > $loss) {
                        $done = false;
                        $heatloss[$i][$j][2][$k + ($i + $j == 1 ? 0 : 1)] = $loss;
                    }
                }
                $loss = intval($file[$i][$j]) + $min;
                if ($heatloss[$i][$j][2][0] > $loss) {
                    $done = false;
                    $heatloss[$i][$j][2][0] = $loss;
                }
            }
        }
    }
    // echo implode(PHP_EOL, array_map(fn ($v) => json_encode($v), $heatloss)) . PHP_EOL;
} while (!$done);
// echo implode(PHP_EOL, array_map(fn ($v) => json_encode($v), $heatloss)) . PHP_EOL;
$sum1 = min(min(array_slice($heatloss[HEIGHT - 1][WIDTH - 1][0], 3)), min(array_slice($heatloss[HEIGHT - 1][WIDTH - 1][1], 3)));
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
