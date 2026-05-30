<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    protected $fillable = [
    'user_id',
    'attendance_id',
    'target_date',
    'start_time',
    'end_time',
    'breaks',
    'reason',
    'status',
];

    protected $casts = [
        'breaks' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 👇 これ追加（超重要）
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
