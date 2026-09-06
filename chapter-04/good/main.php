<?php

require_once __DIR__ . '/AttackPower.php';

// 1. 生成できた時点で必ず有効な状態になっている
echo '=== 生成時 ===', PHP_EOL;
var_dump(new AttackPower(50));

// 2. 不正な値は生成・強化の入口で弾かれる
echo PHP_EOL, '=== 不正値は入口で弾かれる ===', PHP_EOL;

try {
    new AttackPower(-1);
} catch (InvalidArgumentException $e) {
    printf("new AttackPower(-1) : %s%s", $e->getMessage(), PHP_EOL);
}

$attack = new AttackPower(50);

foreach ([0, -10] as $increment) {
    try {
        $attack->enhance($increment);
    } catch (InvalidArgumentException $e) {
        printf("enhance(%4d)       : %s%s", $increment, $e->getMessage(), PHP_EOL);
    }
}

// 3. 強化しても元インスタンスは変わらない (bad との差分はここ)
echo PHP_EOL, '=== 強化しても元が変わらない ===', PHP_EOL;

// bad と同じく、同じインスタンスを2箇所で共有する
$enemyA = $attack;
$enemyB = $attack;

printf("同一インスタンスか : %s%s", $enemyA === $enemyB ? 'yes' : 'no', PHP_EOL);
printf("初期状態           : A = %3d / B = %3d%s", $enemyA->value(), $enemyB->value(), PHP_EOL);

$enhanced = $enemyA->enhance(50);

printf("A を +50 強化      : A = %3d / B = %3d  <- どちらも変わらない%s", $enemyA->value(), $enemyB->value(), PHP_EOL);
printf("enhance() の戻り値 : %3d              <- 別インスタンスとして返る%s", $enhanced->value(), PHP_EOL);
printf("戻り値は別物か     : %s%s", $enhanced !== $enemyA ? 'yes' : 'no', PHP_EOL);
