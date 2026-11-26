<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => (string) $this->amount, // Money -> string
            'type' => $this->type,
            'account' => [
                'id' => $this->account->id,
                'name' => $this->account->name,
            ],
            'transfer_to_account' => $this->transferToAccount ? [
                'id' => $this->transferToAccount->id,
                'name' => $this->transferToAccount->name,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'transaction_date' => $this->transaction_date->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
