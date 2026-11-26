<?php

declare(strict_types=1);

namespace App\Domain\Account\Enums;

enum AccountType: string
{
    case CASH = 'cash';           // Наличные (не может уйти в минус)
    case BANK = 'bank';           // Банковский счёт / дебетовая карта
    case CREDIT = 'credit';       // Кредитная карта / кредит (может уйти в минус)
    case SAVINGS = 'savings';     // Накопительный счёт
    case INVESTMENT = 'investment'; // Инвестиционный счёт
}
