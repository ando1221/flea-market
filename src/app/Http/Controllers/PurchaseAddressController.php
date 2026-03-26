<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class PurchaseAddressController extends Controller
{
    // 認証・メール認証・プロフィール設定済みユーザーのみ許可
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profile.set']);
    }

    // 配送先変更画面を表示
    public function edit(Item $item)
    {
        $user = auth()->user();

        $address = session("purchase_address.{$item->id}", [
            'zip' => $user->zip,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        return view('purchase.address', [
            'item' => $item,
            'address' => $address,
        ]);
    }

    // 配送先をセッションに保存
    public function update(AddressRequest $request, Item $item)
    {
        $validated = $request->validated();

        session([
            "purchase_address.{$item->id}" => [
                'zip' => $validated['zip'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? '',
            ],
        ]);

        return redirect()
            ->route('purchase.show', $item)
            ->with('success', '配送先を更新しました');
    }
}