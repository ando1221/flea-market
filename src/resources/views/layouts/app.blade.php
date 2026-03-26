<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Flea Market')</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

  <header class="header">
    <div class="header__inner">

      {{-- ロゴ --}}
      <a class="brand" href="{{ route('items.index') }}">
        <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="Flea Market">
      </a>

      {{-- ログイン・会員登録・メール認証画面以外でヘッダー機能を表示 --}}
      @unless (Route::is('login', 'register', 'verification.*'))

      {{-- 検索フォーム --}}
      <form class="search" method="GET" action="{{ route('items.index') }}">
        <input
          type="hidden"
          name="tab"
          value="{{ request('tab', 'recommend') }}">

        <input
          type="text"
          name="keyword"
          placeholder="なにをお探しですか？"
          value="{{ request('keyword') }}">
      </form>

      {{-- ナビゲーション --}}
      <nav class="nav">

        {{-- ログイン中 --}}
        @auth
        {{-- メール認証済みユーザー --}}
        @if (auth()->user()->hasVerifiedEmail())
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="nav__link nav__logout" type="submit">
            ログアウト
          </button>
        </form>
        <a class="nav__link" href="{{ route('mypage.index') }}">マイページ</a>
        <a class="nav__link nav__sell" href="{{ route('sell.create') }}">出品</a>

        {{-- メール未認証ユーザー --}}
        @else
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="nav__link nav__logout" type="submit">
            ログアウト
          </button>
        </form>

        <a class="nav__link nav__notice" href="{{ route('verification.notice') }}">
          メール認証を完了してください
        </a>
        @endif

        {{-- 未ログイン --}}
        @else
        <a class="nav__link" href="{{ route('login') }}">ログイン</a>
        <a class="nav__link" href="{{ route('mypage.index') }}">マイページ</a>
        <a class="nav__link nav__sell" href="{{ route('sell.create') }}">出品</a>
        @endauth

      </nav>
      @endunless

    </div>
  </header>

  {{-- 成功メッセージ --}}
  @if (session('success'))
  <div class="flash-message flash-message--success">
    {{ session('success') }}
  </div>
  @endif

  {{-- 各画面のコンテンツ --}}
  @yield('content')

  <script src="{{ asset('js/custom-select.js') }}"></script>
  <script src="{{ asset('js/image-preview.js') }}"></script>
</body>

</html>