@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('header-nav')
    <a href="/attendance">勤怠</a>
    <a href="/attendance/list">勤怠一覧</a>
    <a href="{{ route('stamp.request.list') }}">申請</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('title', '勤怠詳細')

@section('content')

@php
$validBreaks = $attendance->breaks;
@endphp

<div class="detail-container">

    <h2 class="detail-title">勤怠詳細</h2>

    <form method="POST" action="{{ route('stamp.request.store') }}">
    @csrf

    <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
    <input type="hidden" name="target_date" value="{{ $attendance->work_date }}">

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
                @php
                $year = \Carbon\Carbon::parse($attendance->work_date)->format('Y年');
                $monthDay = \Carbon\Carbon::parse($attendance->work_date)->format('n月j日');
                @endphp

                <span>{{ $year }}</span>
                <span>{{ $monthDay }}</span>
            </div>
        </div>

        {{-- 出勤・退勤 --}}
        <div class="row">
        <div class="label">出勤・退勤</div>
        <div class="value">

        <div class="time-row">
            <input type="time" name="clock_in"
                value="{{ old('clock_in', optional($attendance->clock_in)->format('H:i')) }}"
                @if($isPending || $isApproved) disabled @endif>

            <span>〜</span>

            <input type="time" name="clock_out"
                value="{{ old('clock_out', optional($attendance->clock_out)->format('H:i')) }}"
                @if($isPending || $isApproved) disabled @endif>
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
        @foreach($validBreaks as $i => $b)
      <div class="row">
      <div class="label">
       休憩{{ $loop->first ? '' : $loop->iteration }}
      </div>

      <div class="value">
        <div class="time-flex">
            <input type="time" name="breaks[{{ $i }}][start]"
                value="{{ $b->break_start ? \Carbon\Carbon::parse($b->break_start)->format('H:i') : '' }}"
                @if($isPending || $isApproved) disabled @endif>

            <span>〜</span>

            <input type="time" name="breaks[{{ $i }}][end]"
                value="{{ $b->break_end ? \Carbon\Carbon::parse($b->break_end)->format('H:i') : '' }}"
                @if($isPending || $isApproved) disabled @endif>
        </div>

        @error("breaks.$i.start")
            <p class="error">{{ $message }}</p>
        @enderror
        @error("breaks.$i.end")
            <p class="error">{{ $message }}</p>
        @enderror
       </div>
     </div>
      @endforeach

        {{-- 備考 --}}
        <div class="row">
    <div class="label">備考</div>

    <div class="value">
        <textarea name="reason" {{ ($isPending || $isApproved) ? 'disabled' : '' }}>{{ old('note', optional($attendance->correctionRequest)->reason ?? '') }}</textarea>
        @error('reason')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>
    </div>

    </div>

    @if($isPending)

    <p class="notice pending">
        ※承認待ちのため修正はできません。
    </p>

@elseif($isApproved)

    <div class="btn-area">
        <button type="button" class="btn approved-btn" disabled>
            承認済み
        </button>
    </div>

@else

    <div class="btn-area">
        <button type="submit" class="btn">
            修正
        </button>
    </div>

@endif

    </form>
</div>
@endsection