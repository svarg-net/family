<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Family\Actions\CreateFamilyAction;
use App\Application\Family\DTOs\CreateFamilyData;
use App\Presentation\Http\Requests\CreateFamilyRequest;
use App\Presentation\Http\Resources\FamilyResource;
use Illuminate\Http\JsonResponse;

final readonly class FamilyController
{
    public function __construct(
        private CreateFamilyAction $createFamilyAction
    ) {}

    public function store(CreateFamilyRequest $request): JsonResponse
    {
        try {
            $dto = new CreateFamilyData(
                name: $request->validated('name'),
                description: $request->validated('description'),
                avatar: $request->validated('avatar'),
                owner: $request->user(),
            );

            $family = $this->createFamilyAction->execute($dto);

            return response()->json(new FamilyResource($family), 201);
        } catch (\Throwable $e) {
            // Перехватываем ЛЮБУЮ ошибку и возвращаем её детали в ответе
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'class' => get_class($e),
            ], 500);
        }
    }
}
