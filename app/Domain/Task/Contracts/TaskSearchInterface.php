<?php

declare(strict_types=1);

namespace App\Domain\Task\Contracts;

use App\Application\Task\DTOs\TaskSearchCriteria;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskSearchInterface
{
    /**
     * Выполнить полнотекстовый поиск задач
     */
    public function search(TaskSearchCriteria $criteria): LengthAwarePaginator;
}
