<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class JoinFamilyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'invite_code' => ['required', 'string', 'size:10', 'regex:/^[A-Z0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'invite_code.regex' => 'Код приглашения должен состоять из 10 заглавных букв и цифр.',
        ];
    }
}
