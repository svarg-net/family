<?php

declare(strict_types=1);

namespace App\Application\Task\Actions;

use App\Application\Task\DTOs\CreateTaskData;
use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Models\Task;

final readonly class CreateTaskAction
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    /**
     * Выполнить бизнес-сценарий создания задачи.
     *
     * @param CreateTaskData $data DTO с уже провалидированными данными и проверенными инвариантами
     * @return Task Созданная задача (Eloquent-модель)
     */
    public function execute(CreateTaskData $data): Task
    {
        return $this->repository->create($data->toEloquentArray());
    }
}
