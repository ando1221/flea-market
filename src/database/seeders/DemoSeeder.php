<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // デモユーザーを作成
            $seller = User::create([
                'name' => '出品ユーザー',
                'email' => 'seller@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'zip' => '100-0001',
                'address' => '東京都千代田区1-1-1',
                'building' => 'セラービル',
                'profile_image_path' => 'profiles/seller.png',
            ]);

            $buyer = User::create([
                'name' => '購入ユーザー',
                'email' => 'buyer@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'zip' => '150-0001',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'バイヤービル',
                'profile_image_path' => 'profiles/buyer.png',
            ]);

            $viewer = User::create([
                'name' => '閲覧ユーザー',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'zip' => '060-0001',
                'address' => '北海道札幌市中央区1-1-1',
                'building' => 'ビューワービル',
                'profile_image_path' => 'profiles/viewer.jpg',
            ]);

            // 初期設定済みの状態データを取得
            $conditionMap = Condition::pluck('id', 'label');
            if ($conditionMap->isEmpty()) {
                throw new \RuntimeException('conditions テーブルが空です。ConditionSeeder を先に実行してください。');
            }

            // 初期設定済みのカテゴリデータを取得
            $categories = Category::get()->keyBy('name');
            if ($categories->isEmpty()) {
                throw new \RuntimeException('categories テーブルが空です。CategorySeeder を先に実行してください。');
            }

            // 初期設定済みの支払い方法を取得
            $paymentMethods = PaymentMethod::pluck('id', 'label');
            if ($paymentMethods->isEmpty()) {
                throw new \RuntimeException('payment_methods テーブルが空です。PaymentMethodSeeder を先に実行してください。');
            }

            // デモ商品データを作成
            $itemsData = [
                ['name' => '腕時計', 'price' => '15,000', 'brand' => 'Rolax', 'desc' => 'スタイリッシュなデザインのメンズ腕時計', 'img' => '腕時計.jpg', 'condition' => '良好'],
                ['name' => 'HDD', 'price' => '5,000', 'brand' => '西芝', 'desc' => '高速で信頼性の高いハードディスク', 'img' => 'HDD.jpg', 'condition' => '目立った傷や汚れなし'],
                ['name' => '玉ねぎ3束', 'price' => '300', 'brand' => 'なし', 'desc' => '新鮮な玉ねぎ3束のセット', 'img' => '玉ねぎ3束.jpg', 'condition' => 'やや傷や汚れあり'],
                ['name' => '革靴', 'price' => '4,000', 'brand' => '', 'desc' => 'クラシックなデザインの革靴', 'img' => '革靴.jpg', 'condition' => '状態が悪い'],
                ['name' => 'ノートPC', 'price' => '45,000', 'brand' => '', 'desc' => '高性能なノートパソコン', 'img' => 'ノートPC.jpg', 'condition' => '良好'],
                ['name' => 'マイク', 'price' => '8,000', 'brand' => 'なし', 'desc' => '高音質のレコーディング用マイク', 'img' => 'マイク.jpg', 'condition' => '目立った傷や汚れなし'],
                ['name' => 'ショルダーバッグ', 'price' => '3,500', 'brand' => '', 'desc' => 'おしゃれなショルダーバッグ', 'img' => 'ショルダーバッグ.jpg', 'condition' => 'やや傷や汚れあり'],
                ['name' => 'タンブラー', 'price' => '500', 'brand' => 'なし', 'desc' => '使いやすいタンブラー', 'img' => 'タンブラー.jpg', 'condition' => '状態が悪い'],
                ['name' => 'コーヒーミル', 'price' => '4,000', 'brand' => 'Starbacks', 'desc' => '手動のコーヒーミル', 'img' => 'コーヒーミル.jpg', 'condition' => '良好'],
                ['name' => 'メイクセット', 'price' => '2,500', 'brand' => '', 'desc' => '便利なメイクアップセット', 'img' => 'メイクセット.jpg', 'condition' => '目立った傷や汚れなし'],
            ];

            $items = collect();

            foreach ($itemsData as $row) {
                // ブランド名の空文字や「なし」は null に変換
                $brand = trim($row['brand']);
                if ($brand === '' || $brand === 'なし') {
                    $brand = null;
                }

                // カンマ付き価格を整数に変換
                $price = (int) str_replace(',', '', $row['price']);

                // 商品状態ラベルから condition_id を取得
                $conditionId = $conditionMap[$row['condition']] ?? null;
                if (!$conditionId) {
                    throw new \RuntimeException('condition label が conditions に存在しません: ' . $row['condition']);
                }

                // 商品を作成
                $item = Item::firstOrCreate(
                    ['name' => $row['name']],
                    [
                        'user_id' => $seller->id,
                        'brand_name' => $brand,
                        'description' => $row['desc'],
                        'price' => $price,
                        'condition_id' => $conditionId,
                        'status' => 'on_sale',
                        'image_path' => 'products/' . $row['img'],
                    ]
                );

                $items->push($item);
            }

            // 商品にカテゴリを紐付け
            $categoryAssign = [
                '腕時計' => ['ファッション', 'メンズ', 'アクセサリー'],
                'HDD' => ['家電'],
                '玉ねぎ3束' => ['キッチン'],
                '革靴' => ['ファッション', 'メンズ'],
                'ノートPC' => ['家電'],
                'マイク' => ['家電'],
                'ショルダーバッグ' => ['ファッション', 'レディース'],
                'タンブラー' => ['キッチン'],
                'コーヒーミル' => ['キッチン'],
                'メイクセット' => ['コスメ', 'レディース'],
            ];

            foreach ($items as $item) {
                $names = $categoryAssign[$item->name] ?? ['ファッション'];
                $ids = collect($names)
                    ->map(fn($name) => $categories[$name]->id)
                    ->all();

                $item->categories()->sync($ids);
            }

            // お気に入りデータを作成
            $favoriteTargets = ['腕時計', 'ノートPC', 'マイク', 'コーヒーミル', 'ショルダーバッグ'];

            foreach ($items as $item) {
                if (in_array($item->name, $favoriteTargets, true)) {
                    Favorite::firstOrCreate([
                        'user_id' => $buyer->id,
                        'item_id' => $item->id,
                    ]);
                }
            }

            // コメントデータを作成
            $commentSeed = [
                '腕時計' => [
                    ['user_id' => $viewer->id, 'body' => 'かっこいいですね！'],
                    ['user_id' => $buyer->id, 'body' => '購入を検討しています。動作に問題はありませんか？'],
                    ['user_id' => $seller->id, 'body' => 'コメントありがとうございます。問題なく動作します。'],
                ],
                'ノートPC' => [
                    ['user_id' => $viewer->id, 'body' => 'スペックはどのくらいですか？'],
                    ['user_id' => $seller->id, 'body' => 'メモリ8GB、SSD256GBです。'],
                ],
                '玉ねぎ3束' => [
                    ['user_id' => $viewer->id, 'body' => '新鮮そう！'],
                ],
                'メイクセット' => [
                    ['user_id' => $buyer->id, 'body' => '色味が気になります。'],
                    ['user_id' => $seller->id, 'body' => 'ナチュラル系の使いやすいカラーです。'],
                ],
            ];

            foreach ($items as $item) {
                foreach ($commentSeed[$item->name] ?? [] as $commentData) {
                    Comment::firstOrCreate([
                        'user_id' => $commentData['user_id'],
                        'item_id' => $item->id,
                        'body' => $commentData['body'],
                    ]);
                }
            }

            // 購入データを作成
            $purchasePlans = [
                ['item_name' => '腕時計', 'payment_label' => 'カード支払い'],
                ['item_name' => 'マイク', 'payment_label' => 'コンビニ払い'],
            ];

            foreach ($purchasePlans as $plan) {
                $item = $items->firstWhere('name', $plan['item_name']);
                if (!$item) {
                    throw new \RuntimeException('Item が見つかりません: ' . $plan['item_name']);
                }

                $paymentMethodId = $paymentMethods[$plan['payment_label']] ?? null;
                if (!$paymentMethodId) {
                    throw new \RuntimeException('payment method が存在しません: ' . $plan['payment_label']);
                }

                // すでに購入済みならスキップ
                if (Purchase::where('item_id', $item->id)->exists()) {
                    continue;
                }

                Purchase::create([
                    'buyer_id' => $buyer->id,
                    'item_id' => $item->id,
                    'payment_method_id' => $paymentMethodId,
                    'shipping_zip' => $buyer->zip,
                    'shipping_address' => $buyer->address,
                    'shipping_building' => $buyer->building,
                    'purchased_at' => now(),
                ]);

                // 購入済み商品を sold に更新
                $item->update([
                    'status' => 'sold',
                ]);
            }
        });
    }
}
