<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出勤ボタンが正しく機能する
     */
    public function test_出勤ボタンが正しく機能する()
    {
        Carbon::setTestNow('2026-05-10 09:00:00');

        $user = User::factory()->create();

        $this->actingAs($user);

        // 勤怠画面表示
        $response = $this->get('/attendance');

        // 出勤ボタン確認
        $response->assertSee('出勤');

        // 出勤処理
        $this->post('/attendance/clock-in');

        // 再表示
        $response = $this->get('/attendance');

        // ステータス確認
        $response->assertSee('出勤中');

        // DB確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => Attendance::STATUS_WORKING,
        ]);
    }

    /**
     * 出勤は一日一回のみ
     */
    public function test_出勤は一日一回のみできる()
    {
        Carbon::setTestNow('2026-05-10 18:00:00');

        $user = User::factory()->create();

        // 退勤済データ
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        // 出勤ボタン非表示
        $response->assertDontSee('出勤');
    }

    /**
     * 出勤時刻が勤怠一覧画面で確認できる
     */
    public function test_出勤時刻が勤怠一覧画面で確認できる()
    {
        Carbon::setTestNow('2026-05-10 09:30:00');

        $user = User::factory()->create();

        $this->actingAs($user);

        // 出勤処理
        $this->post('/attendance/clock-in');

        // 勤怠一覧
        $response = $this->get('/attendance/list');

        // 出勤時刻確認
        $response->assertSee('09:30');
    }
}