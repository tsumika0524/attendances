<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\StoreStampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
{
    $status = $request->get('status', 'pending');

    // 👇 ベースクエリ
    $query = StampCorrectionRequest::with('user');

    // 👇 管理者じゃない場合だけ絞る
    if (!auth()->user()->is_admin) {
        $query->where('user_id', auth()->id());
    }

    // ステータス絞り込み
    $status = $request->get('status');

    $query = StampCorrectionRequest::with('user');

    if ($status === 'pending') {
    $query->where('status', 'pending');
    } elseif ($status === 'approved') {
    $query->where('status', 'approved');
    } else {
    $query->where('status', 'pending'); // デフォルト
    }

    $requests = $query->latest()->get();

    // 👇 表示切り替え
    if (auth()->user()->is_admin) {
        return view('admin.request.list', compact('requests', 'status'));
    } else {
        return view('stamp_correction_request.list', compact('requests', 'status'));
    }
}
    public function store(StoreStampCorrectionRequest $request)
{
    

    StampCorrectionRequest::create([
        'user_id' => auth()->id(),
        'attendance_id' => $request->attendance_id,
        'target_date' => $request->target_date,

        // Bladeに合わせてる場合
        'start_time' => $request->clock_in,
        'end_time'   => $request->clock_out,

        'breaks' => $request->breaks, // castしてる前提
        'reason' => $request->reason,

        'status' => 'pending',
    ]);

    return redirect()->route('stamp.request.list')
        ->with('success', '申請を送信しました');
}
}