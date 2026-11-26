<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Transaction\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeRules = implode(',', array_column(TransactionType::cases(), 'value'));

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'amount' => ['sometimes', 'nullable', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'type' => ['sometimes', 'nullable', 'string', 'in:' . $typeRules],
            'account_id' => ['sometimes', 'nullable', 'integer', 'exists:accounts,id'],
            'transfer_to_account_id' => ['sometimes', 'nullable', 'integer', 'exists:accounts,id'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'transaction_date' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Сумма транзакции должна быть положительной.',
        ];
    }
}
