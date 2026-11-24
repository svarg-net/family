<?php


declare(strict_types=1);

namespace App\Application\Task\Actions;

use App\Application\Task\DTOs\TaskSearchCriteria;
use App\Domain\Task\Contracts\TaskSearchInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class SearchTasksAction
{
    public function __construct(
        private TaskSearchInterface $searchRepository
    )
    {
    }

    public function execute(TaskSearchCriteria $criteria): LengthAwarePaginator
    {
        return $this->searchRepository->search($criteria);
    }
}
