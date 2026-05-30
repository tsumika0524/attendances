<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 退勤ボタンが正しく機能する
     */
    public function test_clock_out()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        // 退勤ボタン表示確認
        $response = $this->get('/attendance');

        $response->assertSee('退勤');

        // 退勤処理
        $this->post('/attendance/clock-out');

        // ステータス確認
        $response = $this->get('/attendance');

        $response->assertSee('退勤済');
    }

    /**
     * 退勤時刻が一覧画面で確認できる
     */
    public function test_clock_out_time_visible_in_list()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->setTime(9, 0),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        // 退勤処理
        $this->post('/attendance/clock-out');

        // DB再取得
        $attendance->refresh();

        $response = $this->get('/attendance/list');

        // 退勤時刻表示確認
        $response->assertSee(
            $attendance->clock_out->format('H:i')
        );
    }
}