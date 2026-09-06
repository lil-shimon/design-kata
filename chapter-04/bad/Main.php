<?php
require_once __DIR__ . '/AttackPower.php';

$attack = new AttackPower(50);

// 同じインスタンスを2箇所で共有する
$enemyA = $attack;
$enemyB = $attack;

printf("同一インスタンスか : %s%s", $enemyA === $enemyB ? 'yes' : 'no', PHP_EOL);
printf("初期状態           : A = %3d / B = %3d%s", $enemyA->value, $enemyB->value, PHP_EOL);

// A だけを強化する
$enemyA->enhance(50);

printf("A を +50 強化      : A = %3d / B = %3d  <- B には何もしていない%s", $enemyA->value, $enemyB->value, PHP_EOL);
