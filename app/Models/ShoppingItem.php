<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name', 'description', 'quantity', 'unit', 'estimated_price',
    'actual_price', 'is_bought', 'priority', 'shopping_list_id',
    'category_id', 'added_by', 'bought_by'
])]
class ShoppingItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'estimated_price' => 'decimal:2',
            'actual_price' => 'decimal:2',
            'is_bought' => 'boolean',
            'priority' => 'integer',
            'shopping_list_id' => 'integer',
            'category_id' => 'integer',
            'added_by' => 'integer',
            'bought_by' => 'integer',
        ];
    }

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function boughtBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bought_by');
    }
}
