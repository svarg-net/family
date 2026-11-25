<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Название семьи должно содержать минимум 2 символа.',
            'avatar.url' => 'Аватар должен быть корректным URL.',
        ];
    }
}
