<?php

class Armor
{
    public function __construct(
        public string $name,
        public int $defense,
    ) {
    }
}

class Equipments
{
    public bool $canChange = true;
    public ?Armor $armor = null;
}

class Member
{
    public Equipments $equipments;

    public function __construct()
    {
        $this->equipments = new Equipments();
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
        if ($this->members[$memberId]->equipments->canChange) {
            $this->members[$memberId]->equipments->armor = $newArmor;
        }
    }
}
