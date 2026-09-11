<?php

class Armor
{
  public function __construct(private readonly string $name)
  {
  }

  public function name(): string
  {
    return $this->name;
  }
}

class Equipment
{
  private bool $canChange = true;

  public function __construct(private ?Armor $armor = null)
  {
  }

  public function armor(): ?Armor
  {
    return $this->armor;
  }

  public function equipArmor(Armor $armor): void
  {
    if ($this->canChange) {
      $this->armor = $armor;
    }
  }
}

class Member
{
  private Equipment $equipment;

  public function __construct()
  {
    $this->equipment = new Equipment();
  }

  public function equipArmor(Armor $armor): void
  {
    $this->equipment->equipArmor($armor);
  }
}

class Party
{
  /** @param Member[] $members */
  public function __construct(private array $members = [])
  {
  }

  public function equipArmor(int $memberId, Armor $armor): void
  {
    $this->members[$memberId]->equipArmor($armor);
  }
}
