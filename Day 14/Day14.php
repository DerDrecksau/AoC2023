<?php
$start = microtime(true);

$sum1 = $sum2 = 0;
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
define('WIDTH', strlen($file[0]));
define('LENGTH', strlen($file[0]));
function tilt(string $grid, int $dir): string
{
    switch ($dir) {
        default:
        case '0':
            $deltaX = $step = 1;
            $deltaY = $endX = WIDTH;
            $startX = $startY = 0;
            $endY = LENGTH;
            break;
        case 1:
            $deltaX = $endY = WIDTH;
            $deltaY = $step = 1;
            $startX = $startY = 0;
            $endX = LENGTH;
            break;
        case 2:
            $deltaX = 1;
            $deltaY = $endX = WIDTH;
            $startY = LENGTH - 1;
            $startX = 0;
            $endY = $step = -1;
            break;
        case 3:
            $deltaY = 1;
            $deltaX = WIDTH;
            $endX = LENGTH;
            $startY = WIDTH - 1;
            $startX = 0;
            $endY = $step = -1;
            break;
    }
    for ($x = $startX; $x < $endX; ++$x) {
        $target = $startY;
        for ($y = $startY; $y != $endY; $y += $step) {
            switch ($grid[$x * $deltaX + $y * $deltaY]) {
                default:
                case '.':
                    break;
                case 'O':
                    if ($y != $target) {
                        $grid[$x * $deltaX + $y * $deltaY] = '.';
                        $grid[$x * $deltaX + $target * $deltaY] = 'O';
                    }
                    $target += $step;
                    break;
                case '#':
                    $target = $y + $step;
            }
        }
    }
    return $grid;
}
function cycle(string $grid): string
{
    foreach (range(0, 3) as $dir) {
        $grid = tilt($grid, $dir);
    }
    return $grid;
}
function calculateLoad(string $grid): int
{
    $sum = 0;
    for ($y = LENGTH - 1; $y >= 0; --$y) {
        $sum += substr_count($grid, 'O', $y * LENGTH, WIDTH) * (LENGTH - $y);
    }
    return $sum;
}
$grid = implode('', $file);
$sum1 = calculateLoad(tilt($grid, 0));
$configs = [];
for ($count = 0; $count < 1000000000; ++$count) {
    $grid = cycle($grid);
    if ($count == 10000) {
        break;
    }
    // echo wordwrap($grid, WIDTH, PHP_EOL, true) . PHP_EOL . calculateLoad($grid) . PHP_EOL;
    // if (($configs[$grid] ?? -1) >= 0) {
    //     $first = $configs[$grid];
    //     $target = $first + ((999999999 - $first) % ($count - $first));
    //     $grid = array_search($target, $configs);
    //     $sum2 = calculateLoad($grid);
    //     break;
    // }
    // $configs[$grid] = $count;
}
// echo wordwrap(cycle(cycle($grid)), WIDTH, PHP_EOL, true);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
