<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 一般ユーザーのみ
        $users = User::where('is_admin', 0)->get();

        foreach ($users as $user) {

            // 5日分の勤怠
            for ($i = 1; $i <= 5; $i++) {

                $date = Carbon::now()->subDays($i);

                // 勤怠作成
                $attendance = Attendance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'work_date' => $date->format('Y-m-d'),
                    ],
                    [
                        'clock_in' => $date->copy()->setTime(9, 0),
                        'clock_out' => $date->copy()->setTime(18, 0),
                        'status' => 3,
                        'note' => 'ダミーデータ',
                    ]
                );

                // 休憩作成
                BreakTime::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                    ],
                    [
                        'break_start' => $date->copy()->setTime(12, 0),
                        'break_end' => $date->copy()->setTime(13, 0),
                    ]
                );
            }
        }
    }
}