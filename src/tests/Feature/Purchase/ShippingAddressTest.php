<?php

namespace Tests\Feature\Purchase;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    // 配送先住所変更画面で登録した住所が、
    // 商品購入画面に正しく反映されることを確認するテスト
    public function test_changed_shipping_address_is_reflected_on_purchase_page(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'zip' => '111-1111',
            'address' => '元の住所',
            'building' => '元の建物',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'zip' => '222-2222',
            'address' => '出品者住所',
            'building' => 'セラービル',
            'profile_image_path' => 'profiles/seller.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'zip' => '333-3333',
            'address' => '変更後住所',
            'building' => '変更後建物',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('333-3333');
        $response->assertSee('変更後住所');
        $response->assertSee('変更後建物');
    }
}