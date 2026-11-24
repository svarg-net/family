<?php

namespace App\Domain\Family\Enums;

enum FamilyRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
}
