<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreStampCorrectionRequest;

class StampCorrectionController extends Controller
{
    public function store(StoreStampCorrectionRequest $request)
{
    StampCorrectionRequest::create([
    'user_id' => auth()->id(),
    'attendance_id' => $request->attendance_id,
    'target_date' => $request->target_date,
    'start_time' => $request->clock_in,
    'end_time'   => $request->clock_out,
    'breaks'     => json_encode($request->breaks),
    'reason' => $request->reason,
    'status'     => 'pending',
]);

    return redirect()->route('stamp.request.list');
}//
}
