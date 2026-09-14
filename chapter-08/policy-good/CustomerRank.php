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

interface CustomerRule
{
    public function ok(PurchaseHistory $history): bool;
}

class AnnualAmountRule implements CustomerRule
{
    public function __construct(private readonly int $threshold)
    {
    }

    public function ok(PurchaseHistory $history): bool
    {
        return $history->annualAmount >= $this->threshold;
    }
}

class EveryMonthPurchaseRule implements CustomerRule
{
    private const MONTHS_PER_YEAR = 12;

    public function ok(PurchaseHistory $history): bool
    {
        return $history->purchasedMonthCount === self::MONTHS_PER_YEAR;
    }
}

class MonthlyAmountRule implements CustomerRule
{
    public function __construct(private readonly int $threshold)
    {
    }

    public function ok(PurchaseHistory $history): bool
    {
        return $history->minMonthlyAmount >= $this->threshold;
    }
}

class CustomerPolicy
{
    private array $rules = [];

    public function add(CustomerRule $rule): void
    {
        $this->rules[] = $rule;
    }

    public function complyWithAll(PurchaseHistory $history): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->ok($history)) {
                return false;
            }
        }

        return true;
    }
}

class CustomerRank
{
    public function __construct(
        private readonly CustomerPolicy $goldPolicy,
        private readonly CustomerPolicy $silverPolicy,
    ) {
    }

    public function rankOf(PurchaseHistory $history): string
    {
        if ($this->goldPolicy->complyWithAll($history)) {
            return 'ゴールド';
        }

        if ($this->silverPolicy->complyWithAll($history)) {
            return 'シルバー';
        }

        return '通常';
    }
}
