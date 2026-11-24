<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Task\Actions\CreateTaskAction;
use App\Application\Task\Actions\GetUserTasksAction;
use App\Application\Task\Actions\UpdateTaskStatusAction;
use App\Application\Task\DTOs\CreateTaskData;
use App\Application\Task\DTOs\UpdateTaskStatusData;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Enums\TaskVisibility;
use App\Presentation\Http\Requests\CreateTaskRequest;
use App\Presentation\Http\Requests\UpdateTaskStatusRequest;
use App\Presentation\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final readonly class TaskController
{
    public function __construct(
        private CreateTaskAction $createTaskAction,
        private GetUserTasksAction $getUserTasksAction,
        private UpdateTaskStatusAction $updateTaskStatusAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->getUserTasksAction->execute($request->user()->id);

        return TaskResource::collection($tasks);
    }

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

    public function updateStatus(int $id, UpdateTaskStatusRequest $request): JsonResponse
    {
        $dto = new UpdateTaskStatusData(
            taskId: $id,
            userId: $request->user()->id,
            status: TaskStatus::from($request->validated('status')),
        );

        $task = $this->updateTaskStatusAction->execute($dto);

        return response()->json(new TaskResource($task));
    }
}
