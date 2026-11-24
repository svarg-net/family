<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'description', 'visibility', 'status', 'priority',
    'due_date', 'category_id', 'user_id', 'family_id', 'assigned_to'
])]
class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'category_id' => 'integer',
            'user_id' => 'integer',
            'family_id' => 'integer',
            'assigned_to' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
