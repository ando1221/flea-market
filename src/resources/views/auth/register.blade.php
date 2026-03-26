@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="auth-page">
  <div class="auth-card">
    <h1 class="auth-title">会員登録</h1>

    <form class="auth-form" method="POST" action="{{ route('register') }}" novalidate>
      @csrf

      {{-- ユーザー名 --}}
      <div class="auth-field">
        <label class="auth-label" for="name">ユーザー名</label>
        <input
          class="auth-input"
          id="name"
          name="name"
          type="text"
          value="{{ old('name') }}"
        >
        @error('name')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- メールアドレス --}}
      <div class="auth-field">
        <label class="auth-label" for="email">メールアドレス</label>
        <input
          class="auth-input"
          id="email"
          name="email"
          type="email"
          value="{{ old('email') }}"
        >
        @error('email')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- パスワード --}}
      <div class="auth-field">
        <label class="auth-label" for="password">パスワード</label>
        <input
          class="auth-input"
          id="password"
          name="password"
          type="password"
        >
        @error('password')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- パスワード確認 --}}
      <div class="auth-field">
        <label class="auth-label" for="password_confirmation">確認用パスワード</label>
        <input
          class="auth-input"
          id="password_confirmation"
          name="password_confirmation"
          type="password"
        >
        @error('password_confirmation')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 会員登録ボタン --}}
      <button class="auth-btn" type="submit">
        登録する
      </button>
    </form>

    {{-- ログイン画面へのリンク --}}
    <div class="auth-link">
      <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
  </div>
</div>
@endsection