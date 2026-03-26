@extends('layouts.app')

@section('title', '商品出品')

@section('content')
<div class="sell-page">
  <h1 class="sell-title">商品の出品</h1>

  <form class="sell-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- 商品画像 --}}
    <section class="sell-section">
      <div class="field">
        <p class="field__label">商品画像</p>

        <div class="image-picker">
          <div class="image-picker__inner">
            <img
              id="itemPreview"
              class="image-picker__preview is-hidden"
              alt="商品画像プレビュー">

            <span id="itemPreviewText" class="image-picker__text">
              No Image
            </span>

            <label for="image" class="btn-image">画像を選択する</label>

            <input
              id="image"
              type="file"
              name="image"
              accept="image/*"
              class="field__input">
          </div>
        </div>

        @error('image')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>
    </section>

    {{-- 商品の詳細 --}}
    <section class="sell-section">
      <h2 class="sell-section__title">商品の詳細</h2>

      {{-- カテゴリー --}}
      <div class="field">
        <label class="field__label">カテゴリー</label>

        <div class="chip-group" role="group" aria-label="カテゴリー選択">
          @foreach($categories as $category)
          @php
          $selected = in_array($category->id, old('category_ids', []));
          @endphp

          <label class="chip">
            <input
              type="checkbox"
              name="category_ids[]"
              value="{{ $category->id }}"
              {{ $selected ? 'checked' : '' }}>
            <span class="chip__text">{{ $category->name }}</span>
          </label>
          @endforeach
        </div>

        @error('category_ids')
        <p class="field__error">{{ $message }}</p>
        @enderror
        @error('category_ids.*')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品の状態 --}}
      <div class="field">
        <label class="field__label">商品の状態</label>

        <div class="custom-select" data-name="condition_id" data-placeholder="選択してください">
          <button type="button" class="custom-select__trigger" aria-haspopup="listbox" aria-expanded="false">
            <span class="custom-select__value">選択してください</span>
          </button>

          <ul class="custom-select__list" role="listbox">
            @foreach($conditions as $cond)
            <li class="custom-select__option" role="option" data-value="{{ $cond->id }}">
              {{ $cond->label }}
            </li>
            @endforeach
          </ul>

          <input type="hidden" name="condition_id" value="{{ old('condition_id') }}">
        </div>

        @error('condition_id')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>
    </section>

    {{-- 商品名と説明 --}}
    <section class="sell-section">
      <h2 class="sell-section__title">商品名と説明</h2>

      {{-- 商品名 --}}
      <div class="field">
        <label class="field__label" for="name">商品名</label>
        <input
          id="name"
          type="text"
          name="name"
          value="{{ old('name') }}"
          class="field__input"
          placeholder="例：腕時計">
        @error('name')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>

      {{-- ブランド名 --}}
      <div class="field">
        <label class="field__label" for="brand_name">ブランド名</label>
        <input
          id="brand_name"
          type="text"
          name="brand_name"
          value="{{ old('brand_name') }}"
          class="field__input"
          placeholder="（任意）">
        @error('brand_name')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品の説明 --}}
      <div class="field">
        <label class="field__label" for="description">商品の説明</label>
        <textarea
          id="description"
          name="description"
          class="field__textarea"
          rows="6"
          placeholder="商品の色・サイズ感・購入時期などをご記入ください">{{ old('description') }}</textarea>
        @error('description')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 販売価格 --}}
      <div class="field">
        <label class="field__label" for="price">販売価格</label>

        <div class="price-input">
          <span class="price-input__yen">¥</span>
          <input
            id="price"
            type="text"
            inputmode="numeric"
            name="price"
            value="{{ old('price') }}"
            class="price-input__field"
            placeholder="0">
        </div>

        @error('price')
        <p class="field__error">{{ $message }}</p>
        @enderror
      </div>
    </section>

    {{-- 出品ボタン --}}
    <div class="sell-actions">
      <button type="submit" class="btn btn-primary">出品する</button>
    </div>
  </form>
</div>
@endsection