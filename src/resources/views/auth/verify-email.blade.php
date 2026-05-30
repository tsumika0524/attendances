@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('title', 'メール認証')

@section('content')

<div class="verify-container">

    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <div class="verify-btn-area">
        <a href="http://localhost:8025" class="verify-btn">
            認証はこちらから
        </a>
    </div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit" class="resend-link">
            認証メールを再送する
        </button>
    </form>

</div>

@endsection