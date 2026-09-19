<?php

class OrderItem
{
    const MIN_QUANTITY = 1;

    public readonly int $price;
    public readonly int $productId;

    public function __construct(
        private readonly int $quantity,
        Product $product,
    ) {
        if ($quantity < self::MIN_QUANTITY) {
            throw new InvalidArgumentException();
        }

        $this->productId = $product->id;
        $this->price = $product->price;
    }

    public function subtotal(): int
    {
        return $this->quantity * $this->price;
    }
}
