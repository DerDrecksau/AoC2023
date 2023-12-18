<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$loop = $position = [];
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
foreach ($file as $line => $content) {
    if (($spos = strpos($content, 'S')) !== false) {
        ++$sum1;
        $loop[] = [$line, $spos];
        if (in_array($file[$line - 1][$spos], ['|', 'F', '7'])) {
            $position = [$line - 1, $spos, 0];
        } else if (in_array($file[$line][$spos + 1], ['-', 'J'])) {
            $position = [$line, $spos + 1, 1];
        } else {
            $position = [$line + 1, $spos, 2];
        }
        break;
    }
}
if (empty($position)) {
    exit(1);
}
do {
    ++$sum1;
    switch ($file[$position[0]][$position[1]]) {
        case "|":
            $position[0] += $position[2] == 0 ? -1 : 1;
            break;
        case "-":
            $position[1] += $position[2] == 1 ? 1 : -1;
            break;
        case "L":
            $loop[] = [$position[0], $position[1]];
            $position = $position[2] == 2 ? [$position[0], $position[1] + 1, 1] : [$position[0] - 1, $position[1], 0];
            break;
        case "J":
            $loop[] = [$position[0], $position[1]];
            $position = $position[2] == 2 ? [$position[0], $position[1] - 1, 3] : [$position[0] - 1, $position[1], 0];
            break;
        case "7":
            $loop[] = [$position[0], $position[1]];
            $position = $position[2] == 0 ? [$position[0], $position[1] - 1, 3] : [$position[0] + 1, $position[1], 2];
            break;
        case "F":
            $loop[] = [$position[0], $position[1]];
            $position = $position[2] == 0 ? [$position[0], $position[1] + 1, 1] : [$position[0] + 1, $position[1], 2];
            break;
        default:
            break;
    }
} while ($position[0] != $loop[0][0] || $position[1] != $loop[0][1]);
$sum1 /= 2;
foreach ($loop as $i => $point) {
    $other = $loop[$i + 1] ?? $loop[0];
    $sum2 += $point[0] * $other[1];
    $sum2 -= $point[1] * $other[0];
}
$sum2 = abs($sum2) / 2 - $sum1 + 1;
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
