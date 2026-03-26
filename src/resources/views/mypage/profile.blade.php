@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<div class="page">
  <div class="profile-edit">

    <h1 class="profile-edit__title">プロフィール設定</h1>

    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          class="profile-edit__form">
      @csrf

      {{-- プロフィール画像 --}}
      <div class="profile-edit__block">
        @php
          $profileImg = auth()->user()->profile_image_path
            ? asset('storage/' . auth()->user()->profile_image_path)
            : '';
        @endphp

        <div class="profile-edit__image-row">
          <div class="profile-edit__image">
            <img
              id="profilePreview"
              src="{{ $profileImg }}"
              alt="profile"
              class="{{ $profileImg ? '' : 'is-hidden' }}"
            >

            <span
              id="profilePreviewText"
              class="profile-edit__no-image {{ $profileImg ? 'is-hidden' : '' }}"
            >
              No Image
            </span>
          </div>

          {{-- 画像選択 --}}
          <label for="profile_image" class="btn-file">
            画像を選択する
          </label>

          <input type="file"
                 name="profile_image"
                 id="profile_image"
                 accept=".jpg,.jpeg,.png"
                 class="profile-edit__file">

          @error('profile_image')
            <div class="error">{{ $message }}</div>
          @enderror
        </div>
      </div>

      {{-- ユーザー名 --}}
      <div class="profile-edit__block">
        <label class="profile-edit__label">ユーザー名</label>
        <input type="text"
               name="name"
               value="{{ old('name', auth()->user()->name) }}"
               class="profile-edit__input">
        @error('name')
          <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 郵便番号 --}}
      <div class="profile-edit__block">
        <label class="profile-edit__label">郵便番号</label>
        <input type="text"
               name="zip"
               value="{{ old('zip', auth()->user()->zip) }}"
               class="profile-edit__input">
        @error('zip')
          <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 住所 --}}
      <div class="profile-edit__block">
        <label class="profile-edit__label">住所</label>
        <input type="text"
               name="address"
               value="{{ old('address', auth()->user()->address) }}"
               class="profile-edit__input">
        @error('address')
          <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 建物名 --}}
      <div class="profile-edit__block">
        <label class="profile-edit__label">建物名</label>
        <input type="text"
               name="building"
               value="{{ old('building', auth()->user()->building) }}"
               class="profile-edit__input">
        @error('building')
          <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 更新ボタン --}}
      <div class="profile-edit__actions">
        <button type="submit" class="btn-update">
          更新する
        </button>
      </div>

    </form>
  </div>
</div>
@endsection