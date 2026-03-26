<?php

namespace Tests\Feature\Purchase;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    // 購入画面を開いたとき、
    // 支払い方法の選択肢が表示されることを確認するテスト
    public function test_payment_methods_are_displayed_on_purchase_page(): void
    {
        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/buyer.jpg',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'zip' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => 'セラービル',
            'profile_image_path' => 'profiles/seller.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        PaymentMethod::create([
            'label' => 'カード支払い',
        ]);

        PaymentMethod::create([
            'label' => 'コンビニ払い',
        ]);

        $response = $this->actingAs($buyer)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('カード支払い');
        $response->assertSee('コンビニ払い');
    }

    // 支払い方法を選択して再読み込みしたとき、
    // 小計画面に選択した支払い方法が反映されることを確認するテスト
    public function test_selected_payment_method_is_reflected_after_reload(): void
    {
        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/buyer.jpg',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'zip' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => 'セラービル',
            'profile_image_path' => 'profiles/seller.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        $paymentMethod = PaymentMethod::create([
            'label' => 'カード支払い',
        ]);

        // 支払い方法選択フォーム送信
        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}/payment-method", [
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertRedirect("/purchase/{$item->id}");

        // 再読み込み後の購入画面で反映確認
        $followResponse = $this->actingAs($buyer)->get("/purchase/{$item->id}");

        $followResponse->assertStatus(200);
        $followResponse->assertSee('カード支払い');
    }

    // 支払い方法を未選択で購入したとき、
    // バリデーションエラーが表示されることを確認するテスト
    public function test_error_message_is_displayed_when_purchase_is_attempted_without_payment_method(): void
    {
        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/buyer.jpg',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'zip' => '111-1111',
            'address' => '東京都新宿区1-1-1',
            'building' => 'セラービル',
            'profile_image_path' => 'profiles/seller.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        $response = $this->actingAs($buyer)
            ->from("/purchase/{$item->id}")
            ->post("/purchase/{$item->id}", [
                'payment_method_id' => '',
            ]);

        $response->assertRedirect("/purchase/{$item->id}");
        $response->assertSessionHasErrors([
            'payment_method_id' => '支払い方法を選択してください。',
        ]);

        $followResponse = $this->actingAs($buyer)->get("/purchase/{$item->id}");

        $followResponse->assertStatus(200);
        $followResponse->assertSee('支払い方法を選択してください。');
    }
}