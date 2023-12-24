<?php
$start = microtime(true);
$sum1 = $sum2 = $count = 0;
$file = fopen("input.txt", "r");
$bricks = $stack = [];
$max = [0, 0, 0];
while (($line = fgets($file)) != null) {
    $line = trim($line);
    $line = explode('~', $line);
    $coords = [array_map('intval', explode(',', $line[0])), array_map('intval',  explode(',', $line[1]))];
    for ($i = 0; $i < 3; ++$i) {
        $max[$i] = max($max[$i], $coords[0][$i], $coords[1][$i]);
    }
    $diff = [$coords[1][0] - $coords[0][0], $coords[1][1] - $coords[0][1], $coords[1][2] - $coords[0][2]];
    $length = max($diff);
    if ($length == 0) {
        $coords = [$coords[1], $coords[0]];
        $length = -min($diff);
    }
    if ($diff[0] != 0) {
        $dir = 0;
    } else if ($diff[1] != 0) {
        $dir = 1;
    } else {
        $dir = 2;
    }
    $bricks[$count] = [$coords[0], $dir, $length];
    for ($pos = $coords[0]; $pos[$dir] <= $coords[1][$dir]; ++$pos[$dir]) {
        if (isset($stack[$pos[0]][$pos[1]][$pos[2]])) {
            echo json_encode([$stack[$pos[0]][$pos[1]][$pos[2]], $count]) . PHP_EOL;
        }
        $stack[$pos[0]][$pos[1]][$pos[2]] = $count;
    }
    ++$count;
}
for ($z = 0; $z <= $max[2]; ++$z) {
    for ($x = 0; $x <= $max[0]; ++$x) {
        for ($y = 0; $y <= $max[1]; ++$y) {
            if (isset($stack[$x][$y][$z])) {
                $brick = $bricks[$stack[$x][$y][$z]];
                $tiles = [[$brick[0][0], $brick[0][1]]];
                if ($brick[1] != 2) {
                    $tile = $tiles[0];
                    for ($i = 1; $i <= $brick[2]; ++$i) {
                        ++$tile[$brick[1]];
                        $tiles[] = $tile;
                    }
                }
                $bricks[$stack[$x][$y][$z]][3] = $brick[0][2] - 1;
                foreach ($tiles as $tile) {
                    for ($dz = $brick[0][2] - 1; $dz > 0; --$dz) {
                        if (isset($stack[$tile[0]][$tile[1]][$dz])) {
                            $bricks[$stack[$x][$y][$z]][3] = min($bricks[$stack[$x][$y][$z]][3], $brick[0][2] - 1 - $dz + $bricks[$stack[$tile[0]][$tile[1]][$dz]][3]);
                            break;
                        }
                    }
                }
            }
        }
    }
}
$stack = [];
$max = [0, 0, 0];
foreach ($bricks as $index => $brick) {
    $bricks[$index][0][2] -= $brick[3];
    unset($bricks[$index][3]);
    list($first, $dir, $length) = $bricks[$index];
    for ($pos = $first; $pos[$dir] <= $first[$dir] + $length; ++$pos[$dir]) {
        if (isset($stack[$pos[0]][$pos[1]][$pos[2]])) {
            echo json_encode([$stack[$pos[0]][$pos[1]][$pos[2]], $index]) . PHP_EOL;
        }
        $stack[$pos[0]][$pos[1]][$pos[2]] = $index;
        for ($i = 0; $i < 3; ++$i) {
            $max[$i] = max($max[$i], $pos[$i]);
        }
    }
}
for ($z = 0; $z <= $max[2]; ++$z) {
    for ($x = 0; $x <= $max[0]; ++$x) {
        for ($y = 0; $y <= $max[1]; ++$y) {
            if (isset($stack[$x][$y][$z])) {
                $brick = $bricks[$stack[$x][$y][$z]];
                $tiles = [[$brick[0][0], $brick[0][1]]];
                if ($brick[1] != 2) {
                    $tile = $tiles[0];
                    for ($i = 1; $i <= $brick[2]; ++$i) {
                        ++$tile[$brick[1]];
                        $tiles[] = $tile;
                    }
                }
                foreach ($tiles as $tile) {
                    if (isset($stack[$tile[0]][$tile[1]][$brick[0][2] - 1])) {
                        $bricks[$stack[$x][$y][$z]][4][] = $stack[$tile[0]][$tile[1]][$brick[0][2] - 1];
                    }
                }
                $bricks[$stack[$x][$y][$z]][4] = array_unique($bricks[$stack[$x][$y][$z]][4] ?? []);
            }
        }
    }
}
$possible = array_keys($bricks);
$support = [];
foreach ($bricks as $i => $brick) {
    if (count($brick[4]) == 1) {
        unset($possible[$brick[4][0]]);
    }
    foreach($brick[4] as $other) {
        $support[$other][] = $i;
    }
}
foreach($support as $brick => $others) {
    $disintegrated = [$brick];
    while(!empty($others)) {
        $next = [];
        foreach($others as $other) {
            if(empty(array_diff($bricks[$other][4], $disintegrated))) {
                $disintegrated[] = $other;
                $next = array_unique(array_merge($next, $support[$other] ?? []));
            }
        }
        $others = $next;
    }
    $sum2 += count(array_unique($disintegrated)) - 1;
}
$sum1 = count($possible);
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
