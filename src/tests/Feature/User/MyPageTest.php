<?php

namespace Tests\Feature\User;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyPageTest extends TestCase
{
    use RefreshDatabase;

    // ログイン済みユーザーがマイページを開いたとき、
    // プロフィール画像・ユーザー名・出品した商品一覧が表示されることを確認するテスト
    public function test_user_profile_and_sell_items_are_displayed(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'テストユーザー',
            'profile_image_path' => 'profiles/test.jpg',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $user->id,
            'name' => '出品商品1',
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $user->id,
            'name' => '出品商品2',
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=sell');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('出品商品1');
        $response->assertSee('出品商品2');
    }

    // ログイン済みユーザーが購入した商品一覧を開いたとき、
    // 購入した商品が表示されることを確認するテスト
    public function test_bought_items_are_displayed(): void
    {
        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'profile_image_path' => 'profiles/buyer.jpg',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'profile_image_path' => 'profiles/seller.jpg',
            'zip' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => 'セラービル',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'name' => '購入済み商品',
            'status' => 'sold',
        ]);

        $paymentMethod = PaymentMethod::create([
            'label' => 'カード支払い',
        ]);

        Purchase::create([
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method_id' => $paymentMethod->id,
            'shipping_zip' => $buyer->zip,
            'shipping_address' => $buyer->address,
            'shipping_building' => $buyer->building,
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->get('/mypage?tab=buy');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
    }
}