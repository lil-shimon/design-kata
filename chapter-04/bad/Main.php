<?php

class AttackPower {
    const int MIN = 0;
    public int $value;

    public function __construct(int $value)
    {
        if ($value < self::MIN) {
            throw new InvalidArgumentException('attackPowerは'.self::MIN.'以上にしてください。');
        }

        $this->value = $value;
    }

    public function enhance(int $increment): void {
        $this->value += $increment;
    }
}

$attack = new AttackPower(50);
$enemyA = $attack;
$enemyB = $attack;
$enemyA->enhance(50);
echo $enemyB->value;
