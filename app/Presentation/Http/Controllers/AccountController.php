<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Presentation\Http\Resources\AccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

final readonly class AccountController
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ) {}

    /**
     * GET /api/accounts
     * Список счетов пользователя (личные + семейные)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $this->accountRepository->getForUser(
            userId: $request->user()->id,
            familyId: $request->query('family_id') ? (int) $request->query('family_id') : null
        );

        return AccountResource::collection($accounts);
    }

    /**
     * GET /api/accounts/{id}
     * Один счёт с балансом и последними транзакциями
     */
    public function show(int $id, Request $request): JsonResource
    {
        $account = $this->accountRepository->findById($id);

        if (!$account) {
            abort(404, 'Счёт не найден');
        }

        // Проверяем, что счёт принадлежит пользователю
        if ($account->user_id !== $request->user()->id) {
            abort(403, 'Доступ запрещён');
        }

        return new AccountResource($account);
    }
}
