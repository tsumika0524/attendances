@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endpush

@section('title', '勤怠一覧')

@section('header-nav')
    <a href="/attendance">勤怠</a>
    <a href="/attendance/list">勤怠一覧</a>
    <a href="{{ route('stamp.request.list') }}">申請</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>

@endsection

@section('content')

<div class="list-container">

    <h2 class="list-title">
        勤怠一覧
    </h2>

    {{-- 月ナビ --}}
    <div class="month-nav">

        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}">
            ← 前月
        </a>

        <div class="current-month">
            📅 {{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}
        </div>

        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}">
            翌月 →
        </a>

    </div>

    {{-- テーブル --}}
    <div class="table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($attendances as $attendance)
                <tr>

                    {{-- 日付 --}}
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                        ({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($attendance->work_date)->dayOfWeek] }})
                    </td>

                    {{-- 出勤 --}}
                    <td>
                        {{ $attendance->clock_in
                            ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')
                            : '' }}
                    </td>

                    {{-- 退勤 --}}
                    <td>
                        {{ $attendance->clock_out
                            ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')
                            : '' }}
                    </td>

                    {{-- 休憩 --}}
                    <td>
                        @php
                            $breakMinutes = $attendance->breaks->sum(function($b) {
                                if ($b->break_start && $b->break_end) {
                                    return \Carbon\Carbon::parse($b->break_end)
                                        ->diffInMinutes($b->break_start);
                                }
                                return 0;
                            });
                        @endphp

                        {{ $breakMinutes > 0
                            ? sprintf('%02d:%02d', floor($breakMinutes / 60), $breakMinutes % 60)
                            : '00:00' }}
                    </td>

                    {{-- 合計 --}}
                    <td>
                        @if($attendance->clock_in && $attendance->clock_out)
                            @php
                                $workMinutes =
                                    \Carbon\Carbon::parse($attendance->clock_out)
                                    ->diffInMinutes($attendance->clock_in)
                                    - $breakMinutes;
                            @endphp

                            {{ sprintf('%02d:%02d', floor($workMinutes / 60), $workMinutes % 60) }}
                        @endif
                    </td>

                    {{-- 詳細 --}}
                    <td class="detail-cell">
                        <a href="{{ route('attendance.detail', $attendance->id) }}">
                            詳細
                        </a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection