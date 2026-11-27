<?php

declare(strict_types=1);

namespace App\Application\Transaction\Actions;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeleteTransactionAction
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private AccountRepositoryInterface $accountRepository,
    ) {}

    /**
     * @throws ModelNotFoundException Если транзакция не найдена
     * @throws AuthorizationException Если пользователь не является автором
     */
    public function execute(int $transactionId, int $userId): void
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new ModelNotFoundException('Транзакция не найдена.');
        }

        if ($transaction->user_id !== $userId) {
            throw new AuthorizationException('Вы не можете удалить эту транзакцию.');
        }

        $account = $this->accountRepository->findById($transaction->account_id);

        if (!$account) {
            throw new ModelNotFoundException('Счёт, связанный с транзакцией, не найден.');
        }

        $newBalance = match ($transaction->type) {
            TransactionType::INCOME => $account->balance->subtract($transaction->amount),
            TransactionType::EXPENSE => $account->balance->add($transaction->amount),
            TransactionType::TRANSFER => $account->balance->add($transaction->amount),
        };

        $this->accountRepository->updateBalance($account, $newBalance->amount());

        if ($transaction->type === TransactionType::TRANSFER && $transaction->transfer_to_account_id) {
            $targetAccount = $this->accountRepository->findById($transaction->transfer_to_account_id);

            if ($targetAccount) {
                $targetNewBalance = $targetAccount->balance->subtract($transaction->amount);
                $this->accountRepository->updateBalance($targetAccount, $targetNewBalance->amount());
            }
        }

        $this->transactionRepository->delete($transaction);
    }
}
