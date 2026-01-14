<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create 3 users
        User::factory()->count(3)->create();

        //create an admin user
        User::factory()->create(['role' => 'admin', 'name' => 'Admin User', 'email' => 'test@admin.gmail']);
    }
}
