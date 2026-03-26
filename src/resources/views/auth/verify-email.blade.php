@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div class="verify-page">
  <div class="verify-box">

    {{-- 認証案内メッセージ --}}
    <p class="verify-text">
      登録していただいたメールアドレスに確認メールを送信しました。<br>
      メール認証を完了してください。
    </p>

    {{-- MailHog確認用リンク --}}
    <div class="verify-mailhog">
      <a
        href="http://localhost:8025"
        target="_blank"
        rel="noopener noreferrer"
        class="verify-btn"
      >
        認証はこちらから
      </a>
    </div>

    {{-- 再送成功メッセージ --}}
    @if (session('status') == 'verification-link-sent')
      <p class="verify-success">確認メールを再送しました。</p>
    @endif

    {{-- 再送はPOSTが必要なので、見た目だけ青字リンクにする --}}
    <form method="POST" action="{{ route('verification.send') }}" class="verify-resend-form">
      @csrf
      <button type="submit" class="verify-resend-link">
        確認メールを再送する
      </button>
    </form>

  </div>
</div>
@endsection