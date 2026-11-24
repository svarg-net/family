<?php

declare(strict_types=1);

namespace App\Application\Task\Actions;

use App\Domain\Task\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class GetUserTasksAction
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    public function execute(int $userId): Collection
    {
        return $this->repository->getForUser($userId);
    }
}
