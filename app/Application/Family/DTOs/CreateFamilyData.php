<?php

declare(strict_types=1);

namespace App\Application\Family\DTOs;

use App\Models\User;
use InvalidArgumentException;

final readonly class CreateFamilyData
{
    public string $name;
    public string $inviteCode;

    public function __construct(
        string         $name,
        public ?string $description,
        public ?string $avatar,
        public User    $owner,
        ?string        $inviteCode = null,
    )
    {
        $trimmedName = trim($name);

        if ($trimmedName === '') {
            throw new InvalidArgumentException('Название семьи не может быть пустым');
        }

        $this->name = $trimmedName;
        $this->inviteCode = $inviteCode ?? self::generateInviteCode();
    }

    private static function generateInviteCode(): string
    {
        return strtoupper(bin2hex(random_bytes(5)));
    }

    public function toEloquentArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'owner_id' => $this->owner->id,
            'invite_code' => $this->inviteCode,
        ];
    }
}
