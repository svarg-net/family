<?php

declare(strict_types=1);

namespace App\Domain\Family\Contracts;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Collection;

interface FamilyRepositoryInterface
{
    /**
     * Создать новую семью
     */
    public function create(array $data): Family;

    public function update(Family $family, array $data): Family;

    /**
     * Найти семью по коду приглашения
     */
    public function findByInviteCode(string $code): ?Family;

    public function findById(int $id): ?Family;

    /**
     * Добавить пользователя в семью с определенной ролью
     */
    public function addMember(Family $family, User $user, string $role): void;

    public function removeMember(Family $family, User $user): void;

    /**
     * Получить всех членов семьи
     */
    public function getMembers(Family $family): Collection;
}
