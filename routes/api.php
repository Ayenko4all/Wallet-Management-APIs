<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\AuthTokenMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([AuthTokenMiddleware::class])->group(function () {
    // Wallet Management Routes
    Route::prefix('wallets')->group(function () {
        Route::post('/', [WalletController::class, 'store']);
        Route::get('/{wallet}', [WalletController::class, 'show']);
        Route::post('/fund', [WalletController::class, 'fundWallet']);
        Route::post('/withdraw', [WalletController::class, 'withdrawFromWallet']);
        Route::delete('/{wallet}', [WalletController::class, 'deactivateWallet']);
    });

    // Transfer Routes
    Route::prefix('transactions')->group(function () {
        Route::post('/', [TransactionController::class, 'store']);
        Route::get('/{transactionEntry}', [TransactionController::class, 'show']);
        Route::get('/wallet/{wallet}/history', [TransactionController::class, 'walletTransactions']);
    });
});
