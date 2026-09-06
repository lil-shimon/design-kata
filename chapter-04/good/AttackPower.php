<?php

final class AttackPower
{
    const int MIN = 0;
    const int MIN_INCREMENT = 1;

    public function __construct(private readonly int $value)
    {
        if ($value < self::MIN) {
            throw new InvalidArgumentException('attackPowerは'.self::MIN.'以上にしてください。');
        }
    }

    public function enhance(int $increment): self
    {
        if ($increment < self::MIN_INCREMENT) {
            throw new InvalidArgumentException('incrementは'.self::MIN_INCREMENT.'以上にしてください。');
        }

        return new self($this->value + $increment);
    }
}
