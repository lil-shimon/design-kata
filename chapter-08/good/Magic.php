<?php

enum MagicType
{
    case Fire;
    case Thunder;
    case Beam;
}

class Magic
{
    public function __construct(
        public readonly string $name,
        public readonly int $power,
        public readonly int $cost,
    ) {
    }

    public static function value(MagicType $magicType): self
    {
        switch ($magicType) {
            case MagicType::Fire:
                return new Magic(name: 'ファイア', power: 20, cost: 2);
            case MagicType::Thunder:
                return new Magic(name: '落雷', power: 50, cost: 10);
            case MagicType::Beam:
                return new Magic(name: 'ビーム', power: 40, cost: 8);
            default:
                throw new InvalidArgumentException('存在しない魔法種別です');
        }
    }
}
