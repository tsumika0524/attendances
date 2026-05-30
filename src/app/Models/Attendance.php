<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date', // ←追加（重要）
        'clock_in',
        'clock_out',
        'status',
    ];

     protected $casts = [
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
    ];

    // ステータス定数
    const STATUS_OFF = 0;
    const STATUS_WORKING = 1;
    const STATUS_BREAK = 2;
    const STATUS_DONE = 3;

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 休憩
    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }

    public function correctionRequest()
{
    return $this->hasOne(\App\Models\StampCorrectionRequest::class, 'attendance_id');
}
}