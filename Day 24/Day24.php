<?php
$start = microtime(true);
const LOWER = 200000000000000;
const UPPER = 400000000000000;
function cross(array $p1, array $p2): GMP
{
    return gmp_sub(gmp_mul($p1[0], $p2[1]), gmp_mul($p1[1], $p2[0]));
}
function dot(array $p1, array $p2): GMP
{
    return gmp_add(gmp_add(gmp_mul($p1[0], $p2[0]),  gmp_mul($p1[1], $p2[1])), gmp_mul($p1[2], $p2[2]));
}
function cross3(array $p1, array $p2): array
{
    return [
        gmp_sub(gmp_mul($p1[1], $p2[2]), gmp_mul($p1[2], $p2[1])),
        gmp_sub(gmp_mul($p1[2], $p2[0]), gmp_mul($p1[0], $p2[2])),
        gmp_sub(gmp_mul($p1[0], $p2[1]), gmp_mul($p1[1], $p2[0]))
    ];
}
function norm(array $p): GMP
{
    return gmp_sqrt(dot($p, $p));
}
function sub(array $p1, array $p2): array
{
    return [$p1[0] - $p2[0], $p1[1] - $p2[1], $p1[2] - $p2[2]];
}
function linearCombination(GMP $a1, array $p1, GMP $a2, array $p2, GMP $a3, array $p3): array
{
    return [
        gmp_add(gmp_add(gmp_mul($a1, $p1[0]), gmp_mul($a2, $p2[0])), gmp_mul($a3, $p3[0])),
        gmp_add(gmp_add(gmp_mul($a1, $p1[1]), gmp_mul($a2, $p2[1])), gmp_mul($a3, $p3[1])),
        gmp_add(gmp_add(gmp_mul($a1, $p1[2]), gmp_mul($a2, $p2[2])), gmp_mul($a3, $p3[2]))
    ];
}
function find_plane(array $p1, array $v1, array $p2, array $v2): array
{
    $p12 = sub($p1, $p2);
    $v12 = sub($v1, $v2);
    $vv = cross3($v1, $v2);
    return [cross3($p12, $v12), dot($p12, $vv)];
}
$sum1 = $sum2 = 0;
$hail = [];
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    $line = trim($line);
    $line = explode(' @ ', $line);
    $hail[] = [array_map('gmp_init', explode(', ', $line[0])), array_map('gmp_init', explode(', ', $line[1]))];
}
for ($i = 0; $i < count($hail); ++$i) {
    $p1 = $hail[$i];
    for ($j = $i + 1; $j < count($hail); ++$j) {
        $p2 = $hail[$j];
        $d = cross($p1[1], $p2[1]);
        if (gmp_strval($d) != '0') {
            $u = gmp_strval(gmp_div(cross([gmp_sub($p2[0][0], $p1[0][0]), gmp_sub($p2[0][1], $p1[0][1])], $p1[1]), $d));
            $t = gmp_strval(gmp_div(cross([gmp_sub($p2[0][0], $p1[0][0]), gmp_sub($p2[0][1], $p1[0][1])], $p2[1]), $d));
            $intersect = [gmp_strval(gmp_add($p2[0][0], gmp_mul($u, $p2[1][0]))), gmp_strval(gmp_add($p2[0][1], gmp_mul($u, $p2[1][1])))];
            if ($t > 0 && $u > 0 && $intersect[0] >= LOWER && $intersect[0] <= UPPER && $intersect[1] >= LOWER && $intersect[1] <= UPPER) {
                ++$sum1;
            }
        }
    }
}
[$p1, $v1] = $hail[0];
[$p2, $v2] = $hail[1];
[$p3, $v3] = $hail[2];
[$a, $A] = find_plane($p1, $v1, $p2, $v2);
[$b, $B] = find_plane($p1, $v1, $p3, $v3);
[$c, $C] = find_plane($p2, $v2, $p3, $v3);
$w = linearCombination($A, cross3($b, $c), $B, cross3($c, $a), $C, cross3($a, $b));
$t = dot($a, cross3($b, $c));
$w = [gmp_div($w[0], $t), gmp_div($w[1], $t), gmp_div($w[2], $t)];
$w1 = sub($v1, $w);
$w2 = sub($v2, $w);
$ww = cross3($w1, $w2);

$E = dot($ww, cross3($p2, $w2));
$F = dot($ww, cross3($p1, $w1));
$G = dot($p1, $ww);
$S = dot($ww, $ww);

$sum2 = gmp_strval(gmp_div(array_reduce(linearCombination($E, $w1, gmp_neg($F), $w2, $G, $ww), fn($i,$c) => gmp_add($i, $c), 0), $S));
$time = (microtime(true) - $start) * 1000;
echo $sum1 . ' - ' . $sum2 . PHP_EOL;
echo $time . 'ms' . PHP_EOL;
