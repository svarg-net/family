<?php

declare(strict_types=1);

namespace App\Application\Task\DTOs;

use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskVisibility;
use InvalidArgumentException;

final readonly class CreateTaskData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public TaskVisibility $visibility,
        public TaskPriority $priority,
        public int $creatorId,
        public ?int $familyId,
        public ?int $assignedToId,
        public ?int $categoryId,
    ) {
        if ($this->visibility === TaskVisibility::PRIVATE && $this->familyId !== null) {
            throw new InvalidArgumentException('Личная задача не может быть привязана к семье.');
        }

        if ($this->visibility === TaskVisibility::FAMILY && $this->familyId === null) {
            throw new InvalidArgumentException('Семейная задача должна быть привязана к конкретной семье.');
        }
    }

    public function toEloquentArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'visibility' => $this->visibility->value,
            'priority' => $this->priority->value,
            'user_id' => $this->creatorId,
            'family_id' => $this->familyId,
            'assigned_to' => $this->assignedToId,
            'category_id' => $this->categoryId,
            'status' => 'todo',
        ];
    }
}
