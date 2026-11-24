<?php

namespace App\Domain\Shopping\Enums;

enum ShoppingItemUnit: string
{
    case PIECE = 'шт';
    case KILOGRAM = 'кг';
    case LITER = 'л';
    case PACK = 'упак';
    case BOX = 'кор';
}
