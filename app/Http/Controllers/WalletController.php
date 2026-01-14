<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWalletRequest;
use App\Http\Requests\FundWalletRequest;
use App\Http\Requests\WithdrawRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    public function store(createWalletRequest $request): JsonResponse
    {
        try {
            $wallet = $this->walletService->createWallet($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Wallet created successfully',
                'data' => $wallet,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get wallet details
     */
    public function show(int|string $walletId): JsonResponse
    {
        try {
            $wallet = $this->walletService->getWalletDetail($walletId);

            return response()->json([
                'success' => true,
                'data' => $wallet,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    public function fundWallet(FundWalletRequest $request): JsonResponse
    {
        try {
            $result = $this->walletService->fundWallet($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Wallet funded successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function withdrawFromWallet(WithdrawRequest $request): JsonResponse
    {
        try {
            $result = $this->walletService->withdrawFromWallet($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal successful',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function deactivateWallet(int|string $walletId): JsonResponse
    {
        try {
            $this->walletService->deleteWallet($walletId);

            return response()->json([
                'success' => true,
                'message' => 'Wallet deactivated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
