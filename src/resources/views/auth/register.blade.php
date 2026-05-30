@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('title', '会員登録')

@section('content')

@section('body-class', 'auth-page')

<div class="main">
    <div class="login-container">

        <h2 class="login-title">会員登録</h2>
        
        <form method="POST" action="/register">
         @csrf

            <!-- 名前 -->
            <div class="form-group">
                <label>名前</label>
                <input type="text" name="name" value="{{ old('name') }}">

                @error('name')
               <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- メール -->
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

            <!-- 確認 -->
            <div class="form-group">
                <label>確認用パスワード</label>
                <input type="password" name="password_confirmation">
            </div>

            <button type="submit" class="login-btn">登録する</button>
        </form>

        <p class="register-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>

    </div>
</div>

@endsection