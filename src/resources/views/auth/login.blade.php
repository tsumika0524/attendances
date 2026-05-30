@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('title', 'ログイン')

@section('content')

@section('body-class', 'auth-page')

<div class="login-container">

    <h2 class="login-title">ログイン</h2>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- メールアドレス -->
        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">

            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror

        </div>

        <!-- パスワード -->
        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="password">

            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- ログインボタン -->
        <div class="form-group">
        <button type="submit" class="login-btn">ログインする</button>
        </div>

    </form>

    <!-- 会員登録リンク -->
    <div class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>

</div>

@endsection