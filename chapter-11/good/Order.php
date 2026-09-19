<?php

enum PaymentMethod
{
    case Card;
    case Cash;
}

class Order
{
    const MIN_ITEM_COUNT = 1;
    const SHIPPING_FEE = 500;
    const FREE_SHIPPING_THRESHOLD = 5000;

    /**
     * @param OrderItem[] $items
     */
    public function __construct(
        public readonly int $userId,
        public readonly PaymentMethod $paymentMethod,
        public readonly DateTimeImmutable $orderedDate,
        public readonly string $shippingAddress,
        public readonly array $items
    )
    {
        if (count($items) < self::MIN_ITEM_COUNT) {
            throw new InvalidArgumentException();
        }

        foreach ($items as $item) {
            if (!$item instanceof OrderItem) {
                throw new InvalidArgumentException();
            }
        }
    }

    public function basePrice(): int
    {
        $basePrice = 0;
        foreach ($this->items as $item) {
            $basePrice += $item->subbase();
        }

        return $basePrice;
    }

    public function priceWithShippingFee(): int
    {
        $basePrice = $this->basePrice();

        if ($basePrice < self::FREE_SHIPPING_THRESHOLD) {
            return $basePrice + self::SHIPPING_FEE;
        }

        return $basePrice;
    }
}
