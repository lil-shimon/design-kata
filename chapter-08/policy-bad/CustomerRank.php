<?php

class PurchaseHistory
{
    public function __construct(
        public readonly int $annualAmount,
        public readonly int $purchasedMonthCount,
        public readonly int $minMonthlyAmount,
    ) {
    }
}

class CustomerRank
{
    public function rankOf(PurchaseHistory $history): string
    {
        if ($history->annualAmount >= 100000
            && $history->purchasedMonthCount === 12
            && $history->minMonthlyAmount >= 5000) {
            return 'ゴールド';
        }

        if ($history->annualAmount >= 50000
            && $history->purchasedMonthCount === 12) {
            return 'シルバー';
        }

        return '通常';
    }
}
