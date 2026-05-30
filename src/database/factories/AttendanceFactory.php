<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
            'clock_out' => now()->addHours(8),
            'status' => 1,
        ];
    }
}