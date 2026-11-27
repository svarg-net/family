<?php

declare(strict_types=1);

namespace App\Domain\Account\Contracts;

use App\Models\Account;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    /**
     * Найти счёт по ID
     */
    public function findById(int $id): ?Account;

    /**
     * Получить все счета пользователя (личные + семейные, если передан familyId)
     */
    public function getForUser(int $userId, ?int $familyId = null): Collection;

    /**
     * Получить семейные счета (по family_id)
     */
    public function getByFamily(int $familyId): Collection;

    /**
     * Обновить баланс счёта
     */
    public function updateBalance(Account $account, float $newBalance): Account;

    public function delete(Account $account): bool;

    public function update(Account $account, array $data): Account;
}
