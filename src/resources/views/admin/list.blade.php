@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endpush

@section('title', '勤怠一覧（管理者）')

@section('header-nav')
    <a href="/admin/attendance/list">勤怠一覧</a>
    <a href="/admin/staff/list">スタッフ一覧</a>
    <a href="{{ route('admin.request.list') }}">申請一覧</a>

    <form method="POST"
        action="{{ route('logout') }}"
        class="logout-form">

        @csrf

        <button type="submit" class="logout-btn">
            ログアウト
        </button>

    </form>
@endsection

@section('content')

<div class="list-container">

    {{-- タイトル --}}
    <h2 class="list-title">
        {{ \Carbon\Carbon::parse($currentDate)->format('Y年m月d日') }}の勤怠
    </h2>

    {{-- 日付ナビ --}}
    <div class="month-nav">

        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}">
            ← 前日
        </a>

        <div class="current-month">
            📅 {{ \Carbon\Carbon::parse($currentDate)->format('Y/m/d') }}
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}">
            翌日 →
        </a>

    </div>

    {{-- テーブル --}}
    <div class="table-wrapper">

        <table class="attendance-table">

            <thead>
                <tr>
                    <th>名前</th>
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

                    {{-- 名前 --}}
                    <td>
                        {{ $attendance->user->name }}
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

                            $breakMinutes = $attendance->breaks->sum(function ($b) {

                                if ($b->break_start && $b->break_end) {

                                    return \Carbon\Carbon::parse($b->break_end)
                                        ->diffInMinutes($b->break_start);
                                }

                                return 0;
                            });

                        @endphp

                        {{ sprintf(
                            '%02d:%02d',
                            floor($breakMinutes / 60),
                            $breakMinutes % 60
                        ) }}

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

                            {{ sprintf(
                                '%02d:%02d',
                                floor($workMinutes / 60),
                                $workMinutes % 60
                            ) }}

                        @endif

                    </td>

                    {{-- 詳細 --}}
                    <td class="detail-cell">

                        <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
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