<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Account\Actions\CreateAccountAction;
use App\Application\Account\Actions\DeleteAccountAction;
use App\Application\Account\Actions\UpdateAccountAction;
use App\Domain\Account\Contracts\AccountRepositoryInterface;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Presentation\Http\Requests\CreateAccountRequest;
use App\Presentation\Http\Requests\UpdateAccountRequest;
use App\Presentation\Http\Resources\AccountResource;
use App\Presentation\Http\Resources\TransactionResource;
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
        private AccountRepositoryInterface     $accountRepository,
        private DeleteAccountAction            $deleteAccountAction,
        private UpdateAccountAction            $updateAccountAction,
        private CreateAccountAction            $createAccountAction,
        private TransactionRepositoryInterface $transactionRepository,
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

    public function update(int $id, UpdateAccountRequest $request): JsonResponse
    {
        try {
            $account = $this->updateAccountAction->execute(
                $id,
                $request->validated(),
                $request->user()->id
            );

            return response()->json(new AccountResource($account), 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);

        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);

        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function store(CreateAccountRequest $request): JsonResponse
    {
        $account = $this->createAccountAction->execute([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'type' => $request->validated('type'),
            'currency' => $request->validated('currency'),
            'balance' => $request->validated('balance', 0),
            'visibility' => $request->validated('visibility'),
            'user_id' => $request->user()->id,
            'family_id' => $request->validated('family_id'),
            'category_id' => $request->validated('category_id'),
        ]);

        return response()->json(new AccountResource($account), 201);
    }

    public function transactions(int $id, Request $request): AnonymousResourceCollection
    {
        $account = $this->accountRepository->findById($id);

        if (!$account) {
            abort(404, 'Счёт не найден');
        }

        // Проверяем, что счёт принадлежит пользователю
        if ($account->user_id !== $request->user()->id) {
            abort(403, 'Доступ запрещён');
        }

        $type = $request->query('type');
        $perPage = max(1, min((int)$request->query('per_page', 20), 100));

        $validTypes = ['income', 'expense', 'transfer', null];
        if (!in_array($type, $validTypes, true)) {
            abort(400, 'Недопустимый тип транзакции.');
        }

        $transactions = $this->transactionRepository->getByAccount($account->id, $type ?? 'all');

        // Для пагинации используем коллекцию
        $paginated = $transactions->forPage(
            $request->query('page', 1),
            $perPage
        );

        return TransactionResource::collection($paginated);
    }
}
