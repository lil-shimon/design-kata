<?php

class Shipment
{
    public function __construct(
        public readonly Order $order,
        public readonly string $trackingNumber,
        public readonly DateTimeImmutable $shippedAt
    )
    {
        if (strlen($trackingNumber)) {
            throw new InvalidArgumentException();
        }
    }
}

