@extends('layouts.app')

@section('title', '配送先の変更')

@section('content')
<div class="profile-edit">
  <h1 class="profile-edit__title">配送先の変更</h1>

  <form class="profile-edit__form" method="POST" action="{{ route('purchase.address.update', $item) }}">
    @csrf

    {{-- 郵便番号 --}}
    <div class="profile-edit__block">
      <label class="profile-edit__label" for="zip">郵便番号</label>
      <input
        id="zip"
        class="profile-edit__input"
        name="zip"
        type="text"
        value="{{ old('zip', $address['zip']) }}"
        placeholder="例：100-0001"
      >
      @error('zip')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    {{-- 住所 --}}
    <div class="profile-edit__block">
      <label class="profile-edit__label" for="address">住所</label>
      <input
        id="address"
        class="profile-edit__input"
        name="address"
        type="text"
        value="{{ old('address', $address['address']) }}"
        placeholder="例：東京都千代田区1-1-1"
      >
      @error('address')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    {{-- 建物名 --}}
    <div class="profile-edit__block">
      <label class="profile-edit__label" for="building">建物名等</label>
      <input
        id="building"
        class="profile-edit__input"
        name="building"
        type="text"
        value="{{ old('building', $address['building']) }}"
        placeholder="例：◯◯ビル101（任意）"
      >
      @error('building')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    {{-- 更新ボタン --}}
    <div class="profile-edit__actions">
      <button class="btn-update" type="submit">更新する</button>
    </div>
  </form>
</div>
@endsection