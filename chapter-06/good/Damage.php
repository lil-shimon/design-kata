<?php

class SpecialGauge
{
    public function __construct(public readonly int $value)
    {
    }

    public function update(int $damageAmount): self
    {
        if ($this->value === 100) {
            return new self($this->value);
        }

        $newSpecialGauge = $this->value + 5 + (int)($damageAmount / 100);
        return new self($newSpecialGauge);
    }

    public function isSpecialMode(): bool
    {
        if($this->value >= 100) {
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

class Damage
{
    public readonly int $amount;

    public function __construct(Member $member, Weapon $weapon, SpecialGauge $specialGauge, Enemy $enemy)
    {
        $attackPower = $member->armStrength + $weapon->attackPower;

        if ($specialGauge->isSpecialMode()) {
            $attackPower = $attackPower * 2;
        }

        $this->amount = max(0, $attackPower - (int)($enemy->defense / 2));
    }
}

class Weapon
{
    public function __construct(
        public readonly int $durability,
        public readonly int $attackPower,
    ) {
        if ($durability < 0) {
            throw new InvalidArgumentException("durability must be positive number.");
        }
    }

    public function use(Damage $damage, Weapon $weapon, SpecialGauge $specialGauge): self
    {
        if ($damage->amount >= 10) {
            return $weapon;
        }

        if ($specialGauge->isSpecialMode()) {
            return $weapon;
        }

        return new self($weapon->durability - 1, $weapon->attackPower);
    }
}

class Enemy
{
    public function __construct(public readonly int $defense)
    {
    }
}
