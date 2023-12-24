<?php
$start = microtime(true);
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
$modules = $pulses = $cache = $cycles = $changes = [];
$todo = [];
while (($line = fgets($file)) != null) {
    $line = trim($line);
    $line = explode(' -> ', $line);
    $targets = explode(', ', $line[1]);
    $name = substr($line[0], 1);
    $modules[$name][0] = $line[0][0];
    $modules[$name][1] = $targets;
    foreach ($targets as $target) {
        $modules[$target][2][] = $name;
    }
}
foreach ($modules as $name => $module) {
    if ($module[0] == '%') {
        $modules[$name][2] = 0;
    } else if ($module[0] == '&') {
        $modules[$name][2] = array_combine($module[2], array_fill(0, count($module[2]), 0));
        if (!in_array('rx', $module[1])) {
            $todo[] = $name;
        }
        $cache[$name][md5(serialize($modules[$name][2]))] = true;
    } else {
        unset($modules[$name][2]);
    }
}
function pushButton(): array
{
    global $modules, $todo;
    $sum = [1, 0, []];
    $pulses = [['roadcaster', 0, 'button']];
    do {
        $next = [];
        foreach ($pulses as $pulse) {
            foreach ($todo as $name) {
                if ($pulse[1] == 1 && $pulse[2] == $name) {
                    $sum[2][$name] = true;
                }
            }
            $module = $modules[$pulse[0]];
            if ($module[0] == '%') {
                if ($pulse[1] == 0) {
                    $signal = 1 - $module[2];
                    $modules[$pulse[0]][2] = $signal;
                    foreach ($module[1] as $target) {
                        $next[] = [$target, $signal, $pulse[0]];
                        ++$sum[$signal];
                    }
                }
            } else if ($module[0] == '&') {
                $modules[$pulse[0]][2][$pulse[2]] = $pulse[1];
                if (array_sum($modules[$pulse[0]][2]) == count($modules[$pulse[0]][2])) {
                    foreach ($module[1] as $target) {
                        $next[] = [$target, 0, $pulse[0]];
                        ++$sum[0];
                    }
                } else {
                    foreach ($module[1] as $target) {
                        $next[] = [$target, 1, $pulse[0]];
                        ++$sum[1];
                    }
                }
            } else if ($module[0] == 'b') {
                foreach ($module[1] as $target) {
                    $next[] = [$target, 0, $pulse[0]];
                    ++$sum[0];
                }
            }
        }
        $pulses = $next;
    } while (!empty($pulses));
    return $sum;
}
$sum = [0, 0];
for ($i = 1;; ++$i) {
    $p = pushButton();
    if ($i <= 1000) {
        $sum[0] += $p[0];
        $sum[1] += $p[1];
    }
    foreach ($todo as $index => $name) {
        if (isset($p[2][$name])) {
            $cycles[$name] = $i;
            unset($todo[$index]);
        }
    }
    if (empty($todo) && $i > 1000) {
        break;
    }
}
$sum1 = $sum[0] * $sum[1];
$sum2 = 1;
foreach ($cycles as $cycle) {
    $sum2 = gmp_lcm($sum2, $cycle);
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
