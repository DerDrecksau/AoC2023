<?php
$start = microtime(true);
$values1 = array_flip(array_reverse(["A", "K", "Q", "J", "T", "9", "8", "7", "6", "5", "4", "3", "2"]));
$values2 = array_flip(array_reverse(["A", "K", "Q", "T", "9", "8", "7", "6", "5", "4", "3", "2", "J"]));
$hands = [];
$sum1 = $sum2 = 0;
$file = fopen("input.txt", "r");
while (($line = fgets($file)) != null) {
    $hand = substr($line, 0, 5);
    $bid = intval(substr($line, 6));
    $counts1 = $counts2 = [];
    $score1 = $score2 = $jacks = 0;
    for ($card = 0; $card < 5; ++$card) {
        $score1 *= 13;
        $score2 *= 13;
        $counts1[$hand[$card]] = ($counts1[$hand[$card]] ?? 0) + 1;
        if ($hand[$card] != 'J') {
            $counts2[$hand[$card]] = ($counts2[$hand[$card]] ?? 0) + 1;
        } else {
            ++$jacks;
        }
        $score1 += $values1[$hand[$card]];
        $score2 += $values2[$hand[$card]];
    }
    $counts1 = array_filter(array_values($counts1), fn ($v) => $v != 1);
    $score1 += 13 ** 5 * match (implode('', $counts1)) {
        default => 0,
        '2' => 1,
        '22' => 2,
        '3' => 3,
        '23', '32' => 4,
        '4' => 5,
        '5' => 6
    };
    $counts2 = array_values($counts2);
    rsort($counts2);
    $counts2[0] = ($counts2[0] ?? 0) + $jacks;
    $counts2 = array_filter($counts2, fn ($v) => $v != 1);
    $score2 += 13 ** 5 * match (implode('', $counts2)) {
        default => 0,
        '2' => 1,
        '22' => 2,
        '3' => 3,
        '32' => 4,
        '4' => 5,
        '5' => 6
    };
    $hands[] = [$score1, $score2, $bid];
}
usort($hands, fn ($v1, $v2) => $v1[0] <=> $v2[0]);
foreach ($hands as $rank => $hand) {
    $sum1 += ($rank + 1) * $hand[2];
}
usort($hands, fn ($v1, $v2) => $v1[1] <=> $v2[1]);
foreach ($hands as $rank => $hand) {
    $sum2 += ($rank + 1) * $hand[2];
}
$time = (microtime(true) - $start) * 1000;
echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;
echo  $time . 'ms' . PHP_EOL;
