<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskVisibility;
use Illuminate\Foundation\Http\FormRequest;

final class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        //TODO добавить авторизацию
        return true;
    }

    public function rules(): array
    {
        // Получаем массив значений из Enum для правила 'in'
        $visibilityRules = implode(',', array_column(TaskVisibility::cases(), 'value'));
        $priorityRules = implode(',', array_column(TaskPriority::cases(), 'value'));

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visibility' => ['required', 'string', 'in:' . $visibilityRules],
            'priority' => ['required', 'string', 'in:' . $priorityRules],

            'family_id' => ['nullable', 'integer', 'required_if:visibility,' . TaskVisibility::FAMILY->value, 'exists:families,id'],

            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'family_id.required_if' => 'Для семейной задачи необходимо указать ID семьи.',
        ];
    }
}
