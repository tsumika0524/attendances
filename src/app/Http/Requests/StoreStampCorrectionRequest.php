<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'attendance_id' => ['required', 'exists:attendances,id'],
            'target_date'   => ['required', 'date'],

            'clock_in'  => ['required'],
            'clock_out' => ['required'],

            'breaks' => ['array'],
            'breaks.*.start' => ['nullable'],
            'breaks.*.end'   => ['nullable'],

            'reason' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = strtotime($this->clock_in);
            $clockOut = strtotime($this->clock_out);

            // 出勤・退勤チェック
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩チェック
            if ($this->breaks) {
                foreach ($this->breaks as $i => $break) {

                    if (empty($break['start']) || empty($break['end'])) {
                        continue;
                    }

                    $bStart = strtotime($break['start']);
                    $bEnd   = strtotime($break['end']);

                    // ① 出勤前 or 退勤後
                    if ($bStart < $clockIn || $bStart > $clockOut) {
                        $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                    }

                    // ② 休憩終了が退勤後
                    if ($bEnd > $clockOut) {
                        $validator->errors()->add("breaks.$i.end", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            // 必須系（必要なら追加）
            'clock_in.required'  => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',

            // 要件指定（これ絶対一致）
            'reason.required' => '備考を記入してください',
        ];
    }
}