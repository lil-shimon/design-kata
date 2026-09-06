<?php

enum Currency: string
{
    case JPY = 'JPY';
    case USD = 'USD';
}

class MoneyData
{
    public int $amount;
    public Currency $currency;
}

class MoneyManager
{
    public function addMoney(MoneyData $moneyA, MoneyData $moneyB): int
    {
        return $moneyA->amount + $moneyB->amount;
    }

    public function multiply(MoneyData $moneyA, MoneyData $moneyB): int
    {
        return $moneyA->amount * $moneyB->amount;
    }
}
