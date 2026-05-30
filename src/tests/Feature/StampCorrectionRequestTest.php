<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StampCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出勤 > 退勤
     */
    public function test_clock_in_after_clock_out_error()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($user);

        $response = $this->from("/attendance/detail/{$attendance->id}")
            ->post(route('stamp.request.store'), [
                'attendance_id' => $attendance->id,
                'target_date' => today()->toDateString(),
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'reason' => '修正理由',
            ]);

        $response->assertSessionHasErrors('clock_in');
    }

    /**
     * 休憩開始 > 退勤
     */
    public function test_break_start_after_clock_out_error()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($user);

        $response = $this->from("/attendance/detail/{$attendance->id}")
            ->post(route('stamp.request.store'), [
                'attendance_id' => $attendance->id,
                'target_date' => today()->toDateString(),
                'clock_in' => '09:00',
                'clock_out' => '18:00',

                'breaks' => [
                    [
                        'start' => '19:00',
                        'end' => '19:30',
                    ]
                ],

                'reason' => '修正理由',
            ]);

        $response->assertSessionHasErrors('breaks.0.start');
    }

    /**
     * 休憩終了 > 退勤
     */
    public function test_break_end_after_clock_out_error()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($user);

        $response = $this->from("/attendance/detail/{$attendance->id}")
            ->post(route('stamp.request.store'), [
                'attendance_id' => $attendance->id,
                'target_date' => today()->toDateString(),
                'clock_in' => '09:00',
                'clock_out' => '18:00',

                'breaks' => [
                    [
                        'start' => '17:00',
                        'end' => '19:00',
                    ]
                ],

                'reason' => '修正理由',
            ]);

        $response->assertSessionHasErrors('breaks.0.end');
    }

    /**
     * 備考未入力
     */
    public function test_reason_required()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($user);

        $response = $this->from("/attendance/detail/{$attendance->id}")
            ->post(route('stamp.request.store'), [
                'attendance_id' => $attendance->id,
                'target_date' => today()->toDateString(),
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'reason' => '',
            ]);

        $response->assertSessionHasErrors('reason');
    }

    /**
     * 修正申請作成
     */
    public function test_stamp_correction_request_created()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('stamp.request.store'), [
            'attendance_id' => $attendance->id,
            'target_date' => today()->toDateString(),
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'reason' => '電車遅延',
        ]);

        $response->assertRedirect(route('stamp.request.list'));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'reason' => '電車遅延',
            'status' => 'pending',
        ]);
    }

    /**
     * 承認待ち一覧
     */
    public function test_pending_request_visible()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => today(),
            'reason' => '修正申請',
            'status' => 'pending',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('stamp.request.list'));

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
    }

    /**
     * 承認済み一覧
     */
    public function test_approved_request_visible()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => today(),
            'reason' => '修正申請',
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('stamp.request.list', [
            'status' => 'approved'
        ]));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }
}