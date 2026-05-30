@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/request-list.css') }}">
@endpush

@section('title', '申請一覧（管理者）')

@section('header-nav')
    <a href="/admin/attendance/list">勤怠一覧</a>
    <a href="/admin/staff/list">スタッフ一覧</a>
    <a href="/stamp_correction_request/list">申請一覧</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('content')
<div class="request-container">

    <h2 class="request-title">申請一覧</h2>

    <div class="tabs">
        <a href="?status=pending"
           class="tab {{ request('status', 'pending') === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="?status=approved"
           class="tab {{ request('status') === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="table-wrapper">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
                @foreach($requests as $request)
                <tr>
                    <td>
                        {{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}
                    </td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->target_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.stamp.request.show', $request->id) }}" class="detail-link">
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