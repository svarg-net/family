<?php

declare(strict_types=1);

namespace App\Application\Transaction\Actions;

use App\Application\Transaction\DTOs\CreateTransactionData;
use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final readonly class CreateTransactionAction
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    /**
     * @throws ModelNotFoundException Если счёт не найден
     * @throws InvalidArgumentException Если баланс не может уйти в минус
     */
    public function execute(CreateTransactionData $data): Transaction
    {
        $account = $this->accountRepository->findById($data->accountId);

        if (!$account) {
            throw new ModelNotFoundException('Исходный счёт не найден.');
        }

        if ($account->balance->currency() !== $data->amount->currency()) {
            throw new InvalidArgumentException(
                "Валюта транзакции ({$data->amount->currency()}) не совпадает с валютой счёта ({$account->balance->currency()})."
            );
        }

        $targetAccount = null;
        if ($data->type === TransactionType::TRANSFER) {
            $targetAccount = $this->accountRepository->findById($data->transferToAccountId);

            if (!$targetAccount) {
                throw new ModelNotFoundException('Целевой счёт для перевода не найден.');
            }

            // Проверяем, что валюты совпадают
            if ($targetAccount->balance->currency() !== $data->amount->currency()) {
                throw new InvalidArgumentException(
                    "Валюта целевого счёта ({$targetAccount->balance->currency()}) не совпадает с валютой транзакции."
                );
            }
        }

        $newBalance = $account->balance;

        match ($data->type) {
            TransactionType::INCOME => $newBalance = $account->balance->add($data->amount),
            TransactionType::EXPENSE => $newBalance = $account->balance->subtract($data->amount),
            TransactionType::TRANSFER => $newBalance = $account->balance->subtract($data->amount),
        };

        if (!$account->allowsNegativeBalance() && $newBalance->isNegative()) {
            throw new InvalidArgumentException(
                "Счёт '{$account->name}' не может иметь отрицательный баланс. Текущий баланс: {$account->balance}, после операции: {$newBalance}."
            );
        }

        $this->accountRepository->updateBalance($account, $newBalance->amount());

        if ($data->type === TransactionType::TRANSFER && $targetAccount) {
            $targetNewBalance = $targetAccount->balance->add($data->amount);
            $this->accountRepository->updateBalance($targetAccount, $targetNewBalance->amount());
        }

        return $this->transactionRepository->create($data->toEloquentArray());
    }
}
