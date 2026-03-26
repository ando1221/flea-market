<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    // 出品画面を表示する
    public function create()
    {
        $categories = Category::orderBy('id')->get();
        $conditions = Condition::orderBy('id')->get();

        return view('sell.create', compact('categories', 'conditions'));
    }

    // 商品を出品する
    public function store(ExhibitionRequest $request)
    {
        // 商品情報とカテゴリ紐付けをまとめて保存する
        DB::transaction(function () use ($request) {
            $path = $request->file('image')->store('products', 'public');

            $item = Item::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'brand_name' => $request->brand_name ?: null,
                'description' => $request->description,
                'price' => $request->price,
                'condition_id' => $request->condition_id,
                'status' => 'on_sale',
                'image_path' => $path,
            ]);

            $item->categories()->sync($request->category_ids);
        });

        return redirect()
            ->route('mypage.index')
            ->with('success', '商品を出品しました。');
    }
}