<?php

class MagicPoint
{
    const MIN = 0;

    /**
     * @param list<int> $maxMagicPointIncrements 最大MPの増加量(装備やレベルアップによる)
     * @throws InvalidArgumentException MPが0未満の場合
     */
    public function __construct(
        private readonly int $currentMagicPoint,
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

    /**
     * @throws InvalidArgumentException 回復量が0未満の場合
     */
    public function recover(int $amount): self
    {
        $this->assertAmount($amount);

        return new self(
            min($this->max(), $this->currentMagicPoint + $amount),
            $this->originalMagicPoint,
            $this->maxMagicPointIncrements,
        );
    }

    /**
     * @throws InvalidArgumentException 消費量が0未満の場合
     */
    public function consume(int $amount): self
    {
        $this->assertAmount($amount);

        return new self(
            max($this->currentMagicPoint - $amount, self::MIN),
            $this->originalMagicPoint,
            $this->maxMagicPointIncrements,
        );
    }

    private function assertAmount(int $amount): void
    {
        if ($amount < self::MIN) {
            throw new InvalidArgumentException('MPの増減量は0以上にしてください。');
        }
    }
}
