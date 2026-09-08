<?php
class GiftPoint
{
    const MIN = 0;
    const STANDARD_MEMBERSHIP_POINT = 3000;
    const PREMIUM_MEMBERSHIP_POINT = 10000;

    /**
     * @throws InvalidArgumentException ポイントが0未満の場合
     */
    private function __construct(private readonly int $point)
    {
        if ($point < self::MIN) {
            throw new InvalidArgumentException('Point must be greater than or equal to 0');
        }
    }

    public function value(): int
    {
        return $this->point;
    }

    public static function forStandardMembership(): self
    {
        return new self(self::STANDARD_MEMBERSHIP_POINT);
    }

    public static function forPremiumMembership(): self
    {
        return new self(self::PREMIUM_MEMBERSHIP_POINT);
    }
}
