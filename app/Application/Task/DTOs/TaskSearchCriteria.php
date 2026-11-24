<?php

declare(strict_types=1);

namespace App\Application\Task\DTOs;

use App\Domain\Task\Enums\TaskStatus;

final readonly class TaskSearchCriteria
{
    public const int DEFAULT_LIMIT = 20;
    public const int MAX_LIMIT = 100;

    public int $limit;
    public int $offset;

    public function __construct(
        public string $keyword = '',
        public ?TaskStatus $status = null,
        public ?int $userId = null,
        public ?int $familyId = null,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = 0,
    ) {
        $this->limit = max(1, min($limit, self::MAX_LIMIT));
        $this->offset = max(0, $offset);
    }
}
