<?php

namespace Tests\Feature\Purchase;

use App\Http\Controllers\PurchaseController;
use App\Models\Condition;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    // 購入ボタン押下時にStripe Checkoutへリダイレクトすることを確認するテスト
    public function test_user_is_redirected_to_stripe_checkout(): void
    {
        config()->set('services.stripe.secret', 'sk_test_dummy');

        $condition = Condition::factory()->create();

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
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
            'price' => 3000,
        ]);

        $paymentMethod = PaymentMethod::create([
            'label' => 'カード支払い',
        ]);

        $this->withSession([
            "purchase_payment_method.{$item->id}" => $paymentMethod->id,
            "purchase_address.{$item->id}" => [
                'zip' => '123-4567',
                'address' => '東京都渋谷区1-1-1',
                'building' => 'テストビル',
            ],
        ]);

        $mock = Mockery::mock(PurchaseController::class)->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('createCheckoutSession')
            ->once()
            ->andReturn((object) [
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_dummy',
            ]);

        $this->app->instance(PurchaseController::class, $mock);

        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}");

        $response->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_dummy');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}