<?php

enum MagicType
{
    case Fire;
    case Thunder;
    case Beam;
}

class MagicName
{
    public function value(MagicType $magicType): string
    {
        switch ($magicType) {
            case MagicType::Fire:
                return 'ファイア';
            case MagicType::Thunder:
                return '落雷';
            case MagicType::Beam:
                return 'ビーム';
            default:
                return '';
        }
    }
}

class MagicAttackPower
{
    public function value(MagicType $magicType): int
    {
        switch ($magicType) {
            case MagicType::Fire:
                return 20;
            case MagicType::Thunder:
                return 50;
            case MagicType::Beam:
                return 40;
            default:
                return 0;
        }
    }
}

class MagicCostMp
{
    public function value(MagicType $magicType): int
    {
        switch ($magicType) {
            case MagicType::Fire:
                return 2;
            case MagicType::Thunder:
                return 10;
            case MagicType::Beam:
                return 8;
            default:
                return 0;
        }
    }
}

class Magic
{
    public function __construct(private readonly MagicType $magicType)
    {
    }

    public function name(): string
    {
        $magicName = new MagicName();

        return $magicName->value($this->magicType);
    }

    public function attackPower(): int
    {
        $magicAttackPower = new MagicAttackPower();

        return $magicAttackPower->value($this->magicType);
    }

    public function costMagicPoint(): int
    {
        $magicCostMp = new MagicCostMp();

        return $magicCostMp->value($this->magicType);
    }
}
