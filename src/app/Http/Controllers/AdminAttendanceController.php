<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AdminAttendanceController extends Controller
{
   public function index(Request $request)
{
    $currentDate = $request->date ?? now()->format('Y-m-d');
    $carbon = Carbon::parse($currentDate);

    $prevDate = $carbon->copy()->subDay()->format('Y-m-d');
    $nextDate = $carbon->copy()->addDay()->format('Y-m-d');

    $attendances = Attendance::with('user', 'breaks')
    ->whereDate('work_date', $carbon->format('Y-m-d'))
    ->get();

    return view('admin.list', compact(
    'attendances',
    'currentDate',
    'prevDate',
    'nextDate'
));
}

public function detail($id)
{
    $attendance = Attendance::with([
        'user',
        'breaks',
        'correctionRequest'
    ])->findOrFail($id);

    return view('admin.attendance.show', compact('attendance'));
}
public function staffAttendance(Request $request, $id)
{
    $user = User::findOrFail($id);

    // 現在月
    $currentMonth = $request->month ?? now()->format('Y-m');

    $start = Carbon::parse($currentMonth)->startOfMonth();
    $end = Carbon::parse($currentMonth)->endOfMonth();

    // 勤怠取得
    $attendances = Attendance::with('breaks')
        ->where('user_id', $id)
        ->whereBetween('work_date', [$start, $end])
        ->get();

    // 前月・翌月
    $prevMonth = $start->copy()->subMonth()->format('Y-m');
    $nextMonth = $start->copy()->addMonth()->format('Y-m');

    return view('admin.staff.attendance', compact(
        'user',
        'attendances',
        'currentMonth',
        'prevMonth',
        'nextMonth'
    ));
}

public function update(AdminAttendanceUpdateRequest $request, $id)
{
    $attendance = Attendance::with('breaks')->findOrFail($id);

    $attendance->update([
        'clock_in'  => Carbon::parse($request->clock_in),
        'clock_out' => Carbon::parse($request->clock_out),
        'note'      => $request->note,
    ]);

    // 休憩更新
    foreach ($request->breaks as $index => $breakData) {

        $break = $attendance->breaks[$index] ?? null;

        if (!$break) continue;

        if (empty($breakData['start']) && empty($breakData['end'])) continue;

        $break->update([
            'break_start' => $attendance->work_date . ' ' . $breakData['start'],
            'break_end'   => $attendance->work_date . ' ' . $breakData['end'],
        ]);
    }

    return redirect()->route('admin.staff.attendance', [
        'id' => $attendance->user_id
    ])->with('success', '修正しました');
}

    public function csv(Request $request, $id)
{
    $user = User::findOrFail($id);

    $month = $request->month ?? now()->format('Y-m');

    $start = Carbon::parse($month)->startOfMonth();
    $end = Carbon::parse($month)->endOfMonth();

    $attendances = Attendance::with('breaks')
        ->where('user_id', $id)
        ->whereBetween('work_date', [$start, $end])
        ->get();

    $fileName = "attendance_{$user->id}_{$month}.csv";

    $response = new StreamedResponse(function () use ($attendances, $user) {

        $handle = fopen('php://output', 'w');

        // ヘッダー（Shift-JIS）
        fputcsv($handle, array_map(function ($v) {
            return mb_convert_encoding($v, 'SJIS-win', 'UTF-8');
        }, [
            '名前',
            '日付',
            '出勤',
            '退勤',
            '休憩時間',
            '備考',
        ]));

        foreach ($attendances as $attendance) {

            // 休憩合計（分）
            $breakMinutes = $attendance->breaks->sum(function ($b) {
                if ($b->break_start && $b->break_end) {
                    return Carbon::parse($b->break_start)
                        ->diffInMinutes(Carbon::parse($b->break_end));
                }
                return 0;
            });

            // hh:mm形式へ変換
            $breakTime = sprintf('%02d:%02d',
                floor($breakMinutes / 60),
                $breakMinutes % 60
            );

            $row = [
                $user->name,
                $attendance->work_date,
                optional($attendance->clock_in)->format('H:i'),
                optional($attendance->clock_out)->format('H:i'),
                $breakTime, // ←統一
                $attendance->note,
            ];

            fputcsv($handle, array_map(function ($v) {
                return mb_convert_encoding($v, 'SJIS-win', 'UTF-8');
            }, $row));
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=Shift-JIS');
    $response->headers->set(
        'Content-Disposition',
        "attachment; filename={$fileName}"
    );

    return $response;
}
}