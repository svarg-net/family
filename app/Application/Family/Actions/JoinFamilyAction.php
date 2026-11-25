<?php

declare(strict_types=1);

namespace App\Application\Family\Actions;

use App\Application\Family\DTOs\JoinFamilyData;
use App\Domain\Family\Contracts\FamilyRepositoryInterface;
use App\Domain\Family\Enums\FamilyRole;
use App\Models\Family;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class JoinFamilyAction
{
    public function __construct(
        private FamilyRepositoryInterface $repository
    ) {}

    /**
     * @throws ModelNotFoundException Если код неверный
     */
    public function execute(JoinFamilyData $data): Family
    {
        $family = $this->repository->findByInviteCode($data->inviteCode);

        if (!$family) {
            throw new ModelNotFoundException('Семья с таким кодом приглашения не найдена.');
        }

        $this->repository->addMember(
            family: $family,
            user: $data->user,
            role: FamilyRole::MEMBER->value
        );

        return $family->fresh(['owner', 'members']);
    }
}
