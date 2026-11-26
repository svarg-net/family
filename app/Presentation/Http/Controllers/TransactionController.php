<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Transaction\Actions\CreateTransactionAction;
use App\Application\Transaction\Actions\DeleteTransactionAction;
use App\Application\Transaction\Actions\UpdateTransactionAction;
use App\Application\Transaction\DTOs\UpdateTransactionData;
use App\Application\Transaction\DTOs\CreateTransactionData;
use App\Domain\Transaction\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Enums\TransactionType;
use App\Domain\Transaction\ValueObjects\Money;
use App\Presentation\Http\Requests\CreateTransactionRequest;
use App\Presentation\Http\Requests\UpdateTransactionRequest;
use App\Presentation\Http\Resources\TransactionResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

final readonly class TransactionController
{
    public function __construct(
        private CreateTransactionAction        $createTransactionAction,
        private TransactionRepositoryInterface $transactionRepository,
        private DeleteTransactionAction        $deleteTransactionAction,
        private UpdateTransactionAction        $updateTransactionAction,
    )
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $type = $request->query('type');
        $perPage = (int)$request->query('per_page', 20);

        $perPage = max(1, min($perPage, 100));

        $transactions = $this->transactionRepository->getByUser(
            userId: $request->user()->id,
            type: $type,
            perPage: $perPage,
        );

        return TransactionResource::collection($transactions);
    }

    public function show(int $id, Request $request): JsonResource
    {
        $transaction = $this->transactionRepository->findById($id);

        if (!$transaction) {
            abort(404, 'Транзакция не найдена');
        }

        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Доступ запрещён');
        }

        return new TransactionResource($transaction);
    }

    public function store(CreateTransactionRequest $request): JsonResponse
    {
        try {
            $dto = new CreateTransactionData(
                title: $request->validated('title'),
                description: $request->validated('description'),
                amount: new Money(
                    (float)$request->validated('amount'),
                    $request->validated('currency')
                ),
                type: TransactionType::from($request->validated('type')),
                accountId: $request->validated('account_id'),
                transferToAccountId: $request->validated('transfer_to_account_id'),
                categoryId: $request->validated('category_id'),
                user: $request->user(),
                transactionDate: $request->validated('transaction_date')
                    ? new \DateTimeImmutable($request->validated('transaction_date'))
                    : null,
            );

            $transaction = $this->createTransactionAction->execute($dto);

            return response()->json(new TransactionResource($transaction), 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $this->deleteTransactionAction->execute($id, $request->user()->id);

            return response()->json(['message' => 'Транзакция удалена'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);

        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function update(int $id, UpdateTransactionRequest $request): JsonResponse
    {
        try {
            $dto = new UpdateTransactionData(
                title: $request->validated('title'),
                description: $request->validated('description'),
                amount: $request->validated('amount') !== null
                    ? new Money((float)$request->validated('amount'), $request->validated('currency', 'RUB'))
                    : null,
                type: $request->validated('type')
                    ? TransactionType::from($request->validated('type'))
                    : null,
                accountId: $request->validated('account_id'),
                transferToAccountId: $request->validated('transfer_to_account_id'),
                categoryId: $request->validated('category_id'),
                transactionDate: $request->validated('transaction_date')
                    ? new \DateTimeImmutable($request->validated('transaction_date'))
                    : null,
            );

            $transaction = $this->updateTransactionAction->execute($id, $dto, $request->user()->id);

            return response()->json(new TransactionResource($transaction), 200);

        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);

        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
