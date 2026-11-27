<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type?->value,
            'currency' => $this->currency,
            'balance' => (string) $this->balance, // Money -> string
            'visibility' => $this->visibility,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'family' => $this->family ? [
                'id' => $this->family->id,
                'name' => $this->family->name,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'allows_negative_balance' => $this->allowsNegativeBalance(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
