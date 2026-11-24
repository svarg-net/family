<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Task\Actions\CreateTaskAction;
use App\Application\Task\DTOs\CreateTaskData;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskVisibility;
use App\Presentation\Http\Requests\CreateTaskRequest;
use App\Presentation\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;

final class TaskController
{
    public function __construct(
        private readonly CreateTaskAction $createTaskAction
    ) {}

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $dto = new CreateTaskData(
            title: $request->validated('title'),
            description: $request->validated('description'),
            visibility: TaskVisibility::from($request->validated('visibility')),
            priority: TaskPriority::from($request->validated('priority')),
            creatorId: $request->user()->id,
            familyId: $request->validated('family_id'),
            assignedToId: $request->validated('assigned_to'),
            categoryId: $request->validated('category_id'),
        );

        $task = $this->createTaskAction->execute($dto);

        return response()->json(new TaskResource($task), 201);
    }
}
