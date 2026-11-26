<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repositories;

use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(private Transaction $model) {}

    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return $this->model->find($id);
    }

    public function getByAccount(int $accountId, string $type = 'all'): Collection
    {
        $query = $this->model->query()->where('account_id', $accountId);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        return $query->orderByDesc('transaction_date')->get();
    }

    public function getRecentByUser(int $userId, int $limit = 10): Collection
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->limit($limit)
            ->get();
    }

    public function getByUser(int $userId, ?string $type = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query()->where('user_id', $userId);

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('transaction_date')->paginate($perPage);
    }

    public function delete(Transaction $transaction): bool
    {
        return (bool) $transaction->delete(); // soft delete благодаря SoftDeletes
    }
}
