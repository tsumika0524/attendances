@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('title', '勤怠')

@section('header-nav')
    <a href="/attendance">勤怠</a>
    <a href="{{ route('attendance.list') }}">勤怠一覧</a>
    <a href="{{ route('stamp.request.list') }}">申請</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('content')

<div class="attendance-container">

    <!-- ステータス -->
    <div class="status">
        {{ $statusLabel }}
    </div>

    <!-- 日付 -->
    <div class="date">
        {{ $date }}
    </div>

    <!-- 時刻 -->
    <div class="time" id="time"></div>

    <!-- ボタン -->
    <div class="button-area">

        {{-- 勤務外 --}}
       @if ($status === \App\Models\Attendance::STATUS_OFF)
            <form method="POST" action="{{ route('attendance.clockIn') }}">
                @csrf
                <button class="btn">出勤</button>
            </form>
        @endif


        {{-- 出勤中 --}}
       @if ($status === \App\Models\Attendance::STATUS_WORKING)
            <form method="POST" action="{{ route('attendance.clockOut') }}">
                @csrf
                <button class="btn">退勤</button>
            </form>

            <form method="POST" action="{{ route('attendance.breakIn') }}">
                @csrf
                <button class="btn btn-white">休憩入</button>
            </form>
        @endif


        {{-- 休憩中 --}}
       @if ($status === \App\Models\Attendance::STATUS_BREAK)
            <form method="POST" action="{{ route('attendance.breakOut') }}">
                @csrf
                <button class="btn btn-white">休憩戻</button>
            </form>
        @endif


        {{-- 退勤済 --}}
       @if ($status === \App\Models\Attendance::STATUS_DONE)
            <p class="message">お疲れ様でした。</p>
        @endif

    </div>

</div>

<!-- 時刻更新 -->
<script>
function updateTime() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');

    document.getElementById('time').textContent = `${h}:${m}`;
}

updateTime();
setInterval(updateTime, 1000);
</script>

@endsection