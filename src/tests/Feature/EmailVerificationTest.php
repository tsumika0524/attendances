<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_verify_email()
    {
        /*
        |--------------------------------------------------------------------------
        | 会員登録後、認証メール送信
        |--------------------------------------------------------------------------
        */

        Notification::fake();

        $user = User::factory()->unverified()->create([
            'name' => '山田 太郎',
            'email' => 'yamada@example.com',
        ]);

        // 認証メール送信
        $user->sendEmailVerificationNotification();

        // VerifyEmail通知確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        /*
        |--------------------------------------------------------------------------
        | メール認証誘導画面表示
        |--------------------------------------------------------------------------
        */

        $response = $this->actingAs($user)
            ->get('/email/verify');

        $response->assertStatus(200);

        // 「認証はこちらから」が表示されるか
        $response->assertSee('認証はこちらから');

        /*
        |--------------------------------------------------------------------------
        | メール認証完了
        |--------------------------------------------------------------------------
        */

        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)
            ->get($verificationUrl);

        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');

        // メール認証済み確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}