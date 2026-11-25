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
        $family = $this->repository->create($data->toEloquentArray());

        $this->repository->addMember(
            family: $family,
            user: $data->owner,
            role: FamilyRole::OWNER->value
        );

        return $family->fresh(['owner', 'members']);
    }
}
