<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
$rows = $cols = [];
while (($line = fgets($file)) != null) {
    $line = trim($line);
    if (empty($line)) {
        $sum1done = false;
        $sum2done = false;
        $possible = array_combine(range(1, strlen($rows[0]) - 1), array_fill(0, strlen($rows[0]) - 1, 0));
        foreach ($rows as $row) {
            foreach ($possible as $pos => $count) {
                for ($i = 0; $i < $pos && $pos + $i < strlen($row); ++$i) {
                    if ($row[$pos - $i - 1] != $row[$pos + $i]) {
                        if (++$possible[$pos] > 1) {
                            unset($possible[$pos]);
                            break;
                        }
                    }
                }
            }
            if (count($possible) == 0) {
                break;
            }
        }
        if (count($tmp = array_filter($possible, fn ($v) => $v == 0)) == 1) {
            $sum1 += array_keys($tmp)[0];
            $sum1done = true;
        }
        if (count($tmp = array_filter($possible)) == 1) {
            $sum2 += array_keys($tmp)[0];
            $sum2done = true;
        }
        if (!$sum1done || !$sum2done) {
            $possible = array_combine(range(1, count($rows) - 1), array_fill(0, count($rows) - 1, 0));
            for ($col = 0; $col < strlen($rows[0]); ++$col) {
                foreach ($possible as $pos => $count) {
                    for ($i = 0; $i < $pos && $pos + $i < count($rows); ++$i) {
                        if ($rows[$pos - $i - 1][$col] != $rows[$pos + $i][$col]) {
                            if (++$possible[$pos] > ($sum2done ? 0 : 1)) {
                                unset($possible[$pos]);
                                break;
                            }
                        }
                    }
                }
                if (count($possible) == 0) {
                    break;
                }
            }
            if (!$sum1done && count($tmp = array_filter($possible, fn ($v) => $v == 0)) == 1) {
                $sum1 += 100 * array_keys($tmp)[0];
            }
            if (!$sum2done && count($tmp = array_filter($possible)) == 1) {
                $sum2 += 100 * array_keys($tmp)[0];
            }
        }
        $rows = [];
    } else {
        $rows[] = $line;
    }
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
