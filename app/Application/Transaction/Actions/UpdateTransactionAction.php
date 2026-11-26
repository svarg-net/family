<?php

declare(strict_types=1);

namespace App\Application\Transaction\Actions;

use App\Application\Transaction\DTOs\UpdateTransactionData;
use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final readonly class UpdateTransactionAction
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private AccountRepositoryInterface $accountRepository,
    ) {}

    /**
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function execute(int $transactionId, UpdateTransactionData $newData, int $userId): Transaction
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new ModelNotFoundException('Транзакция не найдена.');
        }

        if ($transaction->user_id !== $userId) {
            throw new AuthorizationException('Вы не можете редактировать эту транзакцию.');
        }

        $finalTitle = $newData->title ?? $transaction->title;
        $finalDescription = $newData->description ?? $transaction->description;
        $finalAmount = $newData->amount ?? $transaction->amount; // Money VO
        $finalType = $newData->type ?? TransactionType::from($transaction->type->value);
        $finalAccountId = $newData->accountId ?? $transaction->account_id;
        $finalTransferToAccountId = $newData->transferToAccountId ?? $transaction->transfer_to_account_id;
        $finalCategoryId = $newData->categoryId ?? $transaction->category_id;
        $finalTransactionDate = $newData->transactionDate ?? $transaction->transaction_date;

        $oldAccount = $this->accountRepository->findById($transaction->account_id);

        if (!$oldAccount) {
            throw new ModelNotFoundException('Исходный счёт транзакции не найден.');
        }

        $oldAccountBalance = match ($transaction->type) {
            TransactionType::INCOME => $oldAccount->balance->subtract($transaction->amount),
            TransactionType::EXPENSE => $oldAccount->balance->add($transaction->amount),
            TransactionType::TRANSFER => $oldAccount->balance->add($transaction->amount),
        };

        $this->accountRepository->updateBalance($oldAccount, $oldAccountBalance->amount());

        if ($transaction->type === TransactionType::TRANSFER && $transaction->transfer_to_account_id) {
            $oldTargetAccount = $this->accountRepository->findById($transaction->transfer_to_account_id);

            if ($oldTargetAccount) {
                $oldTargetBalance = $oldTargetAccount->balance->subtract($transaction->amount);
                $this->accountRepository->updateBalance($oldTargetAccount, $oldTargetBalance->amount());
            }
        }

        if ($oldAccount->balance->currency() !== $finalAmount->currency()) {
            $this->accountRepository->updateBalance($oldAccount, $transaction->amount->amount());
            throw new InvalidArgumentException(
                "Валюта транзакции ({$finalAmount->currency()}) не совпадает с валютой счёта ({$oldAccount->balance->currency()})."
            );
        }

        $newAccount = $oldAccount;
        if ($finalAccountId !== $transaction->account_id) {
            $newAccount = $this->accountRepository->findById($finalAccountId);

            if (!$newAccount) {
                throw new ModelNotFoundException('Новый счёт не найден.');
            }

            if ($newAccount->balance->currency() !== $finalAmount->currency()) {
                throw new InvalidArgumentException(
                    "Валюта нового счёта ({$newAccount->balance->currency()}) не совпадает с валютой транзакции."
                );
            }
        }

        $newTargetAccount = null;
        if ($finalType === TransactionType::TRANSFER) {
            $newTargetAccount = $this->accountRepository->findById($finalTransferToAccountId);

            if (!$newTargetAccount) {
                throw new ModelNotFoundException('Целевой счёт для перевода не найден.');
            }

            if ($newTargetAccount->balance->currency() !== $finalAmount->currency()) {
                throw new InvalidArgumentException(
                    "Валюта целевого счёта ({$newTargetAccount->balance->currency()}) не совпадает с валютой транзакции."
                );
            }
        }

        $newBalance = match ($finalType) {
            TransactionType::INCOME => $newAccount->balance->add($finalAmount),
            TransactionType::EXPENSE => $newAccount->balance->subtract($finalAmount),
            TransactionType::TRANSFER => $newAccount->balance->subtract($finalAmount),
        };

        if (!$newAccount->allowsNegativeBalance() && $newBalance->isNegative()) {
            throw new InvalidArgumentException(
                "Счёт '{$newAccount->name}' не может иметь отрицательный баланс."
            );
        }

        $this->accountRepository->updateBalance($newAccount, $newBalance->amount());

        if ($finalType === TransactionType::TRANSFER && $newTargetAccount) {
            $newTargetBalance = $newTargetAccount->balance->add($finalAmount);
            $this->accountRepository->updateBalance($newTargetAccount, $newTargetBalance->amount());
        }

        $updateData = [
            'title' => $finalTitle,
            'description' => $finalDescription,
            'amount' => $finalAmount->amount(),
            'currency' => $finalAmount->currency(),
            'type' => $finalType->value,
            'account_id' => $finalAccountId,
            'transfer_to_account_id' => $finalTransferToAccountId,
            'category_id' => $finalCategoryId,
            'transaction_date' => $finalTransactionDate,
        ];

        $updatedTransaction = $this->transactionRepository->update($transaction, $updateData);

        return $updatedTransaction->fresh(['account', 'transferToAccount', 'category']);
    }
}
