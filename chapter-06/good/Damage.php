<?php

class SpecialGauge
{
    private const MAX_SPECIAL_GAUGE = 100;

    public function __construct(public readonly int $value)
    {
        if ($value > self::MAX_SPECIAL_GAUGE) {
            throw new InvalidArgumentException("special gauge must be less than or equal to " . self::MAX_SPECIAL_GAUGE . ".");
        }
    }

    public function update(int $damageAmount): self
    {
        $newSpecialGauge = $this->value + 5 + (int)($damageAmount / 100);
        return new self(min(self::MAX_SPECIAL_GAUGE, $newSpecialGauge));
    }

    public function isSpecialMode(): bool
    {
        if ($this->value === self::MAX_SPECIAL_GAUGE) {
            return true;
        }

        return false;
    }
}

class Member
{
    public function __construct(public readonly int $armStrength)
    {
    }
}

class AttackPower
{
    public readonly int $power;

    public function __construct(Member $member, Weapon $weapon)
    {
        $this->power = $member->armStrength + $weapon->attackPower;
    }
}

class Damage
{
    public readonly int $amount;

    public function __construct(AttackPower $attackPower, SpecialGauge $specialGauge, Enemy $enemy)
    {
        $power = $attackPower->power;

        if ($specialGauge->isSpecialMode()) {
            $power = $power * 2;
        }

        $this->amount = max(0, $power - (int)($enemy->defense / 2));
    }
}

class Weapon
{
    private const MIN_DURABILITY = 0;
    private const DURABILITY_DECREASE_THRESHOLD = 10;

    public function __construct(
        public readonly int $durability,
        public readonly int $attackPower,
    ) {
        if ($durability < self::MIN_DURABILITY) {
            throw new InvalidArgumentException("durability must be positive number.");
        }
    }

    public function canUse(): bool
    {
        return $this->durability > self::MIN_DURABILITY;
    }

    public function use(Damage $damage, SpecialGauge $specialGauge): self
    {
        if (!$this->canUse()) {
            throw new RuntimeException("weapon is broken.");
        }

        if ($damage->amount >= self::DURABILITY_DECREASE_THRESHOLD) {
            return $this;
        }

        if ($specialGauge->isSpecialMode()) {
            return $this;
        }

        return new self($this->durability - 1, $this->attackPower);
    }
}

class Enemy
{
    public function __construct(public readonly int $defense)
    {
    }
}
