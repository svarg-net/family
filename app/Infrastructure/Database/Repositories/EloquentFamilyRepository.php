<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repositories;

use App\Domain\Family\Contracts\FamilyRepositoryInterface;
use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class EloquentFamilyRepository implements FamilyRepositoryInterface
{
    public function __construct(
        private Family $model
    ) {}

    public function create(array $data): Family
    {
        return $this->model->create($data);
    }

    public function update(Family $family, array $data): Family
    {
        $family->update($data);
        return $family->fresh();
    }

    public function findByInviteCode(string $code): ?Family
    {
        return $this->model->query()
            ->where('invite_code', strtoupper($code))
            ->first();
    }

    public function findById(int $id): ?Family
    {
        return $this->model->find($id);
    }

    public function addMember(Family $family, User $user, string $role): void
    {
        $family->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    public function removeMember(Family $family, User $user): void
    {
        $family->members()->detach($user->id);
    }

    public function getMembers(Family $family): Collection
    {
        return $family->members()->withPivot('role', 'joined_at')->get();
    }
}
