<?php

namespace Tests\Feature\Item;

use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    // 検索キーワードを入力したとき、
    // 商品名で部分一致検索できることを確認するテスト
    public function test_items_can_be_searched_by_partial_name(): void
    {
        $condition = Condition::factory()->create();

        Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '黒い財布',
        ]);

        Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '白いシャツ',
        ]);

        $response = $this->get('/?keyword=財布');

        $response->assertStatus(200);
        $response->assertSee('黒い財布');
        $response->assertDontSee('白いシャツ');
    }

    // ホーム画面で検索したあとマイリストへ移動したとき、
    // 検索キーワードが保持されたまま絞り込みできることを確認するテスト
    public function test_search_keyword_is_kept_in_mylist(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $likedItem = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '黒い財布',
        ]);

        $otherLikedItem = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '白いシャツ',
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $otherLikedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=財布');

        $response->assertStatus(200);
        $response->assertSee('value="財布"', false);
        $response->assertSee('黒い財布');
        $response->assertDontSee('白いシャツ');
    }
}