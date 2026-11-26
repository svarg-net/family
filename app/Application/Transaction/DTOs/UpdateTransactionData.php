<?php

declare(strict_types=1);

namespace App\Application\Transaction\DTOs;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\ValueObjects\Money;

final readonly class UpdateTransactionData
{
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?Money $amount,
        public ?TransactionType $type,
        public ?int $accountId,
        public ?int $transferToAccountId,
        public ?int $categoryId,
        public ?\DateTimeInterface $transactionDate,
    ) {}
}
