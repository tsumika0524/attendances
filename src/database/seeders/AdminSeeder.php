<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => '管理者',
            'email' => '123123@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => 1,
        ]);
    }
}