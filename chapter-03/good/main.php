<?php
require_once __DIR__ . '/Money.php';

$a = new Money(100, 'JPY');
$b = new Money(250, 'JPY');
var_dump($a->add($b));

try {
    $a->add(new Money(5, "USD"));
} catch (InvalidArgumentException $e) {
    echo 'caught: ', $e->getMessage(), PHP_EOL;
}
