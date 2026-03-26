@extends('layouts.app')

@section('title', '購入完了')

@section('content')
<div class="page">
  <h1>購入が完了しました</h1>
  <p>{{ $item->name }} の購入が完了しました。</p>

  <div class="success-actions">
    <a href="{{ route('mypage.index', ['tab' => 'buy']) }}">購入一覧を見る</a>
    <a href="{{ route('items.index') }}">商品一覧へ戻る</a>
  </div>
</div>
@endsection