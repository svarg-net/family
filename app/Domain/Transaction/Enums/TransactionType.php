<?php

namespace App\Domain\Transaction\Enums;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';
}
