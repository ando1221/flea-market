<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    // マイページに認証・メール認証・プロフィール設定確認を適用
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profile.set']);
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'sell');
        $userId = $request->user()->id;

        // 購入した商品一覧を表示する場合
        if ($tab === 'buy') {
            $items = Item::query()
                ->join('purchases', 'items.id', '=', 'purchases.item_id')
                ->where('purchases.buyer_id', $userId)
                ->withCount(['favorites', 'comments'])
                ->select('items.*')
                ->orderByDesc('purchases.purchased_at')
                ->get();
        } else {
            $tab = 'sell';

            // 出品した商品一覧を表示する場合
            $items = Item::query()
                ->where('user_id', $userId)
                ->withCount(['favorites', 'comments'])
                ->latest()
                ->get();
        }

        return view('mypage.index', compact('items', 'tab'));
    }
}