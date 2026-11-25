<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Family\Actions\CreateFamilyAction;
use App\Application\Family\Actions\JoinFamilyAction;
use App\Application\Family\Actions\RemoveFamilyMemberAction;
use App\Application\Family\Actions\UpdateFamilyAction;
use App\Application\Family\DTOs\CreateFamilyData;
use App\Application\Family\DTOs\JoinFamilyData;
use App\Application\Family\DTOs\UpdateFamilyData;
use App\Models\Family;
use App\Models\User;
use App\Presentation\Http\Requests\CreateFamilyRequest;
use App\Presentation\Http\Requests\JoinFamilyRequest;
use App\Presentation\Http\Requests\UpdateFamilyRequest;
use App\Presentation\Http\Resources\FamilyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class FamilyController
{
    public function __construct(
        private CreateFamilyAction $createFamilyAction,
        private UpdateFamilyAction $updateFamilyAction,
        private JoinFamilyAction $joinFamilyAction,
        private RemoveFamilyMemberAction $removeFamilyMemberAction
    ) {}

    public function store(CreateFamilyRequest $request): JsonResponse
    {
        $dto = new CreateFamilyData(
            name: $request->validated('name'),
            description: $request->validated('description'),
            avatar: $request->validated('avatar'),
            owner: $request->user(),
        );

        $family = $this->createFamilyAction->execute($dto);
        return response()->json(new FamilyResource($family), 201);
    }

    public function update(int $id, UpdateFamilyRequest $request): JsonResponse
    {
        try {
            $dto = new UpdateFamilyData(
                familyId: $id,
                name: $request->validated('name'),
                description: $request->validated('description'),
                avatar: $request->validated('avatar'),
            );

            $family = $this->updateFamilyAction->execute($dto, $request->user()->id);
            return response()->json(new FamilyResource($family));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'class' => get_class($e),
            ], 500);
        }
    }

    public function join(JoinFamilyRequest $request): JsonResponse
    {
        try {
            $dto = new JoinFamilyData(
                inviteCode: strtoupper($request->validated('invite_code')),
                user: $request->user(),
            );

            $family = $this->joinFamilyAction->execute($dto);
            return response()->json(new FamilyResource($family));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'class' => get_class($e),
            ], 500);
        }
    }

    public function removeMember(Family $family, User $user, Request $request): JsonResponse
    {
        $this->removeFamilyMemberAction->execute(
            family: $family,
            targetUser: $user,
            currentUserId: $request->user()->id
        );

        return response()->json([
            'message' => 'Участник успешно удален из семьи'
        ], 200);
    }
}
