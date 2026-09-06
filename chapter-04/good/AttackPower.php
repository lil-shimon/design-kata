<?php

final class AttackPower
{
    const int MIN = 0;

    public function __construct(private readonly int $value)
    {
        if ($value < MIN) {
            throw new InvalidArgumentException('attackPowerは'.self::MIN.'以上にしてください。');
        }
    }
}
