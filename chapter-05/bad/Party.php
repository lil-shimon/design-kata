<?php

class Armor
{
    public function __construct(public string $name)
    {
    }
}

class Equipment
{
    public bool $canChange = true;
    public ?Armor $armor = null;
}

class Member
{
    public Equipment $equipment;

    public function __construct()
    {
        $this->equipment = new Equipment();
    }
}

class Party
{
    /** @param Member[] $members */
    public function __construct(public array $members = [])
    {
    }

    public function equipArmor(int $memberId, Armor $newArmor): void
    {
        if ($this->members[$memberId]->equipment->canChange) {
            $this->members[$memberId]->equipment->armor = $newArmor;
        }
    }
}
