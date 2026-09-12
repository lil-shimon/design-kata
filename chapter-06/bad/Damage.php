<?php

class Member
{
    public function __construct(
        public string $name,
        public int $armStrength,
    ) {
    }
}

class Weapon
{
    public function __construct(
        public string $name,
        public int $attackPower,
        public int $durability,
    ) {
    }
}

class Enemy
{
    public function __construct(
        public string $name,
        public int $defense,
    ) {
    }
}

class Damage
{
    public int $specialGauge = 0;

    public function calculateDamage(Member $member, Weapon $weapon, Enemy $enemy): int
    {
        $damageAmount = 0;

        if ($weapon->durability > 0) {
            if ($this->specialGauge >= 100) {
                // ゲージ満タンなので攻撃力2倍
                $damageAmount = ($member->armStrength + $weapon->attackPower) * 2 - (int)($enemy->defense / 2);
                if ($damageAmount < 0) {
                    $damageAmount = 0;
                }
                // ゲージ満タン時は耐久値が減らない
            } else {
                $damageAmount = ($member->armStrength + $weapon->attackPower) - (int)($enemy->defense / 2);
                if ($damageAmount < 0) {
                    $damageAmount = 0;
                }
                if ($damageAmount < 10) {
                    $weapon->durability = $weapon->durability - 1;
                    if ($weapon->durability < 0) {
                        $weapon->durability = 0;
                    }
                }
            }

            $this->specialGauge = $this->specialGauge + 5 + (int)($damageAmount / 100);
            if ($this->specialGauge > 100) {
                $this->specialGauge = 100;
            }
        }

        return $damageAmount;
    }
}
