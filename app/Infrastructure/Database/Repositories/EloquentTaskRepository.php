<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repositories;

use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Collection;

final readonly class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly Task $model
    ) {}

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    public function findById(int $id): ?Task
    {
        return $this->model->find($id);
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->query()
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('visibility', 'private');

                $query->orWhere(function ($q) use ($userId) {
                    $q->where('visibility', 'family')
                        ->whereIn('family_id', function ($subQuery) use ($userId) {
                            $subQuery->select('family_id')
                                ->from('family_user')
                                ->where('user_id', $userId);
                        });
                });
            })
            ->with(['category', 'assignedTo', 'family'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAssignedToUser(int $userId): Collection
    {
        return $this->model->query()
            ->where('assigned_to', $userId)
            ->orderByDesc('created_at')
            ->get();
    }
}
