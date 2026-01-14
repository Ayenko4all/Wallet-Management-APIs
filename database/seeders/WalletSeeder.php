<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $walletExists = Wallet::where('user_id', $user->id)->exists();
            if (!$walletExists) {
                $walletNo = 'WAL' . str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
                Wallet::factory()->create([
                    'user_id' => $user->id,
                    'wallet_type' => $user->role === 'admin' ? 'internal' : 'general',
                    'wallet_number' => $user->role === 'admin' ? config('wallets.ledger_wallet_number') : $walletNo,
                ]);
            }
        }


    }
}
