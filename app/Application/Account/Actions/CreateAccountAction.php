<?php

declare(strict_types=1);

namespace App\Application\Account\Actions;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Models\Account;

final readonly class CreateAccountAction
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ) {}

    public function execute(array $data): Account
    {
        return $this->accountRepository->create($data);
    }
}
