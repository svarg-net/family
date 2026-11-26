<?php

declare(strict_types=1);

namespace App\Domain\Transaction\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class MoneyCast implements CastsAttributes
{
    private string $currencyColumn;

    public function __construct(string $currencyColumn = 'currency')
    {
        $this->currencyColumn = $currencyColumn;
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        $currency = $attributes[$this->currencyColumn] ?? 'RUB';

        return new Money((float)$value, $currency);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            $model->{$this->currencyColumn} = $value->currency();
            return $value->amount();
        }

        return (float)$value;
    }
}
