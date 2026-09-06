<?php

final class AttackPower
{
    const int MIN = 0;

    public function __construct(private readonly int $value)
    {
        if ($value < self::MIN) {
            throw new InvalidArgumentException('attackPowerは'.self::MIN.'以上にしてください。');
        }
    }

    public function enhance(int $increment): self
    {
        if ($increment < 1) {
            throw new InvalidArgumentException('incrementは1以上にしてください。');
        }
        return new self($this->value + $increment);
    }
}
