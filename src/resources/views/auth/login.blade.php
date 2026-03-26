@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="auth-page">
  <div class="auth-card">
    {{-- ログイン画面タイトル --}}
    <h1 class="auth-title">ログイン</h1>

    {{-- ログインフォーム --}}
    <form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      {{-- メールアドレス入力欄 --}}
      <div class="auth-field">
        <label class="auth-label">メールアドレス</label>
        <input 
          class="auth-input" 
          type="email" 
          name="email"
          value="{{ old('email') }}"
        >

        @error('email')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- パスワード入力欄 --}}
      <div class="auth-field">
        <label class="auth-label">パスワード</label>
        <input 
          class="auth-input" 
          type="password" 
          name="password"
        >

        @error('password')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- ログインボタン --}}
      <button class="auth-btn" type="submit">
        ログイン
      </button>
    </form>

    {{-- 会員登録画面へのリンク --}}
    <div class="auth-link">
      <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
  </div>
</div>
@endsection