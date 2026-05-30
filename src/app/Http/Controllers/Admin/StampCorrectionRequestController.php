<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = StampCorrectionRequest::with('attendance.user');

        // statusは明示的に2択のみ許可
        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        } else {
            $status = 'pending';
            $query->where('status', 'pending');
        }

        $requests = $query->latest()->get();

        return view('admin.request.list', compact('requests', 'status'));
    }

    public function show($id)
    {
        $requestData = StampCorrectionRequest::with('attendance.user')
            ->findOrFail($id);

        return view('admin.attendance.detail', [
            'requestData' => $requestData,
            'attendance'  => $requestData->attendance,
        ]);
    }

    public function approve($id)
    {
        $request = StampCorrectionRequest::with('attendance')->findOrFail($id);

        $attendance = $request->attendance;

        DB::transaction(function () use ($request, $attendance) {

            $attendance->update([
                'clock_in'  => $request->start_time,
                'clock_out' => $request->end_time,
                'note'      => $request->reason,
            ]);

            $request->update([
                'status' => 'approved',
            ]);
        });

        return redirect()
            ->route('admin.request.list', ['status' => 'pending'])
            ->with('success', '承認しました');
    }
}