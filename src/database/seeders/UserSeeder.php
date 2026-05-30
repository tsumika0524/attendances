<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {

            User::create([
                'name' => 'ユーザー' . $i,
                'email' => 'user' . $i . '@test.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
