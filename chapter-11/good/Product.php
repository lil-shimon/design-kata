<?php

class Product
{
    const MIN_PRICE = 1;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $price,
    ) {
        if ($this->price < self::MIN_PRICE) {
            throw new InvalidArgumentException();
        }
    }
}
