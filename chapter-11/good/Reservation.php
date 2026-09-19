<?php

class Reservation
{
    const DEPOSIT_RATE = 0.1;
    const MIN_QUANTITY = 1;

    public readonly int $productId;
    public readonly int $deposit;
    public readonly int $productPrice;

    public function __construct(
        public readonly int $userId,
        Product $product,
        public readonly DateTimeImmutable $reservedAt,
        public readonly DateTimeImmutable $releaseAt,
        public readonly int $quantity
    )
    {
        if ($quantity < self::MIN_QUANTITY) {
            throw new InvalidArgumentException();
        }

        $this->productId = $product->id;
        $this->productPrice = $product->price;

        $this->deposit = (int)($this->subtotal() * self::DEPOSIT_RATE);
    }

    public function subtotal(): int
    {
        return $this->productPrice * $this->quantity;
    }

    public function remainingPayment(): int
    {
        return $this->subtotal() - $this->deposit;
    }
}
