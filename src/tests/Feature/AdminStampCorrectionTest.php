<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminStampCorrectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_manage_stamp_correction_requests()
    {
        /*
        |--------------------------------------------------------------------------
        | データ作成
        |--------------------------------------------------------------------------
        */

        // 管理者
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // 一般ユーザー
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        // 勤怠データ
        $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => '2026-05-15',
        'clock_in' => '2026-05-15 09:00:00',
        'clock_out' => '2026-05-15 18:00:00',
         ]);

        // 承認待ち申請
        $pendingRequest = StampCorrectionRequest::create([
        'user_id' => $user->id,
        'attendance_id' => $attendance->id,
        'target_date' => '2026-05-01',
        'reason' => '電車遅延',
        'start_time' => '10:00:00',
        'end_time'   => '19:00:00',
        'status' => 'pending',
         ]);

        $approvedRequest = StampCorrectionRequest::create([
        'user_id' => $user->id,
        'attendance_id' => $attendance->id,
        'target_date' => '2026-05-01',
         'reason' => '早出',
        'requested_clock_in' => '08:30:00',
        'requested_clock_out' => '17:30:00',
        'status' => 'approved',
         ]);

        /*
        |--------------------------------------------------------------------------
        | 承認待ち一覧表示
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list?status=pending');

        $response->assertStatus(200);

        $response->assertSee('山田 太郎');
        $response->assertSee('電車遅延');

        /*
        |--------------------------------------------------------------------------
        | 承認済み一覧表示
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);

        $response->assertSee('早出');

        /*
        |--------------------------------------------------------------------------
        | 修正申請詳細表示
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get("/admin/stamp_correction_request/approve/{$pendingRequest->id}");

        $response->assertStatus(200);

        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee('電車遅延');

        /*
        |--------------------------------------------------------------------------
        | 承認処理
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->post("/admin/stamp_correction_request/approve/{$pendingRequest->id}");

        $response->assertStatus(302);

        // DB更新確認
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $pendingRequest->id,
            'status' => 'approved',
        ]);

        // 勤怠更新確認
        $this->assertDatabaseHas('attendances', [
    'id' => $attendance->id,
    'clock_in' => now()->format('Y-m-d') . ' 10:00:00',
    'clock_out' => now()->format('Y-m-d') . ' 19:00:00',
]);
    }
}