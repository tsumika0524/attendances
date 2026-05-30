<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 休憩入ボタン
     */
    public function test_break_in()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        // 休憩入ボタン表示
        $response = $this->get('/attendance');
        $response->assertSee('休憩入');

        // 休憩処理
        $this->post('/attendance/break-in');

        // ステータス確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    /**
     * 休憩は何回でもできる
     */
    public function test_break_can_repeat()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        // 休憩入
        $this->post('/attendance/break-in');

        // 休憩戻
        $this->post('/attendance/break-out');

        // 再度「休憩入」が表示
        $response = $this->get('/attendance');

        $response->assertSee('休憩入');
    }

    /**
     * 休憩戻ボタン
     */
    public function test_break_out()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'status' => Attendance::STATUS_BREAK,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subMinutes(30),
            'break_end' => null,
        ]);

        $this->actingAs($user);

        // 休憩戻ボタン表示
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');

        // 休憩戻処理
        $this->post('/attendance/break-out');

        // 出勤中へ戻る
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    /**
     * 休憩戻は何回でもできる
     */
    public function test_break_out_can_repeat()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        // 1回目
        $this->post('/attendance/break-in');
        $this->post('/attendance/break-out');

        // 2回目
        $this->post('/attendance/break-in');

        // 「休憩戻」表示
        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');
    }

    /**
     * 休憩時刻が一覧に表示される
     */
    public function test_break_time_visible_in_list()
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => today(),
        'clock_in' => now(),
        'status' => Attendance::STATUS_WORKING,
    ]);

    BreakTime::create([
        'attendance_id' => $attendance->id,
        'break_start' => now()->setTime(12, 0),
        'break_end' => now()->setTime(13, 0),
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance/list');

    // 休憩合計1時間
    $response->assertSee('01:00');
}
}