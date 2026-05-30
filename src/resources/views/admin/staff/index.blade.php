@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/admin_staff_list.css') }}">
@endpush

@section('title', 'スタッフ一覧（管理者）')

@section('header-nav')
    <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
    <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
    <a href="{{ route('admin.request.list') }}">申請一覧</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
@endsection

@section('content')

<div class="staff-list-container">

    <h2 class="page-title">スタッフ一覧</h2>

    <div class="table-wrapper">

        <table class="staff-table">

            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($users as $user)

                    <tr>
                        <td>
                            {{ $user->name }}
                        </td>

                        <td>
                            {{ $user->email }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.staff.attendance', $user->id) }}"
                                class="detail-link"
                            >
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