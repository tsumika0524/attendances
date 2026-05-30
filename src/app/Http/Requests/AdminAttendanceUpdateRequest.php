<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['required'],
            'clock_out' => ['required'],
            'note' => ['required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $date = now()->format('Y-m-d');

            $in = $this->clock_in
                ? Carbon::parse($date . ' ' . $this->clock_in)
                : null;

            $out = $this->clock_out
                ? Carbon::parse($date . ' ' . $this->clock_out)
                : null;

            // 出勤・退勤チェック
            if ($in && $out && $in->gt($out)) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // 休憩データ取得（ここが重要）
            $breaks = $this->input('breaks', []);

foreach ($breaks as $i => $break) {

    if (empty($break['start']) || empty($break['end'])) {
        continue;
    }

    $start = Carbon::parse($date . ' ' . $break['start']);
    $end   = Carbon::parse($date . ' ' . $break['end']);

    // ❌ 逆転チェック
    if ($start->gte($end)) {
        $validator->errors()->add(
            "breaks.$i.start",
            '休憩時間が不適切です'
        );
    }

    // ❌ 勤務開始より前
    if ($start->lt($in)) {
        $validator->errors()->add(
            "breaks.$i.start",
            '休憩時間が不適切です'
        );
    }

    // ❌ 勤務終了より後
    if ($end->gt($out)) {
        $validator->errors()->add(
            "breaks.$i.end",
            '休憩時間もしくは退勤時間が不適切です'
        );
    }
}

        });
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'note.required' => '備考を記入してください',
            'note.string' => '備考は文字で入力してください',
        ];
    }
}