<?php

class MagicPoint
{
    public function __construct(private readonly int $currentMagicPoint, private readonly int $originalMagicPoint, private readonly array $maxMagicPointIncrements) 
    {}

    public function current(): int
    {
        return $this->currentMagicPoint;
    }

    public function max(): int
    {
        $amount = $this->originalMagicPoint;
        foreach ($this->maxMagicPointIncrements as $increment) {
            $amount += $increment;
        }
        return $amount;
    }

    public function recover(int $amount): void
    {
        $this->currentMagicPoint = min($this->max(), $this->currentMagicPoint + $amount);
    }

    public function consume(int $amount): void
    {
        $this->currentMagicPoint = max($this->currentMagicPoint - $amount, 0);
    }
}