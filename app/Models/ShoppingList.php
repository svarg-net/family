<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'visibility', 'status', 'user_id', 'family_id'])]
class ShoppingList extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'family_id' => 'integer',
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

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingItem::class, 'shopping_list_id');
    }
}
