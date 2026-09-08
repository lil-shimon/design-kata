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

    public function shift(int $shiftX, int $shiftY): self
    {
        $nextX = $this->x + $shiftX;
        $nextY = $this->y + $shiftY;

        return new self($nextX, $nextY);
    }
}
