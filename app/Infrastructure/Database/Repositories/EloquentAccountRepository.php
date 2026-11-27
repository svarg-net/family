<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repositories;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Models\Account;
use Illuminate\Support\Collection;

final readonly class EloquentAccountRepository implements AccountRepositoryInterface
{
    public function __construct(private Account $model)
    {
    }

    public function findById(int $id): ?Account
    {
        return $this->model->find($id);
    }

    public function getForUser(int $userId, ?int $familyId = null): Collection
    {
        return $this->model->query()
            ->where(function ($query) use ($userId, $familyId) {
                $query->where('user_id', $userId)
                    ->where('visibility', 'private');

                if ($familyId !== null) {
                    $query->orWhere(function ($q) use ($familyId) {
                        $q->where('family_id', $familyId)
                            ->where('visibility', 'family');
                    });
                }
            })
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByFamily(int $familyId): Collection
    {
        return $this->model->query()
            ->where('family_id', $familyId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function updateBalance(Account $account, float $newBalance): Account
    {
        $account->update(['balance' => $newBalance]);
        return $account->fresh();
    }

    public function delete(Account $account): bool
    {
        return (bool)$account->delete();
    }

    public function update(Account $account, array $data): Account
    {
        $account->update($data);
        return $account->fresh();
    }

    public function create(array $data): Account
    {
        return $this->model->create($data);
    }
}
