<?php
require_once __DIR__ . '/Money.php';

$a = new MoneyData();
$b = new MoneyData();
$manager = new MoneyManager();

var_dump($manager->addMoney($a, $b));
