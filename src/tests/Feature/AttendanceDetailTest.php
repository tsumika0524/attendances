<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前表示確認
     */
    public function test_user_name_visible()
{
    $user = User::factory()->create([
        'name' => '山田 太郎',
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
        'status' => Attendance::STATUS_DONE,
    ]);

    $this->actingAs($user);

    $response = $this->get("/attendance/detail/{$attendance->id}");

    $response->assertStatus(200);

    // HTML取得
    $content = $response->getContent();

    // 空白・改行除去
    $content = preg_replace('/\s+/', '', $content);

    // 名前確認
    $this->assertStringContainsString('山田太郎', $content);
}

    /**
     * 日付表示確認
     */
    public function test_work_date_visible()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-10',
            'clock_in' => '2026-05-10 09:00:00',
            'clock_out' => '2026-05-10 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('2026');
        $response->assertSee('05');
        $response->assertSee('10');
    }

    /**
     * 出勤・退勤時刻表示確認
     */
    public function test_clock_in_out_visible()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-10',
            'clock_in' => '2026-05-10 09:00:00',
            'clock_out' => '2026-05-10 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 休憩時間表示確認
     */
    public function test_break_time_visible()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-10',
            'clock_in' => '2026-05-10 09:00:00',
            'clock_out' => '2026-05-10 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '2026-05-10 12:00:00',
            'break_end' => '2026-05-10 13:00:00',
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}