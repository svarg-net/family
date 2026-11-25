<?php

declare(strict_types=1);

namespace App\Application\Family\Actions;

use App\Application\Family\DTOs\CreateFamilyData;
use App\Domain\Family\Contracts\FamilyRepositoryInterface;
use App\Domain\Family\Enums\FamilyRole;
use App\Models\Family;

final readonly class CreateFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $repository
    ) {}

    public function execute(CreateFamilyData $data): Family
    {
        // 1. Создаем семью в БД
        $family = $this->repository->create($data->toEloquentArray());

        // 2. Бизнес-правило: добавляем владельца в pivot-таблицу
        // Ваш интерфейс позволяет передать готовые объекты, что безопасно и красиво
        $this->repository->addMember(
            family: $family,
            user: $data->owner,
            role: FamilyRole::OWNER->value
        );

        // 3. Возвращаем свежую модель с загруженными связями
        return $family->fresh(['owner', 'members']);
    }
}
