<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'wallet_number' => 'WAL' . str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT),
            'balance' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => 'NGN',
            'status' => 'active',
        ];
    }
}
