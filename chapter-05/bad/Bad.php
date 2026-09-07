<?php

class Common
{
    public static function discountedPrice(int $regularPrice, float $discountRate): int
    {
        if ($regularPrice < 0) {
            throw new InvalidArgumentException('regularPriceは0以上にしてください。');
        }

        if ($discountRate < 0.0) {
            throw new InvalidArgumentException('discountRateは0.0以上にしてください。');
        }

        return (int)($regularPrice * $discountRate);
    }
}

class Util
{
    public static function isFairPrice(int $regularPrice): bool
    {
        if ($regularPrice < 0) {
            throw new InvalidArgumentException('regularPriceは0以上にしてください。');
        }

        return true;
    }
}
