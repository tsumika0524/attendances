@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('title', '勤怠詳細')

@section('header-nav')
    <a href="/admin/attendance/list">勤怠一覧</a>
    <a href="/admin/staff/list">スタッフ一覧</a>
    <a href="{{ route('admin.request.list') }}">申請一覧</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('content')

<div class="detail-container">

    <h2 class="detail-title">勤怠詳細</h2>

    <form method="POST"
        action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf

        <div class="detail-card">

            {{-- 名前 --}}
    <div class="row">
    <div class="label">名前</div>

    <div class="value">
        {{ $attendance->user->name }}
    </div>
    </div>

            {{-- 日付 --}}
            <div class="row">
                <div class="label">日付</div>

                <div class="value date-flex">

                    <span>
                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}
                    </span>

                    <span>
                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}
                    </span>

                </div>
            </div>

            {{-- 出勤退勤 --}}
            <div class="row">
                <div class="label">出勤・退勤</div>

                <div class="value">
                    <div class="time-row">

                        <input type="time"
                            name="clock_in"
                            value="{{ optional($attendance->clock_in)->format('H:i') }}">
                           
                        <span>〜</span>

                        <input type="time"
                            name="clock_out"
                            value="{{ optional($attendance->clock_out)->format('H:i') }}">
                           

                    </div>

                      @error('clock_in')
                     <p class="error">{{ $message }}</p>
                      @enderror

                      @error('clock_out')
                    <p class="error">{{ $message }}</p>
                      @enderror
                </div>
            </div>

            {{-- 休憩 --}}
            @foreach($attendance->breaks as $break)

<div class="row">

    <div class="label">
        休憩{{ $loop->first ? '' : $loop->iteration }}
    </div>

    <div class="value">

        <div class="time-flex">

            <input type="time"
                name="breaks[{{ $loop->index }}][start]"
                value="{{ $break->break_start
                    ? \Carbon\Carbon::parse($break->break_start)->format('H:i')
                    : '' }}">
        
            <span>〜</span>

            <input type="time"
                name="breaks[{{ $loop->index }}][end]"
                value="{{ $break->break_end
                    ? \Carbon\Carbon::parse($break->break_end)->format('H:i')
                    : '' }}">
        
        </div>

        @error("breaks.$loop->index.start")
            <p class="error">{{ $message }}</p>
        @enderror

        @error("breaks.$loop->index.end")
            <p class="error">{{ $message }}</p>
        @enderror


    </div>

</div>

@endforeach

            {{-- 備考 --}}
            <div class="row">

                <div class="label">備考</div>

                <div class="value">
                    <textarea name="note">{{ $attendance->note }}</textarea>
                @error('note')
               <p class="error">{{ $message }}</p>
                @enderror    
                </div>

            </div>
        </div>

        {{-- 修正ボタン --}}
        <div class="btn-area">
            <button type="submit" class="btn">
                修正
            </button>
        </div>

    </form>

</div>

@endsection