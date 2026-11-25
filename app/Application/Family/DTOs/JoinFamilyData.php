<?php

declare(strict_types=1);

namespace App\Application\Family\DTOs;

use App\Models\User;

final readonly class JoinFamilyData
{
    public function __construct(
        public string $inviteCode,
        public User $user,
    ) {}
}
