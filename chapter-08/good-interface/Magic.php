<?php

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
