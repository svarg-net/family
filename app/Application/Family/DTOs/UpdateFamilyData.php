<?php

declare(strict_types=1);

namespace App\Application\Family\DTOs;

final readonly class UpdateFamilyData
{
    public function __construct(
        public int $familyId,
        public ?string $name,
        public ?string $description,
        public ?string $avatar,
    ) {}

    public function toEloquentArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
        ], fn ($value) => $value !== null);
    }
}
