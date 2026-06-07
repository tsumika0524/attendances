<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 詳細画面に選択した勤怠情報が表示される
     */
    public function test_admin_can_view_attendance_detail()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-13',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        BreakTime::create([
         'attendance_id' => $attendance->id,
         'break_start' => '2026-05-13 12:00:00',
         'break_end' => '2026-05-13 13:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);

        $response->assertSee('山田太郎');
        $response->assertSee('2026年');
        $response->assertSee('5月13日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    /**
     * 出勤時間 > 退勤時間
     */
    public function test_validation_error_when_clock_in_is_after_clock_out()
{
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $attendance = Attendance::factory()->create();

    $response = $this->actingAs($admin)
        ->from(route('admin.attendance.detail', $attendance->id))
        ->post(route('admin.attendance.update', $attendance->id), [

            'clock_in' => '19:00',
            'clock_out' => '18:00',

            'note' => '修正理由',
        ]);

    $response->assertSessionHasErrors([
        'clock_in',
    ]);
}

    /**
     * 休憩開始 > 退勤時間
     */
   public function test_validation_error_when_break_start_is_after_clock_out()
{
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $attendance = Attendance::factory()->create();

    $response = $this->actingAs($admin)
        ->from(route('admin.attendance.detail', $attendance->id))
        ->post(route('admin.attendance.update', $attendance->id), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',

            'breaks' => [
                [
                    'start' => '19:00',
                    'end' => '19:30',
                ]
            ],

            'note' => '修正理由',
        ]);

    $response->assertSessionHasErrors([
        'breaks.0.end',
    ]);
}

    /**
     * 備考未入力
     */
    public function test_validation_error_when_note_is_empty()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $attendance = Attendance::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.attendance.detail', $attendance->id))
            ->post(route('admin.attendance.update', $attendance->id), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => '',
            ]);

        $response->assertSessionHasErrors([
            'note',
        ]);
    }
}