<?php

declare(strict_types=1);

namespace App\Application\Task\DTOs;

use App\Domain\Task\Enums\TaskStatus;

final readonly class UpdateTaskStatusData
{
    public function __construct(
        public int $taskId,
        public int $userId,
        public TaskStatus $status,
    ) {}
}
