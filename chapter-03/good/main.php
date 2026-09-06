<?php

require_once __DIR__ . '/Money.php';

$a = new Money(100, Currency::JPY);
$b = new Money(250, Currency::JPY);
var_dump($a->add($b));

try {
    $a->add(new Money(5, Currency::USD));
} catch (InvalidArgumentException $e) {
    echo 'caught: ', $e->getMessage(), PHP_EOL;
}
