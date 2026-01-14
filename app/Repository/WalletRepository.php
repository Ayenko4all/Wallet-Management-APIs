<?php

namespace App\Repository;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function create(array $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            return Wallet::create($data);
        });
    }

    public function findById(int $id): ?Wallet
    {
        return Wallet::find($id);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }

    public function findByWalletNumber(string $walletNumber): ?Wallet
    {
        return Wallet::where('wallet_number', $walletNumber)->first();
    }

    public function delete(Wallet $wallet): bool
    {
        return DB::transaction(function () use ($wallet) {
            return $wallet->delete();
        });
    }
}
