<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;
use App\Http\Controllers\AdminStaffController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// =========================
// 認証系
// =========================

// 管理者ログイン
Route::get('/admin/login', fn() => view('admin.login'))
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login']);


// ユーザーログイン
Route::get('/login', fn() => view('auth.login'))
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);


// ログアウト
Route::post('/logout', function () {

    $isAdmin = auth()->check() && auth()->user()->is_admin;

    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    // 管理者なら管理者ログインへ
    if ($isAdmin) {
        return redirect('/admin/login');
    }

    // 一般ユーザー
    return redirect('/login');

})->name('logout');


// 会員登録
Route::get('/register', fn() => view('auth.register'))
    ->name('register');

Route::post('/register', [RegisterController::class, 'register']);



// =========================
// ユーザー画面
// =========================

Route::middleware(['auth', 'verified', 'user.only'])->group(function () {

    // 勤怠トップ
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    // 出勤
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendance.clockIn');

    // 休憩入
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn'])
        ->name('attendance.breakIn');

    // 休憩戻
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut'])
        ->name('attendance.breakOut');

    // 退勤
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendance.clockOut');

    // 勤怠一覧
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    // 勤怠詳細
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');


    // 修正申請
    Route::post('/stamp_correction_request', [StampCorrectionRequestController::class, 'store'])
        ->name('stamp.request.store');
});



// =========================
// 管理者画面
// =========================

Route::middleware(['auth', 'admin.only'])
    ->prefix('admin')
    ->group(function () {

    // 勤怠一覧
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendance.list');

    // 勤怠詳細
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'detail'])
        ->name('admin.attendance.detail');

    // スタッフ一覧
    Route::get('/staff/list', [AdminStaffController::class, 'index'])
        ->name('admin.staff.list');

    // スタッフ別勤怠一覧
    Route::get(
        '/attendance/staff/{id}',
        [AdminAttendanceController::class, 'staffAttendance']
    )->name('admin.staff.attendance');

    Route::get('/admin/staff/{id}/csv', [AdminAttendanceController::class, 'csv'])
    ->name('admin.staff.csv');
});


// =========================
// 管理者 修正申請
// =========================

Route::middleware(['auth', 'admin.only'])
    ->prefix('admin')
    ->group(function () {

    // 修正申請一覧
    Route::get(
        '/stamp_correction_request/list',
        [AdminStampCorrectionRequestController::class, 'index']
    )->name('admin.request.list');

    // 修正申請詳細
    Route::get(
        '/stamp_correction_request/approve/{id}',
        [AdminStampCorrectionRequestController::class, 'show']
    )->name('admin.stamp.request.show');

    // 承認処理
    Route::post(
        '/stamp_correction_request/approve/{id}',
        [AdminStampCorrectionRequestController::class, 'approve']
    )->name('admin.stamp.request.approve');

    Route::post(
    '/attendance/{id}/update',
    [AdminAttendanceController::class, 'update']
    )->name('admin.attendance.update');
});

// =========================
// 修正申請一覧（共通）
// =========================

Route::get(
    '/stamp_correction_request/list',
    [StampCorrectionRequestController::class, 'index']
)
->middleware('auth')
->name('stamp.request.list');

// =========================
// メール認証
// =========================

// 認証案内画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


// 認証リンククリック時
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

    $request->fulfill();

    return redirect('/attendance');

})->middleware(['auth', 'signed'])->name('verification.verify');


// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {

    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送しました');

})->middleware(['auth', 'throttle:6,1'])->name('verification.send');