<?php

class GiftPoint
{
    const MIN = 0;

    public function __construct(private readonly int $point)
    {
        if ($point < self::MIN) {
            throw new InvalidArgumentException('Point must be greater than 0');
        }
    }

    public function value(): int
    {
        return $this->point;
    }

    public function add(GiftPoint $other): self
    {
        return new self($this->point + $other->point);
    }

    public function isEnough(ConsumptionPoint $consumptionPoint): bool
    {
        return $this->point >= $consumptionPoint->value();
    }

    public function consume(ConsumptionPoint $consumptionPoint): self
    {
        return new self($this->point - $consumptionPoint->value());
    }
}

class ConsumptionPoint
{
    const MIN = 0;

    public function __construct(private readonly int $point)
    {
        if ($point < self::MIN) {
            throw new InvalidArgumentException('Point must be greater than 0');
        }
    }

    public function value(): int
    {
        return $this->point;
    }
}