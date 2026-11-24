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
     * Получить транзакции по конкретному счету
     */
    public function getByAccount(int $accountId, string $type = 'all'): Collection;

    /**
     * Получить последние N транзакций пользователя (для дашборда)
     */
    public function getRecentByUser(int $userId, int $limit = 10): Collection;
}
