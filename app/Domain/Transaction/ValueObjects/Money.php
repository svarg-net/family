<?php

namespace App\Domain\Transaction\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private float $amount,
        private string $currency = 'RUB'
    ) {
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Нельзя сложить разные валюты: {$this->currency} и {$other->currency}");
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Нельзя вычесть разные валюты: {$this->currency} и {$other->currency}");
        }

        // Результат может быть отрицательным (уход в минус по кредитке)
        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * Абсолютное значение (полезно для отображения суммы транзакции без учета знака)
     */
    public function abs(): self
    {
        return new self(abs($this->amount), $this->currency);
    }
}
