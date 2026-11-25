<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'avatar', 'owner_id', 'invite_code'])]
class Family extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'owner_id' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'family_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function shoppingLists(): HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
