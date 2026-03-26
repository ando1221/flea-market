<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // recommend | mylist
        $keyword = $request->query('keyword');

        $query = Item::query()
            ->with(['categories'])
            ->withCount(['favorites', 'comments'])
            ->latest();

        // ログイン中は自分が出品した商品を除外
        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }

        // 商品名でキーワード検索する場合
        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // マイリストタブが選択された場合
        if ($tab === 'mylist') {
            // 未ログインならおすすめ一覧へ戻す
            if (!auth()->check()) {
                return redirect()->route('items.index', ['tab' => 'recommend']);
            }

            // ログインユーザーがお気に入りした商品に絞り込む
            $query->whereHas('favorites', function ($q) {
                $q->where('user_id', auth()->id());
            });
        } else {
            // それ以外はおすすめタブとして扱う
            $tab = 'recommend';
        }

        $items = $query->get();

        return view('items.index', compact('items', 'tab'));
    }

    public function show(Item $item)
    {
        $item->load([
            'categories',
            'condition',
            'comments.user',
        ])->loadCount(['favorites', 'comments']);

        // ログイン中ならお気に入り済みか判定
        $isFavorited = auth()->check()
            ? $item->favorites()->where('user_id', auth()->id())->exists()
            : false;

        return view('items.show', compact('item', 'isFavorited'));
    }
}