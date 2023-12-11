<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$loop = $positions = [];
$file = file("input.txt", FILE_IGNORE_NEW_LINES);
foreach ($file as $line => $content) {
    $spos = strpos($content, 'S');
    if ($spos !== false) {
        ++$sum1;
        $loop[$line][] = $spos;
        $schar = ['L', '|', 'J', 'F', '-', '7'];
        if (in_array($file[$line - 1][$spos], ['|', 'F', '7'])) {
            $positions[] = [$line - 1, $spos, 0];
            $schar = array_diff($schar, ['-', 'F',  '7']);
        }
        if (in_array($file[$line][$spos + 1], ['-', 'J', '7'])) {
            $positions[] = [$line, $spos + 1, 1];
            $schar = array_diff($schar, ['|', 'J',  '7']);
        }
        if (in_array($file[$line + 1][$spos], ['|', 'J', 'L'])) {
            $positions[] = [$line + 1, $spos, 2];
            $schar = array_diff($schar, ['-', 'J',  'L']);
        }
        if (in_array($file[$line][$spos - 1], ['-', 'F', 'L'])) {
            $positions[] = [$line, $spos - 1, 3];
            $schar = array_diff($schar, ['|', 'F',  'L']);
        }
        $file[$line][$spos] = array_pop($schar);
    }
}
do {
    ++$sum1;
    foreach ($positions as $index => $position) {
        $loop[$position[0]][] = $position[1];
        switch ($file[$position[0]][$position[1]]) {
            case "|":
                $positions[$index] = $position[2] == 0 ? [$position[0] - 1, $position[1], 0] : [$position[0] + 1, $position[1], 2];
                break;
            case "-":
                $positions[$index] = $position[2] == 1 ? [$position[0], $position[1] + 1, 1] : [$position[0], $position[1] - 1, 3];
                break;
            case "L":
                $positions[$index] = $position[2] == 2 ? [$position[0], $position[1] + 1, 1] : [$position[0] - 1, $position[1], 0];
                break;
            case "J":
                $positions[$index] = $position[2] == 2 ? [$position[0], $position[1] - 1, 3] : [$position[0] - 1, $position[1], 0];
                break;
            case "7":
                $positions[$index] = $position[2] == 0 ? [$position[0], $position[1] - 1, 3] : [$position[0] + 1, $position[1], 2];
                break;
            case "F":
                $positions[$index] = $position[2] == 0 ? [$position[0], $position[1] + 1, 1] : [$position[0] + 1, $position[1], 2];
                break;
            default:
                break;
        }
    }
} while ($positions[0][0] != $positions[1][0] || $positions[0][1] != $positions[1][1]);
$loop[$positions[0][0]][] = $positions[0][1];
for ($line = 0; $line < count($file); ++$line) {
    $inside = false;
    for ($pos = 0; $pos < strlen($file[$line]); ++$pos) {
        if (in_array($pos, $loop[$line] ?? [])) {
            switch ($file[$line][$pos]) {
                case '|':
                    $inside = !$inside;
                    break;
                case 'F':
                    while ($file[$line][$pos + 1] == '-') {
                        ++$pos;
                    }
                    if ($file[$line][++$pos] == 'J') {
                        $inside = !$inside;
                    }
                    break;
                case 'L':
                    while ($file[$line][$pos + 1] == '-') {
                        ++$pos;
                    }
                    if ($file[$line][++$pos] == '7') {
                        $inside = !$inside;
                    }
                    break;
            }
        } else if ($inside) {
            $file[$line][$pos] = 'I';
            ++$sum2;
        } else {
            $file[$line][$pos] = 'O';
        }
    }
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
