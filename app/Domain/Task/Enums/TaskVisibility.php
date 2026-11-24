<?php

namespace App\Domain\Task\Enums;

enum TaskVisibility: string
{
    case PRIVATE = 'private';
    case FAMILY = 'family';
}
