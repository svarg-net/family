<?php

namespace App\Domain\Shopping\Enums;

enum ShoppingListStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}
