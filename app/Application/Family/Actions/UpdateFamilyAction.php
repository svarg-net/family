<?php

declare(strict_types=1);

namespace App\Application\Family\Actions;

use App\Application\Family\DTOs\UpdateFamilyData;
use App\Domain\Family\Contracts\FamilyRepositoryInterface;
use App\Models\Family;
use Illuminate\Auth\Access\AuthorizationException;

final readonly class UpdateFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $repository
    ) {}

    /**
     * @throws AuthorizationException Если пользователь не является владельцем
     */
    public function execute(UpdateFamilyData $data, int $currentUserId): Family
    {
        $family = $this->repository->findById($data->familyId); // Добавим этот метод в интерфейс, если его нет, или используем find

        if (!$family) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Семья не найдена');
        }

        // БИЗНЕС-ПРАВИЛО: Редактировать семью может только её владелец
        if ($family->owner_id !== $currentUserId) {
            throw new AuthorizationException('Только владелец семьи может изменять её данные.');
        }

        return $this->repository->update($family, $data->toEloquentArray());
    }
}
