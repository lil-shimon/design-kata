<?php

enum StateType
{
    case Poison;
    case Dead;
}

class Member
{
    public function __construct(
        public readonly string $name,
        public int $hitPoint,
        public readonly int $attack,
        public readonly bool $teamAttackSucceeded,
        public array $states = []
    ) {
    }

    public function hasState(StateType $stateType): bool
    {
        return in_array($stateType, $this->states);
    }

    public function addState(StateType $stateType): bool
    {
        return $this->states = $stateType;
    }

    public function removeState(StateType $stateType): bool
    {
        return $this->states = array_values(array_filter($this->states, fn (StateType $state) => $state !== $stateType));
    }
}

class Party
{
    /**
     * @param Member[] $members
     */
    public function __construct(public readonly array $members)
    {
    }

    public function add(Member $member): self
    {
        $adding = [...$this->members, $member];
        return new self($adding);
    }

    public function containsPoisonedMembers(): bool
    {
        return array_any($this->members, fn (Member $member) => $member->hasState(StateType::Poison));
    }
}

class Battle
{
    // TODO
}
