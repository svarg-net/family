<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use App\Domain\Transaction\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

final class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeRules = implode(',', array_column(TransactionType::cases(), 'value'));

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'type' => ['required', 'string', 'in:' . $typeRules],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'transfer_to_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:account_id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'transfer_to_account_id.different' => 'Нельзя перевести средства на тот же счёт.',
            'amount.min' => 'Сумма транзакции должна быть положительной.',
        ];
    }
}
