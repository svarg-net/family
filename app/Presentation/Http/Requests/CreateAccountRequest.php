<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Account\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;

final class CreateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeRules = implode(',', array_column(AccountType::cases(), 'value'));

        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', 'in:' . $typeRules],
            'currency' => ['required', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric'],
            'visibility' => ['required', 'string', 'in:private,family'],
            'family_id' => ['nullable', 'integer', 'exists:families,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Название счёта должно содержать минимум 2 символа.',
            'currency.size' => 'Валюта должна состоять из 3 символов (например, RUB, USD).',
            'type.in' => 'Недопустимый тип счёта.',
            'visibility.in' => 'Видимость должна быть private или family.',
        ];
    }
}
