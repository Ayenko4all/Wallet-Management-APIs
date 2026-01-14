<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Requests\CreateTransactionRequest;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    /**
     * Create a new transaction
     */
    public function store(CreateTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => class_basename($e),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get transaction details
     */
    public function show(string $transactionId): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransaction($transactionId);

            return response()->json([
                'success' => true,
                'data' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * Get wallet transaction history
     */
    public function walletTransactions(string $walletId): JsonResponse
    {
        try {
            $history = $this->transactionService->getWalletHistory($walletId, request()->all());

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
