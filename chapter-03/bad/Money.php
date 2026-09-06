<?php

class Money_A {
    public int $amount;
    public Currency $currency;
}

class Money_B {
    public function addMoney(Money_A $moneyA, Money_A $moneyB): int {
        return $moneyA->amount + $moneyB->amount;
    }
}
