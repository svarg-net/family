<?php

declare(strict_types=1);

namespace App\Domain\Shopping\Contracts;

use App\Models\ShoppingList;
use Illuminate\Support\Collection;

interface ShoppingListRepositoryInterface
{
    /**
     * Создать новый список покупок
     */
    public function create(array $data): ShoppingList;

    /**
     * Получить активные списки пользователя (личные + семейные)
     */
    public function getActiveForUser(int $userId, ?int $familyId = null): Collection;

    /**
     * Отметить список как завершенный
     */
    public function markAsCompleted(ShoppingList $list): ShoppingList;
}
