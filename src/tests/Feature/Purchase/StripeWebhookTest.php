<?php

namespace Tests\Feature\Purchase;

use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    // checkout.session.completed を受けたとき購入情報が保存され、
    // 購入した商品がマイページの購入一覧に表示されることを確認するテスト
    public function test_checkout_session_completed_creates_purchase_marks_item_sold_and_shows_in_mypage_buy_list(): void
    {
        $webhookSecret = 'whsec_test_secret';
        config()->set('services.stripe.webhook_secret', $webhookSecret);

        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '購入ユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $seller->id,
            'status' => 'on_sale',
            'name' => 'Webhook購入商品',
        ]);

        $paymentMethod = PaymentMethod::create([
            'label' => 'カード支払い',
        ]);

        $payload = json_encode([
            'id' => 'evt_test_checkout_completed',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'object' => 'checkout.session',
                    'metadata' => [
                        'item_id' => (string) $item->id,
                        'buyer_id' => (string) $buyer->id,
                        'payment_method_id' => (string) $paymentMethod->id,
                        'shipping_zip' => '333-3333',
                        'shipping_address' => '配送先住所',
                        'shipping_building' => '配送先建物',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE);

        // Stripe署名ヘッダを自前生成
        $timestamp = time();
        $signedPayload = $timestamp . '.' . $payload;
        $signature = hash_hmac('sha256', $signedPayload, $webhookSecret);
        $header = "t={$timestamp},v1={$signature}";

        $response = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            ['HTTP_Stripe-Signature' => $header],
            $payload
        );

        $response->assertOk();

        // 購入情報が保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method_id' => $paymentMethod->id,
            'shipping_zip' => '333-3333',
            'shipping_address' => '配送先住所',
            'shipping_building' => '配送先建物',
        ]);

        // 商品が sold になっていることを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);

        // 購入した商品がマイページ購入一覧に表示されることを確認
        $mypageResponse = $this->actingAs($buyer)->get('/mypage?tab=buy');

        $mypageResponse->assertStatus(200);
        $mypageResponse->assertSee('Webhook購入商品');
    }

    // 不正な署名のWebhookは拒否されることを確認するテスト
    public function test_invalid_signature_webhook_is_rejected(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

        $payload = json_encode([
            'id' => 'evt_invalid',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_invalid',
                    'metadata' => [],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $response = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            ['HTTP_Stripe-Signature' => 'invalid-signature'],
            $payload
        );

        $response->assertStatus(400);
    }
}