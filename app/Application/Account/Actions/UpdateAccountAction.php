<?php

declare(strict_types=1);

namespace App\Application\Account\Actions;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Models\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final readonly class UpdateAccountAction
{
    public function __construct(
        private AccountRepositoryInterface     $accountRepository,
        private TransactionRepositoryInterface $transactionRepository,
    )
    {
    }

    /**
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function execute(
        int   $accountId,
        array $data,
        int   $userId
    ): Account
    {
        $account = $this->accountRepository->findById($accountId);

        if (!$account) {
            throw new ModelNotFoundException('Счёт не найден.');
        }

        if ($account->user_id !== $userId) {
            throw new AuthorizationException('Вы не можете редактировать этот счёт.');
        }

        $hasTransactions = $this->transactionRepository->getByAccount($account->id)->isNotEmpty();

        if ($hasTransactions) {
            if (isset($data['type']) && $data['type'] !== $account->type->value) {
                throw new InvalidArgumentException(
                    'Невозможно изменить тип счёта: по нему есть транзакции.'
                );
            }

            if (isset($data['currency']) && $data['currency'] !== $account->currency) {
                throw new InvalidArgumentException(
                    'Невозможно изменить валюту счёта: по нему есть транзакции.'
                );
            }
        }

        return $this->accountRepository->update($account, $data);
    }
}
