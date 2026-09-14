<?php

enum MagicType
{
    case Fire;
    case Thunder;
    case Beam;
}

interface Magic
{
    public function name(): string;
    public function power(): int;
    public function cost(): int;
}

class Fire implements Magic
{
    public function name(): string
    {
        return 'ファイア';
    }

    public function power(): int
    {
        return 20;
    }

    public function cost(): int
    {
        return 2;
    }
}

class Thunder implements Magic
{
    public function name(): string
    {
        return '落雷';
    }

    public function power(): int
    {
        return 50;
    }

    public function cost(): int
    {
        return 10;
    }
}

class Beam implements Magic
{
    public function name(): string
    {
        return 'ビーム';
    }

    public function power(): int
    {
        return 40;
    }

    public function cost(): int
    {
        return 8;
    }
}

class MagicFactory
{
    private readonly array $magics;

    public function __construct()
    {
        $this->magics = [
            MagicType::Fire->name    => new Fire(),
            MagicType::Thunder->name => new Thunder(),
            MagicType::Beam->name    => new Beam(),
        ];
    }

    public function of(MagicType $magicType): Magic
    {
        return $this->magics[$magicType->name];
    }
}

class MagicAttack
{
    public function __construct(private readonly MagicFactory $magicFactory)
    {
    }

    public function attack(MagicType $magicType): void
    {
        $magic = $this->magicFactory->of($magicType);

        $this->showName($magic);
        $this->showPower($magic);
        $this->showCost($magic);
    }

    private function showName(Magic $magic): void
    {
        // $magic->name() を画面に表示する
    }

    private function showPower(Magic $magic): void
    {
        // $magic->power() を画面に表示する
    }

    private function showCost(Magic $magic): void
    {
        // $magic->cost() を画面に表示する
    }
}
