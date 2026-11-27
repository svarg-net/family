<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Account\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeRules = implode(',', array_column(AccountType::cases(), 'value'));

        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'type' => ['sometimes', 'string', 'in:' . $typeRules],
            'currency' => ['sometimes', 'string', 'size:3'],
            'visibility' => ['sometimes', 'string', 'in:private,family'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Название счёта должно содержать минимум 2 символа.',
            'currency.size' => 'Валюта должна состоять из 3 символов (например, RUB, USD).',
        ];
    }
}
