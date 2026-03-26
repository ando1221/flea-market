@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="page">
  {{-- 一覧タブ --}}
  <div class="tabs">
    <a
      class="tab {{ ($tab ?? 'recommend') === 'recommend' ? 'is-active' : '' }}"
      href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
    >
      おすすめ
    </a>

    {{-- ログイン中はマイリストタブ、未ログイン時はログイン画面へ --}}
    @auth
      <a
        class="tab {{ ($tab ?? 'recommend') === 'mylist' ? 'is-active' : '' }}"
        href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
      >
        マイリスト
      </a>
    @else
      <a class="tab" href="{{ route('login') }}">マイリスト</a>
    @endauth
  </div>

  <div class="tabs__line"></div>

  {{-- 商品一覧 --}}
  <div class="grid">
    @foreach ($items as $item)
      <a class="card" href="{{ route('items.show', $item) }}">
        <div class="card__img-wrap">
          <div class="card__img {{ $item->status === 'sold' ? 'card__img--sold' : '' }}">
            {{-- 商品画像がある場合は表示、ない場合は代替画像を表示 --}}
            @if (!empty($item->image_path))
              <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            @else
              <img src="{{ asset('images/no-image.png') }}" alt="no image">
            @endif

            {{-- 売り切れ商品の場合はSoldを表示 --}}
            @if ($item->status === 'sold')
              <div class="card__sold">Sold</div>
            @endif
          </div>
        </div>

        <div class="card__name">
          {{ $item->name }}
        </div>
      </a>
    @endforeach
  </div>
</div>
@endsection