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
        return match ($magicType) {
            MagicType::Fire    => new Magic(name: 'ファイア', power: 20, cost: 2),
            MagicType::Thunder => new Magic(name: '落雷',     power: 50, cost: 10),
            MagicType::Beam    => new Magic(name: 'ビーム',   power: 40, cost: 8),
        };
    }
}
