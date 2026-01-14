<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = Wallet::where('wallet_type', 'general')->get();
        if ($wallets->count() < 2) {
            return;
        }
        $wallet1 = $wallets[0];
        $wallet2 = $wallets[1];

        TransactionEntry::factory()->create([
            'wallet_id' => $wallet1->id,
            'type' => 'debit',
            'amount' => 1000,
            'description' => 'Transfer to ' . $wallet2->wallet_number,
            'status' => 'success',
        ]);

        TransactionEntry::factory()->create([
            'wallet_id' => $wallet2->id,
            'type' => 'credit',
            'amount' => 1000,
            'description' => 'Transfer from ' . $wallet1->wallet_number,
            'status' => 'success',
        ]);

        //create transaction group id for the above two transactions
        Transaction::create([
            'debit_id' => TransactionEntry::where('wallet_id', $wallet1->id)->latest()->first()->id,
            'credit_id' => TransactionEntry::where('wallet_id', $wallet2->id)->latest()->first()->id,
        ]);
        //update the above wallet balances
        $wallet1->decrement('balance', 1000);
        $wallet2->increment('balance', 1000);


        if ($wallets->count() >= 3) {
            $wallet3 = $wallets[2];

            TransactionEntry::factory()->create([
                'wallet_id' => $wallet3->id,
                'type' => 'debit',
                'amount' => 500,
                'description' => 'Transfer to ' . $wallet1->wallet_number,
                'status' => 'success',
            ]);

            TransactionEntry::factory()->create([
                'wallet_id' => $wallet1->id,
                'type' => 'credit',
                'amount' => 500,
                'description' => 'Transfer from ' . $wallet3->wallet_number,
                'status' => 'success',
            ]);

            //create transaction group id for the above two transactions
            Transaction::create([
                'debit_id' => TransactionEntry::where('wallet_id', $wallet3->id)->latest()->first()->id,
                'credit_id' => TransactionEntry::where('wallet_id', $wallet1->id)->latest()->first()->id,
            ]);

            //update the above wallet balances
            $wallet3->decrement('balance', 500);
            $wallet1->increment('balance', 500);
        }
    }
}
