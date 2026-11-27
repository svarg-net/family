<?php

declare(strict_types=1);

namespace App\Application\Transaction\DTOs;

use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\ValueObjects\Money;
use App\Models\User;
use InvalidArgumentException;

final readonly class CreateTransactionData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public Money $amount,
        public TransactionType $type,
        public int $accountId,
        public ?int $transferToAccountId,
        public ?int $categoryId,
        public User $user,
        public ?\DateTimeInterface $transactionDate = null,
    ) {
        // БИЗНЕС-ПРАВИЛО: Для transfer обязателен target account
        if ($this->type === TransactionType::TRANSFER && $this->transferToAccountId === null) {
            throw new InvalidArgumentException('Для перевода между счетами необходимо указать целевой счёт.');
        }

        // БИЗНЕС-ПРАВИЛО: Сумма транзакции должна быть положительной
        if ($this->amount->amount() <= 0) {
            throw new InvalidArgumentException('Сумма транзакции должна быть положительной.');
        }

        // БИЗНЕС-ПРАВИЛО: Нельзя перевести на тот же счёт
        if ($this->type === TransactionType::TRANSFER && $this->accountId === $this->transferToAccountId) {
            throw new InvalidArgumentException('Нельзя перевести средства на тот же счёт.');
        }
    }

    public function toEloquentArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount->amount(),
            'currency' => $this->amount->currency(),
            'type' => $this->type->value,
            'account_id' => $this->accountId,
            'transfer_to_account_id' => $this->transferToAccountId,
            'category_id' => $this->categoryId,
            'user_id' => $this->user->id,
            'transaction_date' => $this->transactionDate ?? now(),
        ];
    }
}
