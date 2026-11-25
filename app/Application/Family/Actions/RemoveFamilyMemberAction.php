<?php

declare(strict_types=1);

namespace App\Application\Family\Actions;

use App\Domain\Family\Contracts\FamilyRepositoryInterface;
use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class RemoveFamilyMemberAction
{
    public function __construct(
        private FamilyRepositoryInterface $repository
    ) {}

    /**
     * @throws AuthorizationException Если нет прав или попытка удалить владельца
     * @throws ModelNotFoundException Если пользователь не состоит в семье
     */
    public function execute(Family $family, User $targetUser, int $currentUserId): void
    {
        if ($family->owner_id === $targetUser->id) {
            throw new AuthorizationException('Невозможно удалить владельца семьи. Передайте права или удалите семью.');
        }

        $isOwner = $family->owner_id === $currentUserId;
        $isSelf = $targetUser->id === $currentUserId;

        if (!$isOwner && !$isSelf) {
            throw new AuthorizationException('У вас нет прав для удаления этого участника.');
        }

        $isMember = $family->members()->where('user_id', $targetUser->id)->exists();
        if (!$isMember) {
            throw new ModelNotFoundException('Пользователь не является участником этой семьи.');
        }

        $this->repository->removeMember($family, $targetUser);
    }
}
