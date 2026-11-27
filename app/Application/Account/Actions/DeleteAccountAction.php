<?php

declare(strict_types=1);

namespace App\Application\Account\Actions;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Models\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final readonly class DeleteAccountAction
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    /**
     * @throws ModelNotFoundException Если счёт не найден
     * @throws AuthorizationException Если пользователь не является владельцем
     * @throws InvalidArgumentException Если по счёту есть транзакции
     */
    public function execute(int $accountId, int $userId): void
    {
        $account = $this->accountRepository->findById($accountId);

        if (!$account) {
            throw new ModelNotFoundException('Счёт не найден.');
        }

        if ($account->user_id !== $userId) {
            throw new AuthorizationException('Вы не можете удалить этот счёт.');
        }

        $transactions = $this->transactionRepository->getByAccount($account->id);

        if ($transactions->isNotEmpty()) {
            throw new InvalidArgumentException(
                'Невозможно удалить счёт: по нему есть транзакции. Сначала удалите все транзакции.'
            );
        }

        $this->accountRepository->delete($account);
    }
}
