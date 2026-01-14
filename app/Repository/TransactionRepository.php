<?php
namespace App\Repository;

use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    public function createTransactionEntry(array $data): TransactionEntry
    {
        return DB::transaction(function () use ($data) {
            return TransactionEntry::create($data);
        });
    }

    public function findTransactionEntryById(int $id): ?TransactionEntry
    {
        return TransactionEntry::find($id);
    }

    public function updateWalletBalances(Wallet $wallet, float $amount, string $type): void
    {
        DB::transaction(function () use ($wallet, $amount, $type) {
            if ($type === 'debit') {
                $wallet->balance -= $amount;
            } elseif ($type === 'credit') {
                $wallet->balance += $amount;
            }

            $wallet->save();
        });
    }

    public function createTransaction(array $data)
    {
        // Implementation for creating a transaction group
        return Transaction::create([
            'debit_id' => $data['debitEntry_id'],
            'credit_id' => $data['creditEntry_id'],
            'reversible' => $data['reversible'] ?? false,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }
}
