<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AttendanceDateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 現在日時が正しい形式で表示される()
    {
        Carbon::setTestNow(
            Carbon::create(2026, 5, 10, 9, 30, 0)
        );

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        // 日付確認
        $response->assertSee('2026年5月10日(日)');
    }
}