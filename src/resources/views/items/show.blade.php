@extends('layouts.app')

@section('title', $item->name)

@section('content')
<div class="detail">

  <div class="detail__top">

    <div class="detail__image">
      {{-- 商品画像表示 --}}
      @if (!empty($item->image_path))
        <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}">
      @else
        <img src="{{ asset('images/no-image.png') }}" alt="no image">
      @endif
    </div>

    <div class="detail__info">

      <h1 class="detail__name">{{ $item->name }}</h1>

      <div class="detail__brand">
        {{ filled($item->brand_name) ? $item->brand_name : '' }}
      </div>

      <div class="detail__price">
        <span class="detail__yen">¥</span>
        <span class="detail__amount">{{ number_format($item->price) }}</span>
        <span class="detail__tax">（税込）</span>
      </div>

      <div class="detail__icons">
        <div class="icon-block">
          {{-- ログイン中はお気に入り登録・解除、未ログイン時はログイン画面へ --}}
          @auth
            <form method="POST"
                  action="{{ $isFavorited ? route('favorites.destroy', $item) : route('favorites.store', $item) }}">
              @csrf
              @if($isFavorited) @method('DELETE') @endif

              <button type="submit" class="icon-btn" aria-label="お気に入り">
                <img
                  src="{{ asset($isFavorited ? 'images/ハートロゴ_ピンク.png' : 'images/ハートロゴ_デフォルト.png') }}"
                  class="icon"
                  alt="お気に入り"
                >
              </button>
            </form>
          @else
            <a class="icon-btn" href="{{ route('login') }}" aria-label="お気に入り（ログイン）">
              <img src="{{ asset('images/ハートロゴ_デフォルト.png') }}" class="icon" alt="お気に入り">
            </a>
          @endauth

          <div class="icon-count">{{ $item->favorites_count }}</div>
        </div>

        <div class="icon-block">
          <a class="icon-btn" href="#comments" aria-label="コメントへ移動">
            <img src="{{ asset('images/ふきだしロゴ.png') }}" class="icon" alt="コメント">
          </a>
          <div class="icon-count">{{ $item->comments_count }}</div>
        </div>
      </div>

      {{-- 購入可否の判定に使う --}}
      @php
        $isOwner = auth()->check() && auth()->id() === $item->user_id;
        $isSold  = ($item->status ?? '') === 'sold';
      @endphp

      {{-- 売り切れ・出品者本人・未ログインで表示を切り替える --}}
      @if($isSold)
        <a class="btn-buy btn-block" >
          売り切れ
        </a>
      @else
        @auth
          @if(!$isOwner)
            <a class="btn-buy btn-block" href="{{ route('purchase.show', $item) }}">
              購入手続きへ
            </a>
          @else
            <a class="btn-buy btn-block" >
              自分の商品は購入できません
            </a>
          @endif
        @else
          <a class="btn-buy btn-block" href="{{ route('login') }}">
            購入手続きへ
          </a>
        @endauth
      @endif

      <section class="detail__section">
        <h2 class="detail__heading">商品説明</h2>
        <p class="detail__text">{{ $item->description ?? '（説明はありません）' }}</p>
      </section>

      <section class="detail__section">
        <h2 class="detail__heading">商品の情報</h2>

        <div class="info-row">
          <div class="info-key">カテゴリー</div>
          <div class="info-val">
            {{-- カテゴリー一覧を表示 --}}
            @forelse($item->categories as $cat)
              <span class="badge">{{ $cat->name }}</span>
            @empty
              <span>なし</span>
            @endforelse
          </div>
        </div>

        <div class="info-row">
          <div class="info-key">商品の状態</div>
          <div class="info-val">
            {{ optional($item->condition)->label ?? 'なし' }}
          </div>
        </div>
      </section>

      <section id="comments" class="detail__comments">
        <h2 class="detail__heading">
          コメント（{{ $item->comments_count }}）
        </h2>

        <div class="comment-list">
          {{-- コメント一覧を表示、未投稿ならメッセージを表示 --}}
          @forelse($item->comments as $comment)
            <div class="comment {{ $comment->user_id === $item->user_id ? 'comment--seller' : '' }}">
              <div class="comment__head">
                <div class="comment__avatar">
                  {{-- プロフィール画像がある場合のみ表示 --}}
                  @if ($comment->user->profile_image_path)
                    <img src="{{ asset('storage/'.$comment->user->profile_image_path) }}" alt="profile">
                  @endif
                </div>
                <div class="comment__user">{{ $comment->user->name }}</div>
              </div>

              <div class="comment__body">
                {{ $comment->body }}
              </div>
            </div>
          @empty
            <div class="comment-empty">コメントはまだありません。</div>
          @endforelse
        </div>

        <div class="comment-form-title">
          商品へのコメント
        </div>

        <div class="comment-form">
          {{-- ログイン中のみコメント投稿を許可 --}}
          @auth
            <form method="POST" action="{{ route('comments.store', $item) }}">
              @csrf

              <textarea
                name="body"
                rows="4"
                placeholder="コメント内容"
              >{{ old('body') }}</textarea>

              @error('body')
                <p class="field__error">{{ $message }}</p>
              @enderror

              <button type="submit" class="btn-send btn-block">
                コメントを送信する
              </button>
            </form>
          @else
            <textarea
              rows="4"
              placeholder="コメント内容"
              disabled
            ></textarea>

            <div class="comment-note">
              コメントを投稿するにはログインが必要です
            </div>

            <a href="{{ route('login') }}" class="btn-send btn-block">
              ログインしてコメントする
            </a>
          @endauth
        </div>
      </section>

    </div>
  </div>
</div>
@endsection