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
        public readonly StateType $stateType
    ) {
    }

    public function hasState(StateType $stateType): bool
    {
        return in_array($stateType, $this->stateType);
    }

    public function addState(StateType $stateType): bool
    {
        return $this->stateType[] = $stateType;
    }

    public function removeState(StateType $stateType): bool
    {
        return $this->stateType = array_values(array_filter($this->stateType, fn (StateType $state) => $state !== $stateType));
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
