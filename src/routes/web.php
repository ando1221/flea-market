<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FortifyLoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseAddressController;

// 未ログインでも閲覧（一覧・詳細）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// ログイン送信は自作コントローラで受けて認証処理はFortify
Route::middleware('guest')->post('/login', [FortifyLoginController::class, 'store'])->name('login');

// 認証不要の外部通知
Route::post('/stripe/webhook', [PurchaseController::class, 'webhook'])
    ->name('stripe.webhook');
Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])
    ->name('purchase.success');

// ログイン + メール認証 必須（操作系）
Route::middleware(['auth', 'verified', 'profile.set'])->group(function () {

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/item/{item}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/item/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}/payment-method', [PurchaseController::class, 'update'])->name('purchase.payment.update');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/purchase/address/{item}', [PurchaseAddressController::class, 'edit'])
        ->name('purchase.address.edit');

    Route::post('/purchase/address/{item}', [PurchaseAddressController::class, 'update'])
        ->name('purchase.address.update');


});