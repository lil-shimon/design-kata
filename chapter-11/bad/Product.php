<?php

enum ProductStatus
{
    case OnSale;
    case Reserved;
    case Ordered;
    case Shipped;
    case Canceled;
}

class Product
{
    public ProductStatus $status = ProductStatus::OnSale;

    public ?int $userId = null;
    public int $quantity = 0;

    // 予約
    public ?DateTimeImmutable $reservedAt = null;
    public ?DateTimeImmutable $releaseDate = null;
    public int $deposit = 0;

    // 注文
    public ?DateTimeImmutable $orderedAt = null;
    public ?string $paymentMethod = null;

    // 発送
    public ?string $address = null;
    public ?DateTimeImmutable $shippedAt = null;
    public ?string $trackingNumber = null;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public int $price,
        public int $stock,
    ) {
    }

    public function reserve(int $userId, int $quantity, DateTimeImmutable $releaseDate): void
    {
        if ($this->status !== ProductStatus::OnSale) {
            throw new LogicException('予約できない状態です');
        }

        $this->userId = $userId;
        $this->quantity = $quantity;
        $this->reservedAt = new DateTimeImmutable();
        $this->releaseDate = $releaseDate;
        // 予約金は価格の1割
        $this->deposit = (int) ($this->price * $quantity * 0.1);
        $this->status = ProductStatus::Reserved;
    }

    public function order(int $userId, int $quantity, string $paymentMethod, string $address): void
    {
        if ($this->status !== ProductStatus::OnSale && $this->status !== ProductStatus::Reserved) {
            throw new LogicException('注文できない状態です');
        }

        if ($this->stock < $quantity) {
            throw new LogicException('在庫が足りません');
        }

        $this->userId = $userId;
        $this->quantity = $quantity;
        $this->orderedAt = new DateTimeImmutable();
        $this->paymentMethod = $paymentMethod;
        $this->address = $address;
        $this->stock -= $quantity;
        $this->status = ProductStatus::Ordered;
    }

    public function ship(string $trackingNumber): void
    {
        if ($this->status !== ProductStatus::Ordered) {
            throw new LogicException('発送できない状態です');
        }

        if ($this->address === null) {
            throw new LogicException('住所が設定されていません');
        }

        $this->shippedAt = new DateTimeImmutable();
        $this->trackingNumber = $trackingNumber;
        $this->status = ProductStatus::Shipped;
    }

    public function cancel(): void
    {
        if ($this->status === ProductStatus::Shipped) {
            throw new LogicException('発送済みのためキャンセルできません');
        }

        if ($this->status === ProductStatus::Ordered) {
            $this->stock += $this->quantity;
        }

        $this->status = ProductStatus::Canceled;
    }

    public function totalPrice(): int
    {
        if ($this->status === ProductStatus::Reserved) {
            return $this->price * $this->quantity - $this->deposit;
        }

        if ($this->status === ProductStatus::Ordered || $this->status === ProductStatus::Shipped) {
            // 5000円未満は送料500円
            $total = $this->price * $this->quantity;
            if ($total < 5000) {
                $total += 500;
            }

            return $total;
        }

        return $this->price;
    }
}
