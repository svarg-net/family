<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class, 'family_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function ownedFamilies(): HasMany
    {
        return $this->hasMany(Family::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->HasMany(Task::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->HasMany(Task::class, 'assigned_to');
    }

    public function accounts(): HasMany
    {
        return $this->HasMany(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->HasMany(Transaction::class);
    }

    public function shoppingLists(): HasMany
    {
        return $this->HasMany(ShoppingList::class);
    }

    public function categories(): HasMany
    {
        return $this->HasMany(Category::class);
    }
}
