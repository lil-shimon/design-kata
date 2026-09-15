<?php

enum StateType
{
    case Poison;
    case Dead;
}

class Member
{
    /** @var StateType[] */
    public array $states = [];

    public function __construct(
        public readonly string $name,
        public int $hitPoint,
        public readonly int $attack,
        public readonly bool $teamAttackSucceeded,
    ) {
    }

    public function hasState(StateType $stateType): bool
    {
        return in_array($stateType, $this->states, true);
    }

    public function addState(StateType $stateType): void
    {
        $this->states[] = $stateType;
    }

    public function removeState(StateType $stateType): void
    {
        $this->states = array_values(
            array_filter($this->states, fn (StateType $state) => $state !== $stateType)
        );
    }
}

class Battle
{
    /**
     * 毒状態のメンバーがいるか
     *
     * @param Member[] $members
     */
    public function containsPoisonedMember(array $members): bool
    {
        $isPoisoned = false;

        foreach ($members as $member) {
            if ($member->hasState(StateType::Poison)) {
                $isPoisoned = true;
                break;
            }
        }

        return $isPoisoned;
    }

    /**
     * 毒ダメージを与える
     *
     * @param Member[] $members
     */
    public function applyPoisonDamage(array $members): void
    {
        foreach ($members as $member) {
            if (0 < $member->hitPoint) {
                if ($member->hasState(StateType::Poison)) {
                    $member->hitPoint -= 10;

                    if ($member->hitPoint <= 0) {
                        $member->hitPoint = 0;
                        $member->addState(StateType::Dead);
                        $member->removeState(StateType::Poison);
                    }
                }
            }
        }
    }

    /**
     * 連携攻撃の合計ダメージを算出する
     *
     * @param Member[] $members
     */
    public function totalTeamAttackDamage(array $members): int
    {
        $totalDamage = 0;

        foreach ($members as $member) {
            if ($member->teamAttackSucceeded) {
                $damage = (int) ($member->attack * 1.1);

                if (20 < $damage) {
                    $totalDamage += $damage;

                    if (200 <= $totalDamage) {
                        break;
                    }
                }
            }
        }

        return $totalDamage;
    }
}
