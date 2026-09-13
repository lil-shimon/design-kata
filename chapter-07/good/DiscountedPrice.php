<?php

class DiscountedPrice
{
    private const MIN_PRICE = 0;

    public function __construct(private readonly int $price)
    {
        if ($price < self::MIN_PRICE) {
            throw new InvalidArgumentException("price must be positive number.");
        }
    }

    public function apply(int $discountAmount): int
    {
        return max(self::MIN_PRICE, $this->price - $discountAmount);
    }
}

class RegularDiscountManager
{
    private const DISCOUNT_AMOUNT = 300;

    public function __construct(private readonly DiscountedPrice $discountedPrice)
    {
    }

    public function getDiscountedPrice(): int
    {
        return $this->discountedPrice->apply(self::DISCOUNT_AMOUNT);
    }
}

class SummerDiscountManager
{
    private const DISCOUNT_AMOUNT = 400;

    public function __construct(private readonly DiscountedPrice $discountedPrice)
    {
    }

    public function getDiscountedPrice(): int
    {
        return $this->discountedPrice->apply(self::DISCOUNT_AMOUNT);
    }
}
