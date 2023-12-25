<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
define('WIDTH', strlen($file[0]));
define('HEIGHT', count($file));
$spos =  strpos($file[0], '.');
$node = '0,' . $spos;
$nodes1 = $nodes2 = [$node => []];
$cache = [];
$todo = [[1, $spos, 1, $node]];
$exit = "";
while (!empty($todo)) {
    $next = [];
    foreach ($todo as [$x, $y, $dir, $node]) {
        $length = 0;
        $forward = $backward = true;
        do {
            $cache[$x . ',' . $y] = true;
            $tile = $file[$x][$y];
            if ($tile == '>') {
                if ($dir != 0) {
                    $forward = false;
                }
                if ($dir != 3) {
                    $backward = false;
                }
            } else if ($tile == '<') {
                if ($dir != 3) {
                    $forward = false;
                }
                if ($dir != 0) {
                    $backward = false;
                }
            } else if ($tile == '^') {
                if ($dir != 2) {
                    $forward = false;
                }
                if ($dir != 1) {
                    $backward = false;
                }
            } else if ($tile == 'v') {
                if ($dir != 1) {
                    $forward = false;
                }
                if ($dir != 2) {
                    $backward = false;
                }
            }
            ++$length;
            $possible = [[$x, $y + 1], [$x + 1, $y], [$x - 1, $y], [$x, $y - 1]];
            unset($possible[3 - $dir]);
            foreach ($possible as $i => [$nextX, $nextY]) {
                if ($nextX == HEIGHT - 1 && $file[$nextX][$nextY] == '.') {
                    $exit = $nextX . ',' . $nextY;
                    if ($forward) {
                        $nodes1[$node][$exit] = $length + 1;
                    }
                    $nodes2[$node][$exit] = $length + 1;
                    break 3;
                } else if ($nextX < 0 || $nextX >= HEIGHT || $nextY < 0 || $nextY >= WIDTH || $file[$nextX][$nextY] == '#') {
                    unset($possible[$i]);
                }
            }
            if (count($possible) == 1) {
                $dir = array_keys($possible)[0];
                [$x, $y] = $possible[$dir];
            } else {
                if ($forward) {
                    $nodes1[$node][$x . ',' . $y] = $length;
                }
                if ($backward) {
                    $nodes1[$x . ',' . $y][$node] = $length;
                }
                $nodes2[$node][$x . ',' . $y] = $length;
                $nodes2[$x . ',' . $y][$node] = $length;
                foreach ($possible as $dir => [$nextX, $nextY]) {
                    if (!isset($cache[$nextX . ',' . $nextY])) {
                        $next[] = [$nextX, $nextY, $dir, $x . ',' . $y];
                    }
                }
                break;
            }
        } while (true);
    }
    $todo = $next;
}
function scan(string $node, int $length, array $path, array $nodes): int
{
    global $exit;
    if ($node == $exit) {
        return $length;
    }
    $paths = [];
    foreach ($nodes[$node] as $edge => $distance) {
        if (!isset($path[$edge])) {
            $nextPath = $path;
            $nextPath[$edge] = true;
            $paths[] = scan($edge, $length + $distance, $nextPath, $nodes);
        }
    }
    return empty($paths) ? 0 : max($paths);
}
$sum2 = scan('0,' . $spos, 0, ['0,' . $spos => true], $nodes2);
$sum1 = scan('0,' . $spos, 0, ['0,' . $spos => true], $nodes1);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
