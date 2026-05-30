<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @stack('css')
</head>
<body class="@yield('body-class')">

    <!-- ヘッダー -->
    <header class="header">
    <div class="header-inner">

        <img src="{{ asset('images/logo.png') }}" alt="COACHTECH" class="logo">

        {{-- ★ここ修正 --}}
        @if (View::hasSection('header-nav'))
            <nav class="nav">
                @yield('header-nav')
            </nav>
        @endif

    </div>
    </header>

    @if(session('success'))
    <div class="global-success">
        {{ session('success') }}
    </div>
    @endif


    <!-- コンテンツ -->
    <main class="main">
        @yield('content')
    </main>

</body>
</html>