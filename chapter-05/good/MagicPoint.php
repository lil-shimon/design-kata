<?php

class MagicPoint
{
    const MIN = 0;

    /**
     * @param list<int> $maxMagicPointIncrements
     */
    public function __construct(
        private int $currentMagicPoint,
        private readonly int $originalMagicPoint,
        private readonly array $maxMagicPointIncrements,
    ) {
        if ($currentMagicPoint < self::MIN) {
            throw new InvalidArgumentException('現在MPは0以上にしてください。');
        }
        if ($originalMagicPoint < self::MIN) {
            throw new InvalidArgumentException('元の最大MPは0以上にしてください。');
        }
    }

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
        $this->assertAmount($amount);

        $this->currentMagicPoint = min($this->max(), $this->currentMagicPoint + $amount);
    }

    public function consume(int $amount): void
    {
        $this->assertAmount($amount);

        $this->currentMagicPoint = max($this->currentMagicPoint - $amount, self::MIN);
    }

    private function assertAmount(int $amount): void
    {
        if ($amount < self::MIN) {
            throw new InvalidArgumentException('MPの増減量は0以上にしてください。');
        }
    }
}
