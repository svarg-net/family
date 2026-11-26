<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Account\Actions\DeleteAccountAction;
use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Presentation\Http\Resources\AccountResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

final readonly class AccountController
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private DeleteAccountAction $deleteAccountAction,
    )
    {
    }

    /**
     * GET /api/accounts
     * Список счетов пользователя (личные + семейные)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $this->accountRepository->getForUser(
            userId: $request->user()->id,
            familyId: $request->query('family_id') ? (int)$request->query('family_id') : null
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

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $this->deleteAccountAction->execute($id, $request->user()->id);

            return response()->json(['message' => 'Счёт удалён'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);

        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);

        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
