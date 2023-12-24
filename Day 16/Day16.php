<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
$starts = [];
for ($i = 0; $i < count($file); ++$i) {
    $starts[] = [$i, -1, 0];
    $starts[] = [$i, strlen($file[0]), 2];
}
for ($i = 0; $i < strlen($file[0]); ++$i) {
    $starts[] = [-1, $i, 1];
    $starts[] = [count($file), $i, 3];
}
$sums = [];
foreach ($starts as $first) {
    $beams = [$first];
    $energized = [];
    $done = [];
    do {
        $next = [];
        foreach ($beams as $beam) {
            $pos = match ($beam[2]) {
                0 => [$beam[0], $beam[1] + 1, $beam[2]],
                1 => [$beam[0] + 1, $beam[1], $beam[2]],
                2 => [$beam[0], $beam[1] - 1, $beam[2]],
                3 => [$beam[0] - 1, $beam[1], $beam[2]],
            };
            if ($pos[0] >= 0 && $pos[0] < count($file) && $pos[1] >= 0 && $pos[1] < strlen($file[0]) && !($done[$pos[0] . '.' . $pos[1] . '.' . $pos[2]] ?? false)) {
                $energized[$pos[0] . '.' . $pos[1]] = 1;
                $done[$pos[0] . '.' . $pos[1] . '.' . $pos[2]] = true;
                switch ($file[$pos[0]][$pos[1]]) {
                    case '.':
                        $next[] = $pos;
                        break;
                    case '\\':
                        $pos[2] = (5 - $pos[2]) % 4;
                        $next[] = $pos;
                        break;
                    case '/':
                        $pos[2] = 3 - $pos[2];
                        $next[] = $pos;
                        break;
                    case '-':
                        if ($pos[2] == 0 || $pos[2] == 2) {
                            $next[] = $pos;
                        } else {
                            $next[] = [$pos[0], $pos[1], 0];
                            $next[] = [$pos[0], $pos[1], 2];
                        }
                        break;
                    case '|':
                        if ($pos[2] == 1 || $pos[2] == 3) {
                            $next[] = $pos;
                        } else {
                            $next[] = [$pos[0], $pos[1], 1];
                            $next[] = [$pos[0], $pos[1], 3];
                        }
                        break;
                }
            }
        }
        $beams = $next;
    } while (count($beams) > 0);
    $sums[] = count($energized);
}
$sum1 = $sums[0];
$sum2 = max($sums);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
