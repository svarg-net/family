<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Task\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusRules = implode(',', array_column(TaskStatus::cases(), 'value'));

        return [
            'status' => ['required', 'string', 'in:' . $statusRules],
        ];
    }
}
