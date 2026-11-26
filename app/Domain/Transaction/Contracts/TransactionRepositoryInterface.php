<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Contracts;

use App\Models\Transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    /**
     * Создать новую транзакцию
     */
    public function create(array $data): Transaction;

    /**
     * Найти транзакцию по ID
     */
    public function findById(int $id): ?Transaction;

    /**
     * Получить транзакции по конкретному счёту (с фильтрацией по типу)
     */
    public function getByAccount(int $accountId, string $type = 'all'): Collection;

    /**
     * Получить последние N транзакций пользователя (для дашборда)
     */
    public function getRecentByUser(int $userId, int $limit = 10): Collection;

    /**
     * Получить все транзакции пользователя с пагинацией (для истории)
     */
    public function getByUser(int $userId, ?string $type = null, int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Удалить транзакцию (soft delete)
     */
    public function delete(Transaction $transaction): bool;

    /**
     * Обновить транзакцию
     */
    public function update(Transaction $transaction, array $data): Transaction;
}
