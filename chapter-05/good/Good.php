<?php

class RegularPrice
{
    public function __construct(private readonly int $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('regularPriceは0以上にしてください。');
        }
    }

    public function value(): int
    {
        return $this->amount;
    }
}

class DiscountRate
{
    public function __construct(private readonly float $rate)
    {
        if ($rate < 0.0) {
            throw new InvalidArgumentException('discountRateは0.0以上にしてください。');
        }
    }

    public function value(): float
    {
        return $this->rate;
    }
}

class DiscountedPrice
{
    private readonly int $amount;

    public function __construct(RegularPrice $regularPrice, DiscountRate $discountRate)
    {
        $this->amount = (int)($regularPrice->value() * $discountRate->value());
    }

    public function value(): int
    {
        return $this->amount;
    }
}
