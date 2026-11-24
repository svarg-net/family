<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'description', 'amount', 'type', 'transaction_date',
    'account_id', 'category_id', 'user_id', 'transfer_to_account_id'
])]
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
            'account_id' => 'integer',
            'category_id' => 'integer',
            'user_id' => 'integer',
            'transfer_to_account_id' => 'integer',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transferToAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'transfer_to_account_id');
    }
}
