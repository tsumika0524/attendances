<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理者がその日の全ユーザー勤怠を確認できる
     */
    public function test_admin_can_view_today_attendance_list()
    {
        Carbon::setTestNow('2026-05-13');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user1 = User::factory()->create([
            'name' => '山田太郎',
        ]);

        $user2 = User::factory()->create([
            'name' => '佐藤花子',
        ]);

        Attendance::create([
            'user_id' => $user1->id,
            'work_date' => '2026-05-13',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'work_date' => '2026-05-13',
            'clock_in' => '10:00:00',
            'clock_out' => '19:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        $response->assertSee('山田太郎');
        $response->assertSee('佐藤花子');

        $response->assertSee('09:00');
        $response->assertSee('18:00');

        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    /**
     * 現在の日付が表示される
     */
    public function test_current_date_is_displayed()
    {
        Carbon::setTestNow('2026-05-13');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        $response->assertSee('2026/05/13');
    }

    /**
     * 前日ボタンで前日の勤怠が表示される
     */
    public function test_previous_day_attendance_is_displayed()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-12',
            'clock_in' => '08:00:00',
            'clock_out' => '17:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.list', [
                'date' => '2026-05-12',
            ]));

        $response->assertStatus(200);

        $response->assertSee('2026/05/12');
        $response->assertSee('山田太郎');
        $response->assertSee('08:00');
        $response->assertSee('17:00');
    }

    /**
     * 翌日ボタンで翌日の勤怠が表示される
     */
    public function test_next_day_attendance_is_displayed()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => '佐藤花子',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-14',
            'clock_in' => '11:00:00',
            'clock_out' => '20:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.list', [
                'date' => '2026-05-14',
            ]));

        $response->assertStatus(200);

        $response->assertSee('2026/05/14');
        $response->assertSee('佐藤花子');
        $response->assertSee('11:00');
        $response->assertSee('20:00');
    }
}