<?php

namespace App\Models;

use App\Domain\Account\Enums\AccountType;
use App\Domain\Transaction\ValueObjects\MoneyCast;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'description', 'type', 'currency', 'balance',
    'visibility', 'user_id', 'family_id', 'category_id'
])]
class Account extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'balance' => MoneyCast::class . ':currency',
            'user_id' => 'integer',
            'family_id' => 'integer',
            'category_id' => 'integer',
            'type' => AccountType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function allowsNegativeBalance(): bool
    {
        return $this->type === AccountType::CREDIT;
    }
}
