<?php

namespace Tests\Feature\Item;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    // 商品一覧ページを開いたとき、
    // すべての商品が表示されることを確認するテスト
    public function test_all_items_are_displayed(): void
    {
        $condition = Condition::factory()->create();

        Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '腕時計',
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '財布',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('財布');
    }

    // 購入済み商品のとき、
    // 商品一覧に Sold ラベルが表示されることを確認するテスト
    public function test_sold_label_is_displayed_for_purchased_items(): void
    {
        $condition = Condition::factory()->create();

        Item::factory()->sold()->create([
            'condition_id' => $condition->id,
            'name' => '購入済み商品',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    // ログイン中のユーザーが商品一覧ページを開いたとき、
    // 自分が出品した商品は表示されないことを確認するテスト
    public function test_own_items_are_not_displayed(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '他人の商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}