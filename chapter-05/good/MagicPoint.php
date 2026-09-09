<?php

class MagicPoint
{
    /**
     * @param list<int> $maxMagicPointIncrements 最大MPの増加量(装備やレベルアップによる)
     */
    public function __construct(
        private readonly int $currentMagicPoint,
        private readonly int $originalMagicPoint,
        private readonly array $maxMagicPointIncrements,
    ) {}

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

    public function recover(int $amount): self
    {
        return new self(
            min($this->max(), $this->currentMagicPoint + $amount),
            $this->originalMagicPoint,
            $this->maxMagicPointIncrements,
        );
    }

    public function consume(int $amount): self
    {
        return new self(
            max($this->currentMagicPoint - $amount, 0),
            $this->originalMagicPoint,
            $this->maxMagicPointIncrements,
        );
    }
}
