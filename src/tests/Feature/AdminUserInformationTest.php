<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserInformationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_user_information_and_attendance()
    {
        // 管理者ユーザー作成
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // 一般ユーザー作成
        $user = User::factory()->create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
        ]);

        // 勤怠データ作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-05-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        /*
        |--------------------------------------------------------------------------
        | スタッフ一覧画面
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get('/admin/staff/list');

        $response->assertStatus(200);

        // 氏名・メールアドレス表示確認
        $response->assertSee('山田 太郎');
        $response->assertSee('yamada@example.com');

        /*
        |--------------------------------------------------------------------------
        | ユーザー勤怠一覧画面
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}");

        $response->assertStatus(200);

        // 勤怠情報確認
        // 勤怠情報確認
        $response->assertSee('05/01');
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        /*
        |--------------------------------------------------------------------------
        | 前月表示確認
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}?month=2026-04");

        $response->assertStatus(200);

        $response->assertSee('2026/04');

        /*
        |--------------------------------------------------------------------------
        | 翌月表示確認
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}?month=2026-06");
            

        $response->assertStatus(200);

        $response->assertSee('2026/06');

        /*
        |--------------------------------------------------------------------------
        | 勤怠詳細画面確認
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($admin)
            ->get("/admin/attendance/{$attendance->id}");

        $response->assertStatus(200);
    }
}