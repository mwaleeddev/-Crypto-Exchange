<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100000, // $100,000
        ]);

        // Give user some initial assets
        Asset::create([
            'user_id' => $user->id,
            'symbol' => 'BTC',
            'amount' => 1.5,
            'locked_amount' => 0,
        ]);

        Asset::create([
            'user_id' => $user->id,
            'symbol' => 'ETH',
            'amount' => 10,
            'locked_amount' => 0,
        ]);

        // Create another user
        $user2 = User::create([
            'name' => 'Trader 2',
            'email' => 'trader2@example.com',
            'password' => bcrypt('password'),
            'balance' => 50000,
        ]);

        Asset::create([
            'user_id' => $user2->id,
            'symbol' => 'BTC',
            'amount' => 0.5,
            'locked_amount' => 0,
        ]);
    }
}