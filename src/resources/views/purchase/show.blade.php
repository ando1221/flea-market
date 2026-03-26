@extends('layouts.app')

@section('title', '購入内容の確認')

@section('content')
<div class="purchase-page">
  <div class="purchase-layout">

    {{-- 左側：購入内容 --}}
    <div class="purchase-main">

      {{-- 商品情報 --}}
      <section class="purchase-section">
        <div class="purchase-item">
          <img src="{{ asset('storage/' . $item->image_path) }}" alt="">
          <div class="purchase-item__info">
            <p class="purchase-item__name">{{ $item->name }}</p>
            <p class="purchase-item__price">¥{{ number_format($item->price) }}</p>
          </div>
        </div>
      </section>

      {{-- 支払い方法 --}}
      <section class="purchase-section">
        <h2 class="purchase-section__title">支払い方法</h2>

        {{-- 支払い方法は選択時に自動送信 --}}
        <form method="POST" action="{{ route('purchase.payment.update', $item) }}">
          @csrf

          <div
            class="custom-select"
            data-name="payment_method_id"
            data-placeholder="選択してください"
            data-auto-submit="true"
          >
            <button
              type="button"
              class="custom-select__trigger"
              aria-haspopup="listbox"
              aria-expanded="false"
            >
              <span class="custom-select__value">
                {{ $selectedPaymentMethodLabel ?? '選択してください' }}
              </span>
            </button>

            <ul class="custom-select__list" role="listbox">
              @foreach($paymentMethods as $method)
                <li
                  class="custom-select__option {{ (int) ($selectedPaymentMethodId ?? 0) === $method->id ? 'is-selected' : '' }}"
                  role="option"
                  data-value="{{ $method->id }}"
                >
                  {{ $method->label }}
                </li>
              @endforeach
            </ul>

            <input type="hidden" name="payment_method_id" value="{{ $selectedPaymentMethodId ?? '' }}">
          </div>

          @error('payment_method_id')
            <p class="purchase-error">{{ $message }}</p>
          @enderror
        </form>
      </section>

      {{-- 配送先 --}}
      <section class="purchase-section">
        <div class="purchase-address__head">
          <h2 class="purchase-section__title">配送先</h2>
          <a href="{{ route('purchase.address.edit', $item) }}" class="purchase-address__edit">変更する</a>
        </div>

        <p class="purchase-address">
          〒{{ $shippingAddress['zip'] }}<br>
          {{ $shippingAddress['address'] }}<br>
          {{ $shippingAddress['building'] }}
        </p>
      </section>

    </div>

    {{-- 右側：購入サマリ --}}
    <aside class="purchase-summary">
      <div class="summary-box">
        <div class="summary-row">
          <span class="summary-key">商品代金</span>
          <span class="summary-val">¥{{ number_format($item->price) }}</span>
        </div>

        <div class="summary-row">
          <span class="summary-key">支払い方法</span>
          <span class="summary-val summary-payment {{ $selectedPaymentMethodLabel ? '' : 'is-empty' }}">
            {{ $selectedPaymentMethodLabel ?? '未選択' }}
          </span>
        </div>
      </div>

      {{-- 支払い方法未選択時は購入時にバリデーションエラーを表示 --}}
      <form method="POST" action="{{ route('purchase.store', $item) }}">
        @csrf
        <input type="hidden" name="payment_method_id" value="{{ $selectedPaymentMethodId ?? '' }}">

        <button type="submit" class="btn btn-primary summary-btn">
          購入する
        </button>
      </form>
    </aside>

  </div>
</div>
@endsection