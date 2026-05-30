<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;

class AttendanceController extends Controller
{
    public function index()
    {   
       // 管理者はアクセス禁止
        if (auth()->user()->is_admin) {

        auth()->logout();

        return redirect('/login')->withErrors([
            'email' => '管理者はユーザー画面へログインできません'
        ]);
    }

    $now = Carbon::now();



        $now = Carbon::now();

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        $status = $attendance->status ?? Attendance::STATUS_OFF;

        $statusLabel = match ($status) {
            Attendance::STATUS_OFF => '勤務外',
            Attendance::STATUS_WORKING => '出勤中',
            Attendance::STATUS_BREAK => '休憩中',
            Attendance::STATUS_DONE => '退勤済',
        };

        $days = ['日', '月', '火', '水', '木', '金', '土'];
        $date = $now->format('Y年n月j日') . '(' . $days[$now->dayOfWeek] . ')';

        return view('attendance.index', [
            'date' => $date,
            'time' => $now->format('H:i'),
            'status' => $status,
            'statusLabel' => $statusLabel,
        ]);
    }

    public function clockIn()
    {
        // 1日1回制御
        $exists = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->exists();

        if ($exists) {
            return back();
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'work_date' => today(),
            'clock_in' => now(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        return back();
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance) return back();

        $attendance->update([
            'clock_out' => now(),
            'status' => Attendance::STATUS_DONE,
        ]);

        return back();
    }

    public function breakIn()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance || $attendance->status !== Attendance::STATUS_WORKING) {
            return back();
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        $attendance->update([
            'status' => Attendance::STATUS_BREAK,
        ]);

        return back();
    }

    public function breakOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance || $attendance->status !== Attendance::STATUS_BREAK) {
            return back();
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if ($break) {
            $break->update([
                'break_end' => now(),
            ]);
        }

        $attendance->update([
            'status' => Attendance::STATUS_WORKING,
        ]);

        return back();
    }

    public function list(Request $request)
{
    $currentMonth = $request->month ?? now()->format('Y-m');

    $start = Carbon::parse($currentMonth)->startOfMonth();
    $end = Carbon::parse($currentMonth)->endOfMonth();

    $attendances = Attendance::where('user_id', auth()->id())
        ->whereBetween('work_date', [$start, $end])
        ->get();

    return view('attendance.list', [
        'attendances' => $attendances,
        'currentMonth' => $currentMonth,
        'prevMonth' => $start->copy()->subMonth()->format('Y-m'),
        'nextMonth' => $start->copy()->addMonth()->format('Y-m'),
    ]);
}

    public function detail($id)
{
    $attendance = Attendance::with('breaks', 'user')->findOrFail($id);

    $isPending = StampCorrectionRequest::where('attendance_id', $attendance->id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending')
        ->exists();

    $isApproved = StampCorrectionRequest::where('attendance_id', $attendance->id)
        ->where('user_id', auth()->id())
        ->where('status', 'approved')
        ->exists();

    return view('attendance.detail', compact(
        'attendance',
        'isPending',
        'isApproved'
    ));
}
    public function show($id)
{
    $attendance = Attendance::with('breaks', 'user')->findOrFail($id);

    $isPending = StampCorrectionRequest::where('attendance_id', $attendance->id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending')
        ->exists();

    $isApproved = StampCorrectionRequest::where('attendance_id', $attendance->id)
        ->where('user_id', auth()->id())
        ->where('status', 'approved')
        ->exists();

    return view('attendance.detail', compact(
        'attendance',
        'isPending',
        'isApproved'
    ));
}
}