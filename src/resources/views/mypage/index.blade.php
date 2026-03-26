@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="page">

  {{-- プロフィール表示 --}}
  <div class="mypage-profile">
    @php
      $user = auth()->user();
    @endphp

    <div class="mypage-profile__left">
      <div class="mypage-profile__avatar">
        @if ($user?->profile_image_path)
          <img src="{{ asset('storage/'.$user->profile_image_path) }}" alt="profile">
        @endif
      </div>
      <div class="mypage-profile__name">{{ $user->name ?? '' }}</div>
    </div>

    <div class="mypage-profile__right">
      <a class="btn-edit" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>
  </div>

  {{-- タブ切り替え --}}
  <div class="tabs">
    <a
      class="tab {{ ($tab ?? 'sell') === 'sell' ? 'is-active' : '' }}"
      href="{{ route('mypage.index', ['tab' => 'sell']) }}"
    >
      出品した商品
    </a>

    {{-- ログイン中は購入商品タブを表示 --}}
    @auth
      <a
        class="tab {{ ($tab ?? 'sell') === 'buy' ? 'is-active' : '' }}"
        href="{{ route('mypage.index', ['tab' => 'buy']) }}"
      >
        購入した商品
      </a>
    @else
      <a class="tab" href="{{ route('login') }}">購入した商品</a>
    @endauth
  </div>

  <div class="tabs__line"></div>

  {{-- 商品一覧 --}}
  <div class="grid">
    @forelse ($items as $item)
      <a class="card" href="{{ route('items.show', $item) }}">
        <div class="card__img-wrap">
          <div class="card__img {{ $item->status === 'sold' ? 'card__img--sold' : '' }}">
            {{-- 商品画像の有無で切り替え --}}
            @if (!empty($item->image_path))
              <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            @else
              <img src="{{ asset('images/no-image.png') }}" alt="no image">
            @endif

            {{-- 売り切れ商品の場合はSold表示 --}}
            @if ($item->status === 'sold')
              <div class="card__sold">Sold</div>
            @endif
          </div>
        </div>

        <div class="card__name">{{ $item->name }}</div>
      </a>
    @empty
      <div class="empty">
        表示する商品がありません。
      </div>
    @endforelse
  </div>
</div>
@endsection