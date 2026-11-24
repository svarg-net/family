<?php

declare(strict_types=1);

namespace App\Domain\Task\Contracts;

use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    /**
     * Создать новую задачу
     */
    public function create(array $data): Task;

    /**
     * Обновить существующую задачу
     */
    public function update(Task $task, array $data): Task;

    /**
     * Удалить задачу (использует soft deletes)
     */
    public function delete(Task $task): bool;

    /**
     * Найти задачу по ID
     */
    public function findById(int $id): ?Task;

    /**
     * Получить все задачи пользователя (его личные + семейные, где он участник)
     * Это ключевой метод, который скрывает сложную SQL-логику от бизнес-уровня
     */
    public function getForUser(int $userId): Collection;

    /**
     * Получить задачи, назначенные конкретному пользователю (исполнитель)
     */
    public function getAssignedToUser(int $userId): Collection;
}
