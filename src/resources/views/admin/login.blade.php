@extends('layouts.app')

@section('body-class', 'auth-page')

@section('title', '管理者ログイン')

@push('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')

<div class="login-container">

    <h2 class="login-title">管理者ログイン</h2>

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">

            @error('email')
        <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="password">

            @error('password')
        <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="login-btn">
            管理者ログインする
        </button>

    </form>

</div>

@endsection