<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 自分の勤怠情報が全て表示される
     */
    public function test_attendance_list_visible()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-01',
            'clock_in' => '2026-05-01 09:00:00',
            'clock_out' => '2026-05-01 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-02',
            'clock_in' => '2026-05-02 10:00:00',
            'clock_out' => '2026-05-02 19:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-05');

        $response->assertSee('05/01');
        $response->assertSee('05/02');
    }

    /**
     * 現在月が表示される
     */
    public function test_current_month_visible()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee(now()->format('Y/m'));
    }

    /**
     * 前月表示
     */
    public function test_previous_month_visible()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-04-10',
            'clock_in' => '2026-04-10 09:00:00',
            'clock_out' => '2026-04-10 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-04');

        $response->assertSee('2026/04');
        $response->assertSee('04/10');
    }

    /**
     * 翌月表示
     */
    public function test_next_month_visible()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-06-10',
            'clock_in' => '2026-06-10 09:00:00',
            'clock_out' => '2026-06-10 18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-06');

        $response->assertSee('2026/06');
        $response->assertSee('06/10');
    }

    /**
     * 詳細画面へ遷移
     */
    public function test_detail_link()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => now()->addHours(8),
            'status' => Attendance::STATUS_DONE,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee(
            "/attendance/detail/{$attendance->id}",
            false
        );
    }
}