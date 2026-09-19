<?php

class CanceledOrder
{
    public function __construct(
        public readonly Order $order,
        public readonly DateTimeImmutable $canceledAt
    )
    {
    }
}

