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
