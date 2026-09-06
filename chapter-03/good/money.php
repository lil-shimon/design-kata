<?php

enum Currency: string {
    case JPY = 'JPY';
    case USD = 'USD';
}

class Money {
  public function __construct(private readonly int $amount, private readonly Currency $currency) {
    if ($amount < 0) {
      throw new InvalidArgumentException('amountは0以上にしてください。');
    }
  }

  public function add(Money $other): self {
    if ($other->currency !== $this->currency) {
      throw new InvalidArgumentException("通貨が異なります");
    }

    $added = $this->amount + $other->amount;

    return new self($added, $this->currency);
  }
}
