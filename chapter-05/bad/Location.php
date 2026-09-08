<?php
class Location
{
    private readonly int $x;
    private readonly int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}

class ActorManager
{
    public function shift(Location $location, int $shiftX, int $shiftY): void
    {
        $location->x += $shiftX;
        $location->y += $shiftY;
    }
}

class SpecialAttackManager 
{
    public function shift(Location $location, int $shiftX, int $shiftY): void
    {
        $location->x += $shiftX * 2;
        $location->y += $shiftY * 2;
    }
}