@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('title', '修正申請承認')

@section('header-nav')
    <a href="/admin/attendance/list">勤怠一覧</a>
    <a href="/admin/staff/list">スタッフ一覧</a>
    <a href="{{ route('stamp.request.list') }}">申請一覧</a>
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('content')

<div class="detail-container">

    <h2 class="detail-title">修正申請承認</h2>

    <form method="POST"
      action="{{ route('admin.stamp.request.approve', $requestData->id) }}">
        @csrf

        <div class="detail-card">

            {{-- 名前 --}}
            <div class="row">
                <div class="label">名前</div>
                <div class="value">
                    {{ $requestData->user->name }}
                </div>
            </div>

            {{-- 日付 --}}
            <div class="row">
                <div class="label">日付</div>
                <div class="value date-flex">
                    @php
                    $year = \Carbon\Carbon::parse($requestData->target_date)->format('Y年');
                    $monthDay = \Carbon\Carbon::parse($requestData->target_date)->format('n月j日');
                    @endphp

                    <span>{{ $year }}</span>
                    <span>{{ $monthDay }}</span>
                </div>
            </div>

            {{-- 出勤・退勤（申請内容） --}}
            <div class="row">
                <div class="label">出勤・退勤</div>
                <div class="value">
                    <div class="time-row">
                        <input type="time"
                         value="{{ \Carbon\Carbon::parse($requestData->start_time)->format('H:i') }}"
                         disabled>

                        <input type="time"
                          value="{{ \Carbon\Carbon::parse($requestData->end_time)->format('H:i') }}"
                          disabled>
                    </div>
                </div>
            </div>

            {{-- 休憩 --}}
            @if(!empty($requestData->breaks))
            @foreach($requestData->breaks as $b)

            @if(!empty($b['start']) || !empty($b['end']))
           <div class="row">
           <div class="label">
            休憩{{ $loop->first ? '' : $loop->iteration }}
           </div>

            <div class="value">
            <div class="time-flex">
                <input type="time" value="{{ $b['start'] ?? '' }}" disabled>
                <span>〜</span>
                <input type="time" value="{{ $b['end'] ?? '' }}" disabled>
            </div>
            </div>
            </div>
            @endif

            @endforeach
            @endif

            {{-- 備考 --}}
            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <textarea disabled>{{ $requestData->reason }}</textarea>
                </div>
            </div>

        </div>

        {{-- 承認ボタン --}}
        <div class="btn-area">

         @if($requestData->status === 'approved')
         <button type="button" class="btn approved-btn" disabled>
         承認済み
         </button>
          @else
         <button type="submit" class="btn">
          承認
        </button>
         @endif

        </div>

        </form>
    </div>
@endsection