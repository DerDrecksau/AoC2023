<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
$workflows = [];
while (!empty($line = trim(fgets($file)))) {
    $spos = strpos($line, '{');
    $name = substr($line, 0, $spos);
    $rules = substr($line, $spos + 1, -1);
    foreach (explode(',', $rules) as $rule) {
        if (($spos = strpos($rule, ':')) !== false) {
            $res = substr($rule, $spos + 1);
            $val = intval(substr($rule, 2, $spos - 1));
            $type = ($rule[1] == '>') ? 1 : 2;
            $comp = $rule[0];
        } else {
            $res = $rule;
            $type = 0;
            $val = null;
            $comp = null;
        }
        $workflows[$name][] = [$type, $comp, $val, $res];
    }
}
$parts = [];
while (($line = fgets($file)) != null) {
    $line = trim($line);
    if (preg_match('/{x=(\d+),m=(\d+),a=(\d+),s=(\d+)}/', $line, $matches)) {
        $parts[] = ['x' => intval($matches[1]), 'm' => intval($matches[2]), 'a' => intval($matches[3]), 's' => intval($matches[4])];
    }
}
$ranges = ['in' => [['x' => [1, 4000], 'm' => [1, 4000], 'a' => [1, 4000], 's' => [1, 4000], 'parts' => $parts]], 'A' => [], 'R' => []];
do {
    $newRanges = ['A' => $ranges['A'], 'R' => $ranges['R']];
    foreach ($ranges as $flow => $rangeParts) {
        if ($flow === 'A' || $flow === 'R') {
            continue;
        }
        foreach ($rangeParts as $range) {
            foreach ($workflows[$flow] as $rule) {
                list($type, $comp, $val, $res) = $rule;
                if ($type == 0) {
                    $newRanges[$res][] = $range;
                } else {
                    $rangeStart = $range[$comp][0];
                    $rangeEnd = $range[$comp][1];
                    if ($type == 1) {
                        if ($val < $rangeStart) {
                            $newRanges[$res][] = $range;
                        } else if ($val < $rangeEnd) {
                            $newRange = $range;
                            $newRange[$comp] =  [$val + 1, $rangeEnd];
                            $partList = $range['parts'];
                            $newRange['parts'] = $range['parts'] = [];
                            foreach ($partList as $part) {
                                if( $part[$comp] > $val) {
                                    $newRange['parts'][] = $part;
                                } else {
                                    $range['parts'][] = $part;
                                }
                            }
                            $newRanges[$res][] = $newRange;
                            $range[$comp] = [$rangeStart, $val];
                        }
                    } else {
                        if ($val > $rangeEnd) {
                            $newRanges[$res][] = $range;
                        } else if ($val > $rangeStart) {
                            $newRange = $range;
                            $newRange[$comp] =  [$rangeStart, $val - 1];
                            $partList = $range['parts'];
                            $newRange['parts'] = $range['parts'] = [];
                            foreach ($partList as $part) {
                                if ($part[$comp] < $val) {
                                    $newRange['parts'][] = $part;
                                } else {
                                    $range['parts'][] = $part;
                                }
                            }
                            $newRanges[$res][] = $newRange;
                            $range[$comp] = [$val, $rangeEnd];
                        }
                    }
                }
            }
        }
    }
    $ranges = $newRanges;
} while (!empty(array_diff(array_keys($ranges), ['A', 'R'])));
$sum1 = array_sum(array_map('array_sum', array_merge(...array_column($ranges['A'], 'parts'))));
foreach ($ranges['A'] as $range) {
    $sum2 += ($range['x'][1] - $range['x'][0] + 1) * ($range['m'][1] - $range['m'][0] + 1) * ($range['a'][1] - $range['a'][0] + 1) * ($range['s'][1] - $range['s'][0] + 1);
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
