<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // 商品をお気に入りに追加
    public function store(Item $item)
    {
        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
        ]);

        return back();
    }

    // 商品のお気に入りを解除
    public function destroy(Item $item)
    {
        Favorite::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->delete();

        return back();
    }
}