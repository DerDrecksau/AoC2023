<?php

$start = microtime(true);
$maps = [];
$map = -1;
$file = fopen("input.txt", "r");
$seeds = array_map('intval', explode(' ', substr(fgets($file), 7)));
while (($line = fgets($file)) != null) {
    if ($line == PHP_EOL) {
        continue;
    }
    $parse =  explode(' ', $line);
    if (count($parse) == 2) {
        ++$map;
        $maps[] = [];
    } else {
        $maps[$map][] = array_map('intval', $parse);
    }
}
$seedRanges = [];
foreach (range(0, count($seeds) - 1, 2) as $i) {
    $seedRanges[] = [$seeds[$i], $seeds[$i + 1]];
}
foreach ($maps as $index => $map) {
    usort($map, fn ($a1, $a2) => $a1[1] <=> $a2[1]);
    $maps[$index] = $map;
}
foreach ($maps as $map) {
    $newSeeds = [];
    foreach ($seeds as $seed) {
        $found = false;
        for ($x = 0; $x < count($map); ++$x) {
            $range = $map[$x];
            if ($seed < $range[1]) {
                break;
            }
            if ($seed < $range[1] + $range[2]) {
                $newSeeds[] = $seed - $range[1] + $range[0];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $newSeeds[] = $seed;
        }
    }
    usort($seedRanges, fn ($a1, $a2) => $a1[0] <=> $a2[0]);
    $newSeedRanges = [];
    $mapValues = $map[0];
    $range = $seedRanges[0];
    $m = $r = 0;
    while (!is_null($mapValues) && !is_null($range)) {
        $mapStart = $mapValues[1];
        $mapEnd = $mapStart + $mapValues[2];
        $mapOffset = $mapValues[0] - $mapValues[1];
        $rangeStart = $range[0];
        $rangeEnd = $rangeStart + $range[1];
        if ($rangeEnd < $mapStart) { // range to low
            $newSeedRanges[] = $range;
            $range = $seedRanges[++$r] ?? null;
            continue;
        } else if ($rangeEnd <= $mapEnd && $rangeStart < $mapStart) { // overlap: range lower
            $newSeedRanges[] = [$rangeStart, $mapStart - $rangeStart];
            $newSeedRanges[] = [$mapStart + $mapOffset, $rangeEnd - $mapStart];
            $mapValues = [$rangeEnd + $mapOffset, $rangeEnd, $mapEnd - $rangeEnd];
            $range = $seedRanges[++$r] ?? null;
        } else if ($rangeEnd <= $mapEnd) { // range contained
            $newSeedRanges[] = [$rangeStart + $mapOffset, $range[1]];
            $mapValues = [$rangeEnd + $mapOffset, $rangeEnd, $mapEnd - $rangeEnd];
            $range = $seedRanges[++$r] ?? null;
        } else if ($rangeStart <= $mapStart) { // map contained
            $newSeedRanges[] = [$rangeStart, $mapStart - $rangeStart];
            $newSeedRanges[] = [$mapStart + $mapOffset, $mapValues[2]];
            $range = [$mapEnd, $rangeEnd - $mapEnd];
            $mapValues = $map[++$m] ?? null;
        } else if ($rangeStart < $mapEnd) { // overlap: range higher
            $newSeedRanges[] = [$rangeStart + $mapOffset, $mapEnd - $rangeStart];
            $range = [$mapEnd, $range[1] - $mapEnd + $rangeStart];
            $mapValues = $map[++$m] ?? null;
        } else { //range to high;
            $mapValues = $map[++$m] ?? null;
        }
        if (is_null($mapValues)) {
            $newSeedRanges = array_merge($newSeedRanges, [$range], array_slice($seedRanges, ++$r));
        }
    }
    $seedRanges = $newSeedRanges;
    $seeds = $newSeeds;
}
$time = (microtime(true) - $start) * 1000;
echo min($seeds) . ' - ' . min(array_column($seedRanges, 0)) . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
