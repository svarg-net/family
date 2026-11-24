<?php

declare(strict_types=1);

namespace App\Application\Task\Actions;

use App\Application\Task\DTOs\UpdateTaskStatusData;
use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;

final readonly class UpdateTaskStatusAction
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    /**
     * @throws AuthorizationException Если пользователь не имеет прав на изменение задачи
     */
    public function execute(UpdateTaskStatusData $data): Task
    {
        $task = $this->repository->findById($data->taskId);

        if (!$task) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Задача не найдена');
        }

        $isOwner = $task->user_id === $data->userId;
        $isFamilyMember = $task->visibility === 'family' && $task->family_id !== null;

        if (!$isOwner && !$isFamilyMember) {
            throw new AuthorizationException('У вас нет прав на изменение этой задачи.');
        }

        return $this->repository->update($task, ['status' => $data->status->value]);
    }
}
