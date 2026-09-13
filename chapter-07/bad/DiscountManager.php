<?php

class DiscountManager
{
    public function __construct(protected int $price)
    {
    }

    public function getDiscountedPrice(): int
    {
        // 通常割引は300円引き
        $discountedPrice = $this->price - 300;

        if ($discountedPrice < 0) {
            $discountedPrice = 0;
        }

        return $discountedPrice;
    }
}

class RegularDiscountManager extends DiscountManager
{
}

class SummerDiscountManager extends DiscountManager
{
    public function getDiscountedPrice(): int
    {
        // サマーキャンペーンは400円引き
        return $this->price - 400;
    }
}
