<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;

class AdminStampCorrectionController extends Controller
{
    public function show($id)
    {
        $requestData = StampCorrectionRequest::with('attendance.user')
            ->findOrFail($id);

        return view('admin.attendance.detail', [
            'requestData' => $requestData,
            'attendance'  => $requestData->attendance,
            'isAdmin'     => true,
        ]);
    }

    public function approve($id)
{
    $request = StampCorrectionRequest::with('attendance')->findOrFail($id);
    $attendance = $request->attendance;

    $clockIn  = Carbon::parse($request->target_date . ' ' . $request->start_time);
    $clockOut = Carbon::parse($request->target_date . ' ' . $request->end_time);

    // 出勤退勤チェック
    if ($clockIn->gte($clockOut)) {
        return back()->withErrors([
            'clock' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    // 休憩チェック（ここが重要）
    if (!empty($request->breaks)) {
        $breaks = is_string($request->breaks)
            ? json_decode($request->breaks, true)
            : $request->breaks;

        foreach ($breaks as $b) {

            if (empty($b['start']) || empty($b['end'])) {
                continue;
            }

            $bStart = Carbon::parse($request->target_date . ' ' . $b['start']);
            $bEnd   = Carbon::parse($request->target_date . ' ' . $b['end']);

            // ❌ 休憩が勤務外
            if ($bStart->lt($clockIn) || $bEnd->gt($clockOut)) {
                return back()->withErrors([
                    'break' => '休憩時間が不適切な値です'
                ]);
            }

            // ❌ 逆転
            if ($bStart->gte($bEnd)) {
                return back()->withErrors([
                    'break' => '休憩時間が不適切な値です'
                ]);
            }
        }
    }

    // OKなら更新
    $attendance->update([
        'clock_in'  => $clockIn,
        'clock_out' => $clockOut,
        'note'      => $request->reason,
    ]);

    $request->update([
        'status' => 'approved'
    ]);

    return redirect()->route('admin.request.list');
}
}